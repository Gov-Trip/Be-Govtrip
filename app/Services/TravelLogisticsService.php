<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\TravelLogistics;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TravelLogisticsService
{
    // Kalkulasi Biaya Terstandarisasi dari Master Data SBM (TravelPolicy)
    public function calculateStandardizedBudget(TravelRequest $travelRequest): array
    {
        $travelRequest->loadMissing(['assignees.employee', 'destinationCity']);

        $days = Carbon::parse($travelRequest->start_date)
            ->diffInDays(Carbon::parse($travelRequest->end_date)) + 1;
        $nights = max(0, $days - 1);

        $breakdown = [];
        $grandTotal = 0;

        foreach ($travelRequest->assignees as $assignee) {
            $emp = $assignee->employee;
            if (! $emp) {
                continue;
            }

            $policy = TravelPolicy::where('destination_city_id', $travelRequest->destination_city_id)
                ->where('grade_group', $emp->grade)
                ->first();

            $dailyAllowance = $policy ? (float) $policy->daily_allowance * $days : 0;
            $hotelRate = $policy ? (float) $policy->max_hotel_rate * $nights : 0;
            $transportRate = $policy ? (float) $policy->max_transport_rate : 0;
            $subtotal = $dailyAllowance + $hotelRate + $transportRate;

            $grandTotal += $subtotal;

            $breakdown[] = [
                'employee_id'        => $emp->id,
                'name'               => $emp->name,
                'grade'              => $emp->grade,
                'is_leader'          => (bool) $assignee->is_leader,
                'days'               => $days,
                'nights'             => $nights,
                'sbm_daily_total'    => $dailyAllowance,
                'sbm_hotel_total'    => $hotelRate,
                'sbm_transport_rate' => $transportRate,
                'subtotal_standardized' => $subtotal,
            ];
        }

        return [
            'days'               => $days,
            'nights'             => $nights,
            'assignee_count'     => count($breakdown),
            'standardized_total' => $grandTotal,
            'breakdown'          => $breakdown,
        ];
    }

    // 1. Input Logistik & Kunci Anggaran Standar SBM
public function inputLogistics($travelRequestId, $adminUser, array $data, $ipAddress, $userAgent): TravelRequest
    {
        $travelRequest = TravelRequest::with(['assignees.employee', 'requester'])->findOrFail($travelRequestId);

        if ($travelRequest->status !== 'Disetujui') {
            throw new Exception("Logistik hanya bisa diinput untuk pengajuan yang berstatus 'Disetujui'. Status saat ini: {$travelRequest->status}");
        }

        // Hitung anggaran terstandarisasi otomatis dari Master SBM
        $budgetData = $this->calculateStandardizedBudget($travelRequest);
        
        // Evaluasi finalCost: Pakai inputan manual Admin Travel JIKA ADA, jika tidak pakai hitungan SBM
        if (isset($data['estimated_total_cost']) && $data['estimated_total_cost'] > 0) {
            $finalCost = (float) $data['estimated_total_cost'];
        } else {
            $finalCost = $budgetData['standardized_total'] > 0
                ? $budgetData['standardized_total']
                : (float) $travelRequest->estimated_total_cost;
        }

        DB::beginTransaction();
        try {
            // 1. Simpan atau perbarui data tiket/hotel
            TravelLogistics::updateOrCreate(
                ['travel_request_id' => $travelRequest->id],
                [
                    'admin_travel_id'        => $adminUser->employee->id,
                    'transport_type'         => $data['transport_type'],
                    'booking_code_or_ticket' => $data['booking_code_or_ticket'],
                    'hotel_name'             => $data['hotel_name'] ?? null,
                    'departure_datetime'     => $data['departure_datetime'],
                    'return_datetime'        => $data['return_datetime'],
                ]
            );

            // Pastikan nomor SPPD sudah ada (fallback)
            $sppdNumber = $travelRequest->sppd_number;
            if (! $sppdNumber) {
                $sppdNumber = '090/SPPD-' . strtoupper(substr($travelRequest->id, 0, 4)) . '/' . now()->format('m/Y');
            }

            // 2. Kunci budget final ke advance_payment & ubah status ke Siap Berangkat
            $travelRequest->update([
                'sppd_number'          => $sppdNumber,
                'estimated_total_cost' => $finalCost,
                'advance_payment'      => $finalCost,
                'status'               => 'Siap Berangkat',
                'rejection_reason'     => null,
            ]);

            // 3. Jejak Audit
            $formattedBudget = number_format($finalCost, 0, ',', '.');
            AuditLog::create([
                'travel_request_id' => $travelRequest->id,
                'user_id'           => $adminUser->id,
                'action'            => 'INPUT_LOGISTICS',
                'module'            => 'ADMIN_TRAVEL',
                'description'       => "Logistik {$data['transport_type']} (Kode: {$data['booking_code_or_ticket']}) diinput. Anggaran dikunci: Rp {$formattedBudget}.",
                'ip_address'        => $ipAddress,
                'user_agent'        => $userAgent,
            ]);

            DB::commit();

            return $travelRequest->fresh(['logistics', 'requester', 'originCity', 'destinationCity']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // 2. Tolak / Kembalikan dari Admin Travel jika kendala akomodasi/kuota
    public function rejectLogistics($travelRequestId, $adminUser, string $reason, $ipAddress, $userAgent): TravelRequest
    {
        $travelRequest = TravelRequest::with('requester')->findOrFail($travelRequestId);

        if ($travelRequest->status !== 'Disetujui') {
            throw new Exception("Hanya pengajuan berstatus 'Disetujui' yang dapat ditolak pada tahap logistik.");
        }

        DB::beginTransaction();
        try {
            $travelRequest->update([
                'status'           => 'Direvisi',
                'rejection_reason' => $reason,
            ]);

            AuditLog::create([
                'travel_request_id' => $travelRequest->id,
                'user_id'           => $adminUser->id,
                'action'            => 'LOGISTICS_REJECTED',
                'module'            => 'ADMIN_TRAVEL',
                'description'       => "Admin Travel mengembalikan pengajuan. Alasan: {$reason}",
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