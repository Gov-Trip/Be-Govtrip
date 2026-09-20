<?php
namespace App\Services;

use App\Models\TravelRequest;
use App\Models\TravelLogistics;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Exception;

class TravelLogisticsService
{
    public function inputLogistics($travelRequestId, $adminUser, array $data, $ipAddress, $userAgent)
    {
        $travelRequest = TravelRequest::findOrFail($travelRequestId);

        // Validasi State: Admin hanya bisa pesan tiket kalau Kadis sudah ACC
        if ($travelRequest->status !== 'Disetujui') {
            throw new Exception("Logistik hanya bisa diinput untuk pengajuan yang berstatus 'Disetujui' (Telah di-ACC Kadis).");
        }

        DB::beginTransaction();
        try {
            // 1. Simpan data tiket/hotel
            TravelLogistics::create(array_merge($data, [
                'travel_request_id' => $travelRequest->id,
                'admin_travel_id'   => $adminUser->employee->id,
            ]));

            // 2. Ubah status agar Karyawan tahu tiket sudah siap
            $travelRequest->update([
                'status' => 'Siap Berangkat'
            ]);

            // 3. Jejak Audit
            AuditLog::create([
                'user_id'     => $adminUser->id,
                'action'      => 'INPUT_LOGISTICS',
                'module'      => 'ADMIN_TRAVEL',
                'description' => "Tiket {$data['transport_type']} (Kode: {$data['booking_code_or_ticket']}) diinput oleh Admin.",
                'ip_address'  => $ipAddress,
                'user_agent'  => $userAgent,
            ]);

            DB::commit();
            return $travelRequest;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}