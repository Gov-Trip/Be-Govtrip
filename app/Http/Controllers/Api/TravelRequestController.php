<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequest;
use App\Services\TravelRequestService;
use App\Models\TravelRequest;
use Illuminate\Http\Request;
use Exception;

class TravelRequestController extends Controller
{
    protected TravelRequestService $travelService;

    public function __construct(TravelRequestService $travelService)
    {
        $this->travelService = $travelService;
    }

    // 1. Tampilkan daftar pengajuan milik Karyawan yang sedang login
    public function index(Request $request)
    {
        // Ambil ID user yang sedang login (sesuaikan jika kamu pakai relasi ->employee_id)
        $userId = $request->user()->id;

        // Tampilkan semua riwayat milik user ini, urutkan dari yang terbaru
        $myRequests = TravelRequest::with(['destinationCity'])
            ->where('requester_employee_id', $userId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $myRequests
        ]);
    }

    // 2. Simpan pengajuan baru sebagai DRAFT
    public function store(StoreTravelRequest $request)
    {
        try {
            // Kita tambahkan request->ip() dan userAgent() untuk kebutuhan Audit Log di Service
            $travelRequest = $this->travelService->createTravelRequest(
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Draft pengajuan perjalanan dinas berhasil dibuat.',
                'data' =>
                    [
                        $travelRequest->only([
                            'id',
                            'requester_employee_id',
                            'origin_city_id',
                            'destination_city_id',
                            'departure_date',
                            'return_date',
                            'purpose',
                            'status'
                        ]),
                    ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // // 3. Tombol "Kirim" -> Ubah status jadi Menunggu Approval Atasan
    // public function submitRequest(Request $request, $id)
    // {
    //     try {
    //         $travelRequest = TravelRequest::findOrFail($id);

    //         // Policy: Pastikan data ini milik karyawan yang sedang login
    //         $this->authorize('view', $travelRequest);

    //         $result = $this->travelService->submitToAtasan(
    //             $id,
    //             $request->user(),
    //             $request->ip(),
    //             $request->userAgent()
    //         );

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Pengajuan berhasil dikirim ke Atasan.',
    //             'data' => $result
    //         ]);

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 400);
    //     }
    // }

    // 4. Lihat detail 1 pengajuan beserta relasinya
    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;

        // Eager load semua data agar Karyawan bisa nge-track sampai mana berkasnya
        $travelRequest = TravelRequest::with([
            'originCity',
            'destinationCity',
            'approvals.approver', // Karyawan bisa lihat catatan atasan (kalau ditolak/revisi)
            'logistics',          // Karyawan bisa lihat kode booking tiket & hotel kalau sudah di-ACC
            // 'expenseReport'       // Karyawan bisa lihat rincian sisa/kurang uang setelah Finance proses
        ])
            ->where('requester_employee_id', $userId) // SECURITY: Cegah Karyawan intip ID pengajuan orang lain
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $travelRequest
        ]);
    }
}