<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ExpenseReport;
use App\Models\TravelRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public function verifyExpense($travelRequestId, $financeUser, array $data, $ipAddress, $userAgent): TravelRequest
    {
        $travelRequest = TravelRequest::with(['requester', 'expenseReport'])->findOrFail($travelRequestId);

        // 1. Validasi Status (Tangkap operan dari Admin Travel atau Seeder)
        $allowedStatuses = ['Siap Berangkat', 'Menunggu Verifikasi LPJ'];
        if (! in_array($travelRequest->status, $allowedStatuses)) {
            throw new Exception("Akses ditolak. Berkas belum siap diverifikasi Finance (Status saat ini: {$travelRequest->status}).");
        }

        $isValid = (bool) $data['is_valid'];
        $notes = $data['notes'] ?? $data['rejection_reason'] ?? null;

        // Gunakan nominal dari Finance jika diisi, atau pakai nominal standar SBM yang sudah dikunci Admin Travel
        $approvedAdvance = isset($data['advance_payment'])
            ? (float) $data['advance_payment']
            : (float) ($travelRequest->advance_payment > 0 ? $travelRequest->advance_payment : $travelRequest->estimated_total_cost);

        $nextStatus = $isValid ? 'Selesai' : 'Revisi Admin Travel';

        DB::beginTransaction();
        try {
            // 2. Update Dana Cair (Advance Payment), Status Utama, & Alasan Revisi
            $travelRequest->update([
                'advance_payment'  => $isValid ? $approvedAdvance : $travelRequest->advance_payment,
                'status'           => $nextStatus,
                'rejection_reason' => $isValid ? null : ($notes ?? 'Perlu perbaikan rincian logistik/administrasi oleh Admin Travel.'),
            ]);

            // 3. Sinkronkan ke tabel expense_reports agar PDF Audit Final memiliki rekap yang valid
            ExpenseReport::updateOrCreate(
                ['travel_request_id' => $travelRequest->id],
                [
                    'employee_id'        => $travelRequest->requester_employee_id,
                    'total_advance'      => $isValid ? $approvedAdvance : 0,
                    'total_real_expense' => $travelRequest->expenseReport->total_real_expense ?? ($isValid ? $approvedAdvance : 0),
                    'balance_amount'     => $travelRequest->expenseReport->balance_amount ?? 0,
                    'activity_summary'   => $travelRequest->expenseReport->activity_summary ?? $travelRequest->purpose,
                    'status'             => $isValid ? 'VERIFIED' : 'REJECTED',
                    'finance_notes'      => $notes,
                ]
            );

            // 4. Catat Jejak Audit Pencairan / Penolakan
            $formattedAmount = number_format($approvedAdvance, 0, ',', '.');
            AuditLog::create([
                'user_id'           => $financeUser->id,
                'travel_request_id' => $travelRequest->id,
                'action'            => $isValid ? 'FINANCE_APPROVAL' : 'FINANCE_REJECTED',
                'module'            => 'FINANCE',
                'description'       => $isValid
                    ? "Verifikasi Finance disetujui. Pencairan Dana: Rp {$formattedAmount}. Status: {$nextStatus}. Catatan: " . ($notes ?? '-')
                    : "Dikembalikan oleh Finance ke Admin Travel. Alasan: " . ($notes ?? '-'),
                'ip_address'        => $ipAddress,
                'user_agent'        => $userAgent,
            ]);

            DB::commit();

            return $travelRequest->fresh(['requester', 'originCity', 'destinationCity', 'logistics', 'expenseReport']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}