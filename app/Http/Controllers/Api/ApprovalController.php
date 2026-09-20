<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalDecisionRequest;
use App\Models\TravelRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index(Request $request)
    {
        // Tangkap status dari query parameter FE (jika ada), defaultnya nunggu atasan
        $status = $request->query('status', 'Menunggu Persetujuan Atasan');

        // Eager load tipis-tipis saja karena Card nggak butuh data berat
        $queue = TravelRequest::with(['requester', 'destinationCity'])
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $queue
        ]);
    }

    // 2. SHOW standar untuk Drawer/Modal Detail di FE
    public function show($id)
    {
        // Eager load komplit karena Atasan butuh baca semuanya sebelum nge-klik Setuju/Tolak
        $travelRequest = TravelRequest::with([
            'requester.department', 
            'originCity', 
            'destinationCity',
            'approvals.approver' // Tarik juga log persetujuan (kalau misal ada 2 lapis)
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $travelRequest
        ]);
    }

    public function decide(ApprovalDecisionRequest $request, $id)
    {
        try {
            // Langsung lempar ke Service, biarkan Service yang menghakimi hak aksesnya
            $travelRequest = $this->approvalService->processApproval(
                $id, 
                $request->user(), 
                $request->validated(), 
                $request->ip(), 
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Persetujuan berhasil diproses.',
                'data' => $travelRequest
            ], 200);

        } catch (\Exception $e) {
            // Menangkap Exception dari Service (seperti "Akses ditolak. Hanya Atasan Langsung...")
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400); // <-- Ubah jadi 400 (Bad Request), jangan biarkan jadi 500 atau 403
        }
    }
}