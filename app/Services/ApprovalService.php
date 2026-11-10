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

        // 1. Validasi Status Pengajuan (kompatibel dengan semua variasi penamaan & level berjenjang)
        $allowedQueueStatuses = [
            'Menunggu Persetujuan Atasan',
            'Menunggu Approval Atasan',
            'Menunggu Atasan',
            'Menunggu Persetujuan Kabid',
            'Menunggu Persetujuan Kadis',
            'Menunggu Persetujuan',
            'Draft',
            'draft',
        ];

        if (! in_array($currentStatus, $allowedQueueStatuses)) {
            throw new Exception("Pengajuan ini tidak sedang dalam antrean persetujuan (Status saat ini: {$currentStatus}).");
        }

        // 2. Validasi Hak Akses (Hanya Atasan Langsung, Pejabat Manajerial, atau Super Admin)
        $isSupervisor = $travelRequest->requester && $travelRequest->requester->direct_supervisor_id === $approver->id;
        $isSuperAdmin = ($approverUser->employee?->department?->code === 'SUPER_ADMIN');
        $posLower = strtolower($approver->position ?? '');
        $isManagerOrLeader = in_array($approver->grade, ['Manager', 'Director', 'Supervisor'])
            || str_contains($posLower, 'kepala')
            || str_contains($posLower, 'kadis')
            || str_contains($posLower, 'kabid')
            || str_contains($posLower, 'kasi')
            || str_contains($posLower, 'atasan');

        if (! $isSupervisor && ! $isSuperAdmin && ! $isManagerOrLeader) {
            throw new Exception('Akses ditolak. Hanya Atasan Langsung atau pejabat berwenang yang dapat memproses tahap ini.');
        }

        // Ambil catatan / alasan penolakan dari notes atau rejection_reason
        $notes = $data['notes'] ?? $data['rejection_reason'] ?? null;

        // 3. Tentukan Status Berikutnya Sesuai Alur Berjenjang (Atasan -> Kabid -> Kadis -> SPPD)
        if ($data['decision'] === 'Disetujui') {
            $roleLabel = app(PermissionMatrixService::class)->resolveUserRoleLabel($approverUser);

            if (! empty($data['final_approval']) || $isSuperAdmin || $roleLabel === 'Super Admin') {
                $nextStatus = 'Disetujui';
            } elseif ($currentStatus === 'Menunggu Persetujuan Kadis' || $roleLabel === 'Kadis') {
                $nextStatus = 'Disetujui';
            } elseif ($currentStatus === 'Menunggu Persetujuan Kabid') {
                $nextStatus = 'Menunggu Persetujuan Kadis';
            } elseif (in_array($currentStatus, ['Menunggu Persetujuan Atasan', 'Menunggu Approval Atasan', 'Menunggu Atasan', 'Menunggu Persetujuan', 'Draft', 'draft'])) {
                if ($roleLabel === 'Kabid') {
                    $nextStatus = 'Menunggu Persetujuan Kadis';
                } else {
                    $nextStatus = 'Menunggu Persetujuan Kabid';
                }
            } else {
                $nextStatus = 'Disetujui';
            }
            $rejectionReason = null;
        } elseif ($data['decision'] === 'Ditolak') {
            $nextStatus = 'Ditolak';
            $rejectionReason = $notes ?? 'Pengajuan ditolak oleh pejabat berwenang.';
        } else {
            // Gunakan 'Direvisi' agar sinkron dengan Dashboard Atasan, Seeder, & Modul Karyawan
            $nextStatus = 'Direvisi';
            $rejectionReason = $notes ?? 'Pengajuan perlu direvisi sesuai arahan Atasan/Pejabat.';
        }

        DB::beginTransaction();
        try {
            // 4. Catat Riwayat Persetujuan ke tabel approvals
            Approval::create([
                'travel_request_id' => $travelRequest->id,
                'approver_employee_id' => $approver->id,
                'status' => $data['decision'],
                'notes' => $notes,
                'action_at' => now(),
            ]);

            // 5. Generate SPPD Digital Otomatis jika Disetujui
            $sppdNumber = $travelRequest->sppd_number;
            if ($nextStatus === 'Disetujui' && ! $sppdNumber) {
                $sppdNumber = 'SPPD/'.date('Y/m').'/'.strtoupper(substr($travelRequest->id, 0, 4));
            }

            // 6. Update Status & Alasan Penolakan di Tabel Utama
            $travelRequest->update([
                'status' => $nextStatus,
                'sppd_number' => $sppdNumber,
                'rejection_reason' => $rejectionReason,
            ]);

            // 7. Jejak Audit
            AuditLog::create([
                'user_id' => $approverUser->id,
                'travel_request_id' => $travelRequest->id,
                'action' => 'APPROVAL_'.strtoupper(str_replace(' ', '_', $data['decision'])),
                'module' => 'APPROVAL',
                'description' => "Diproses oleh {$approver->name} ({$approver->position}). Keputusan: {$data['decision']}. Status menjadi: {$nextStatus}",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            DB::commit();

            return $travelRequest;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
