<?php

namespace App\Services;

use App\Models\TravelRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Exception;

class FinanceService
{
    public function verifyExpense($travelRequestId, $financeUser, array $data, $ipAddress, $userAgent)
    {
        $travelRequest = TravelRequest::findOrFail($travelRequestId);

        // 1. Validasi Status (Nangkep operan dari Admin Travel yang statusnya "Siap Berangkat")
        if ($travelRequest->status !== 'Siap Berangkat') {
            throw new Exception("Akses ditolak. Tiket dan logistik belum diproses oleh Admin Travel.");
        }

        // 2. Tentukan Keputusan
        $nextStatus = $data['is_valid'] ? 'Selesai' : 'Revisi Admin Travel';

        DB::beginTransaction();
        try {
            // 3. Update Dana Cair (Advance Payment) & Status Utama
            $travelRequest->update([
                'advance_payment' => $data['is_valid'] ? $data['advance_payment'] : 0,
                'status'          => $nextStatus
            ]);

            // 4. Catat Jejak Audit Pencairan
            AuditLog::create([
                'user_id'           => $financeUser->id,
                'travel_request_id' => $travelRequest->id,
                'action'            => 'FINANCE_APPROVAL',
                'description'       => "Pencairan Uang Muka: Rp " . number_format($data['advance_payment'], 0, ',', '.') . ". Status: {$nextStatus}. Catatan: " . ($data['notes'] ?? '-'),
                'ip_address'        => $ipAddress,
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