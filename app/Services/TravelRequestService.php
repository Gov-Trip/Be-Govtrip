<?php
namespace App\Services;

use App\Models\TravelRequest;
use App\Models\TravelPolicy;
use App\Models\TravelAssignee;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class TravelRequestService
{
    // 1. Fungsi Buat Draft
    public function createTravelRequest(User $user, array $data, $ipAddress, $userAgent): TravelRequest
    {
        $employee = $user->employee;
        $days = Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1;

        // Note: Kalkulasi SBM kita biarkan dulu sesuai ERD. Kalau nanti fix pakai external API, kita ubah logic ini.
        $policy = TravelPolicy::where('destination_city_id', $data['destination_city_id'])
            ->where('grade_group', $employee->grade)->first();
        $estimatedCost = $policy ? (($policy->daily_allowance * $days) + ($policy->max_hotel_rate * ($days - 1)) + $policy->max_transport_rate) : 0;

        DB::beginTransaction();
        try {
            $travelRequest = TravelRequest::create([
                'requester_employee_id' => $employee->id,
                'origin_city_id'        => $data['origin_city_id'],
                'destination_city_id'   => $data['destination_city_id'],
                'start_date'            => $data['start_date'],
                'end_date'              => $data['end_date'],
                'purpose'               => $data['purpose'],
                'estimated_total_cost'  => $estimatedCost,
                'status'                => 'Draft', // Sesuai flow baru
            ]);

            // Insert Rombongan
            $assignees = $data['assignee_ids'] ?? [];
            if (!in_array($employee->id, $assignees)) $assignees[] = $employee->id; 
            
            foreach ($assignees as $assigneeId) {
                TravelAssignee::create([
                    'travel_request_id' => $travelRequest->id,
                    'employee_id'       => $assigneeId,
                    'is_leader'         => $assigneeId === $employee->id,
                ]);
            }

            AuditLog::create([
                'user_id' => $user->id, 'action' => 'CREATE_DRAFT', 'module' => 'TRAVEL_REQUEST',
                'description' => "Draft pengajuan dibuat.", 'ip_address' => $ipAddress, 'user_agent' => $userAgent,
            ]);

            DB::commit();
            return $travelRequest;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // 2. Fungsi Kirim Pengajuan ke Atasan
    public function submitToAtasan($travelRequestId, User $user, $ipAddress, $userAgent)
    {
        $travelRequest = TravelRequest::findOrFail($travelRequestId);

        // Hanya bisa dikirim jika statusnya Draft atau sedang di-Revisi
        if (!in_array($travelRequest->status, ['Draft', 'Revisi'])) {
            throw new Exception("Pengajuan ini tidak dapat dikirim karena status saat ini adalah: {$travelRequest->status}");
        }

        DB::beginTransaction();
        try {
            $travelRequest->update(['status' => 'Menunggu Approval Atasan']);

            AuditLog::create([
                'user_id' => $user->id, 'action' => 'SUBMIT_REQUEST', 'module' => 'TRAVEL_REQUEST',
                'description' => "Pengajuan dikirim ke Atasan Langsung.", 'ip_address' => $ipAddress, 'user_agent' => $userAgent,
            ]);

            DB::commit();
            return $travelRequest;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}