<?php
namespace App\Services;

use App\Models\TravelRequest;
use App\Models\Approval;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Exception;

class ApprovalService
{
    public function processApproval($travelRequestId, $approverUser, array $data, $ip, $userAgent)
    {
        $travelRequest = TravelRequest::with('requester')->findOrFail($travelRequestId);
        $approver = $approverUser->employee;
        $currentStatus = $travelRequest->status;
        $nextStatus = '';

        // 1. Validasi Status Pengajuan
        // Mengakomodasi "Menunggu Atasan" (dari input Bruno) atau "Menunggu Persetujuan" (dari default DB)
        if (!in_array($currentStatus, ['Menunggu Atasan', 'Menunggu Persetujuan', 'draft', 'Draft'])) {
            throw new Exception("Pengajuan ini tidak sedang dalam antrean persetujuan.");
        }
        // 2. Validasi Hak Akses (Hanya Atasan Langsung)
        if ($travelRequest->requester->direct_supervisor_id !== $approver->id) {
            throw new Exception("Akses ditolak. Hanya Atasan Langsung yang dapat memproses tahap ini.");
        }

        // 3. Tentukan Keputusan (Sesuaikan dengan Form Request)
        if ($data['decision'] === 'Disetujui') {
            $nextStatus = 'Disetujui';
        } elseif ($data['decision'] === 'Ditolak') {
            $nextStatus = 'Ditolak';
        } else {
            $nextStatus = 'Perlu Revisi'; // <-- Sesuaikan dengan 'in:...,Perlu Revisi'
        }

        DB::beginTransaction();
        try {
            // 4. Catat Riwayat Persetujuan
            Approval::create([
                'travel_request_id' => $travelRequest->id,
                'approver_employee_id' => $approver->id,
                'status' => $data['decision'],
                'notes' => $data['notes'] ?? null,
                'action_at' => now(),
            ]);

            // 5. Generate SPPD Digital Otomatis jika Disetujui
            $sppdNumber = $travelRequest->sppd_number;
            if ($nextStatus === 'Disetujui' && !$sppdNumber) {
                // Format SPPD menggunakan 4 karakter pertama UUID agar unik namun rapi
                $sppdNumber = 'SPPD/' . date('Y/m') . '/' . strtoupper(substr($travelRequest->id, 0, 4));
            }

            // 6. Update Status Pengajuan
            $travelRequest->update([
                'status' => $nextStatus,
                'sppd_number' => $sppdNumber
            ]);

            // 7. Jejak Audit
            AuditLog::create([
                'user_id' => $approverUser->id,
                'travel_request_id' => $travelRequest->id,
                'action' => 'APPROVAL_' . strtoupper($data['decision']),
                'description' => "Diproses oleh {$approver->name} ({$approver->position}). Keputusan: {$data['decision']}. Lanjut ke tahap: {$nextStatus}",
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