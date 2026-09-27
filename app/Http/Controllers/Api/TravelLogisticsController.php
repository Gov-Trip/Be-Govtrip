<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogisticsRequest;
use App\Services\TravelLogisticsService;
use App\Models\TravelRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Exception;

class TravelLogisticsController extends Controller
{
    protected TravelLogisticsService $logisticsService;

    public function __construct(TravelLogisticsService $logisticsService) 
    {
        $this->logisticsService = $logisticsService;
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'Disetujui');

        $queue = TravelRequest::with(['requester', 'destinationCity'])
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $queue
        ]);
    }

    public function show($id)
    {
        $travelRequest = TravelRequest::with([
            'requester.department', 
            'originCity', 
            'destinationCity',
            'approvals.approver' 
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $travelRequest
        ]);
    }

    public function store(StoreLogisticsRequest $request, $id)
    {
        try {
            $result = $this->logisticsService->inputLogistics(
                $id, 
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            $timFinance = User::whereHas('department', function($q) {
                $q->where('code', 'FINANCE');
            })->get();

            Notification::send($timFinance, new SystemNotification(
                "Laporan Menunggu Verifikasi (Uang Muka)",
                "SPPD untuk {$result->requester->name} siap diproses keuangannya.",
                "warning"
            ));

            return response()->json([
                'success' => true, 
                'message' => 'Data logistik dan tiket berhasil disimpan. Status berubah menjadi Siap Berangkat.', 
                'data'    => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }
}