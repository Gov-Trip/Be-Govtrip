<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\TravelRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function processApproval($travelRequestId, $approverUser, array $data, $ip, $userAgent)
    {
        $travelRequest = TravelRequest::with('requester')->findOrFail($travelRequestId);
        $approver = $approverUser->employee;

        if (! $approver) {
            throw new Exception('Data profil pegawai Atasan tidak ditemukan.');
        }

        $currentStatus = $travelRequest->status;

        // 1. Validasi Status Pengajuan (kompatibel dengan semua variasi penamaan)
        $allowedQueueStatuses = [
            'Menunggu Persetujuan Atasan',
            'Menunggu Approval Atasan',
            'Menunggu Atasan',
            'Menunggu Persetujuan',
            'Draft',
            'draft',
        ];

        if (! in_array($currentStatus, $allowedQueueStatuses)) {
            throw new Exception("Pengajuan ini tidak sedang dalam antrean persetujuan (Status saat ini: {$currentStatus}).");
        }

        // 2. Validasi Hak Akses (Hanya Atasan Langsung atau satu departemen)
        if ($travelRequest->requester->direct_supervisor_id !== $approver->id) {
            throw new Exception('Akses ditolak. Hanya Atasan Langsung yang dapat memproses tahap ini.');
        }

        // Ambil catatan / alasan penolakan dari notes atau rejection_reason
        $notes = $data['notes'] ?? $data['rejection_reason'] ?? null;

        // 3. Tentukan Status Berikutnya
        if ($data['decision'] === 'Disetujui') {
            $nextStatus = 'Disetujui';
            $rejectionReason = null;
        } elseif ($data['decision'] === 'Ditolak') {
            $nextStatus = 'Ditolak';
            $rejectionReason = $notes ?? 'Pengajuan ditolak oleh Atasan.';
        } else {
            // Gunakan 'Direvisi' agar sinkron dengan Dashboard Atasan, Seeder, & Modul Karyawan
            $nextStatus = 'Direvisi';
            $rejectionReason = $notes ?? 'Pengajuan perlu direvisi sesuai arahan Atasan.';
        }

        DB::beginTransaction();
        try {
            // 4. Catat Riwayat Persetujuan ke tabel approvals
            Approval::create([
                'travel_request_id'    => $travelRequest->id,
                'approver_employee_id' => $approver->id,
                'status'               => $data['decision'],
                'notes'                => $notes,
                'action_at'            => now(),
            ]);

            // 5. Generate SPPD Digital Otomatis jika Disetujui
            $sppdNumber = $travelRequest->sppd_number;
            if ($nextStatus === 'Disetujui' && ! $sppdNumber) {
                $sppdNumber = 'SPPD/' . date('Y/m') . '/' . strtoupper(substr($travelRequest->id, 0, 4));
            }

            // 6. Update Status & Alasan Penolakan di Tabel Utama
            $travelRequest->update([
                'status'           => $nextStatus,
                'sppd_number'      => $sppdNumber,
                'rejection_reason' => $rejectionReason,
            ]);

            // 7. Jejak Audit
            AuditLog::create([
                'user_id'           => $approverUser->id,
                'travel_request_id' => $travelRequest->id,
                'action'            => 'APPROVAL_' . strtoupper(str_replace(' ', '_', $data['decision'])),
                'module'            => 'APPROVAL',
                'description'       => "Diproses oleh {$approver->name} ({$approver->position}). Keputusan: {$data['decision']}. Status menjadi: {$nextStatus}",
                'ip_address'        => $ip,
                'user_agent'        => $userAgent,
            ]);

            DB::commit();

            return $travelRequest;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}