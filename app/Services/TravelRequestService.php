<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\TravelAssignee;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TravelRequestService
{
    // 1. Fungsi Buat Draft
    public function createTravelRequest(User $user, array $data, $ipAddress, $userAgent): TravelRequest
    {
        $employee = $user->employee;
        $days = Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1;

        $policy = TravelPolicy::where('destination_city_id', $data['destination_city_id'])
            ->where('grade_group', $employee->grade)
            ->first();

        $estimatedCost = $policy
            ? (($policy->daily_allowance * $days) + ($policy->max_hotel_rate * max(0, $days - 1)) + $policy->max_transport_rate)
            : 0;

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
                'status'                => 'Draft',
            ]);

            $assignees = $data['assignee_ids'] ?? [];
            if (! in_array($employee->id, $assignees)) {
                $assignees[] = $employee->id;
            }

            foreach ($assignees as $assigneeId) {
                TravelAssignee::create([
                    'travel_request_id' => $travelRequest->id,
                    'employee_id'       => $assigneeId,
                    'is_leader'         => $assigneeId === $employee->id,
                ]);
            }

            AuditLog::create([
                'travel_request_id' => $travelRequest->id,
                'user_id'           => $user->id,
                'action'            => 'CREATE_DRAFT',
                'module'            => 'TRAVEL_REQUEST',
                'description'       => 'Draft pengajuan dibuat.',
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

    // 2. Fungsi Edit Draft / Perbaikan Revisi
    public function updateTravelRequest($travelRequestId, User $user, array $data, $ipAddress, $userAgent): TravelRequest
    {
        $employee = $user->employee;
        $travelRequest = TravelRequest::where('requester_employee_id', $employee->id)
            ->findOrFail($travelRequestId);

        if (! in_array($travelRequest->status, ['Draft', 'Revisi', 'Direvisi'])) {
            throw new Exception("Pengajuan berstatus {$travelRequest->status} tidak dapat diubah.");
        }

        $startDate = $data['start_date'] ?? $travelRequest->start_date;
        $endDate = $data['end_date'] ?? $travelRequest->end_date;
        $destCityId = $data['destination_city_id'] ?? $travelRequest->destination_city_id;

        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $policy = TravelPolicy::where('destination_city_id', $destCityId)
            ->where('grade_group', $employee->grade)
            ->first();

        $estimatedCost = $policy
            ? (($policy->daily_allowance * $days) + ($policy->max_hotel_rate * max(0, $days - 1)) + $policy->max_transport_rate)
            : $travelRequest->estimated_total_cost;

        DB::beginTransaction();
        try {
            $travelRequest->update([
                'origin_city_id'      => $data['origin_city_id'] ?? $travelRequest->origin_city_id,
                'destination_city_id' => $destCityId,
                'start_date'          => $startDate,
                'end_date'            => $endDate,
                'purpose'             => $data['purpose'] ?? $travelRequest->purpose,
                'estimated_total_cost'=> $estimatedCost,
            ]);

            if (isset($data['assignee_ids'])) {
                TravelAssignee::where('travel_request_id', $travelRequest->id)->delete();
                $assignees = $data['assignee_ids'];
                if (! in_array($employee->id, $assignees)) {
                    $assignees[] = $employee->id;
                }
                foreach ($assignees as $assigneeId) {
                    TravelAssignee::create([
                        'travel_request_id' => $travelRequest->id,
                        'employee_id'       => $assigneeId,
                        'is_leader'         => $assigneeId === $employee->id,
                    ]);
                }
            }

            AuditLog::create([
                'travel_request_id' => $travelRequest->id,
                'user_id'           => $user->id,
                'action'            => 'UPDATE_REQUEST',
                'module'            => 'TRAVEL_REQUEST',
                'description'       => 'Data pengajuan diperbarui oleh karyawan.',
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

    // 3. Fungsi Kirim / Ajukan Ulang Pengajuan ke Atasan
    public function submitToAtasan($travelRequestId, User $user, $ipAddress, $userAgent): TravelRequest
    {
        $employee = $user->employee;
        $travelRequest = TravelRequest::where('requester_employee_id', $employee->id)
            ->findOrFail($travelRequestId);

        if (! in_array($travelRequest->status, ['Draft', 'Revisi', 'Direvisi'])) {
            throw new Exception("Pengajuan ini tidak dapat dikirim karena status saat ini adalah: {$travelRequest->status}");
        }

        DB::beginTransaction();
        try {
            $travelRequest->update([
                'status'           => 'Menunggu Persetujuan Atasan',
                'rejection_reason' => null,
            ]);

            AuditLog::create([
                'travel_request_id' => $travelRequest->id,
                'user_id'           => $user->id,
                'action'            => 'SUBMIT_REQUEST',
                'module'            => 'TRAVEL_REQUEST',
                'description'       => 'Pengajuan dikirim ke Atasan Langsung.',
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