<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;

class TravelRequestPolicy
{
    // Cuma Karyawan yang bikin pengajuan yang boleh lihat detailnya
    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $user->employee->id === $travelRequest->requester_employee_id;
    }

    // Cuma Karyawan yang bikin yang boleh membatalkan (dan statusnya harus Menunggu Persetujuan)
    public function cancel(User $user, TravelRequest $travelRequest): bool
    {
        return $user->employee->id === $travelRequest->requester_employee_id
            && $travelRequest->status === 'Menunggu Persetujuan';
    }

    // HRD tidak boleh approve pengajuan miliknya sendiri
    public function approve(User $user, TravelRequest $travelRequest): bool
    {
        return $user->employee->department->code === 'HRD'
            && $user->employee->id !== $travelRequest->requester_employee_id;
    }
}
