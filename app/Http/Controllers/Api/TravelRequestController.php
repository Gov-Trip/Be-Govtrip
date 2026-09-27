<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequest;
use App\Services\TravelRequestService;
use App\Models\TravelRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\SystemNotification;
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

            $atasan = User::where('department_id', $request->user()->department_id)
                          ->where('role', 'ATASAN') 
                          ->first();
                          
            if ($atasan) {
                $atasan->notify(new SystemNotification(
                    "Pengajuan baru dari {$request->user()->name}",
                    "Tujuan: " . $travelRequest->destinationCity->city_name,
                    "warning"
                ));
            }

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

public function submitReport(Request $request, $id)
    {
        try {
            // Asumsi kamu melempar form request LPJ ke TravelService
            $result = $this->travelService->submitExpenseReport(
                $id,
                $request->user(),
                $request->all(), // atau $request->validated() jika pakai FormRequest
                $request->ip(),
                $request->userAgent()
            );
            // Sesuai mockup Finance: "4 laporan menunggu verifikasi"
            $timFinance = User::whereHas('department', function($q) {
                $q->where('code', 'FINANCE');
            })->get();

            \Illuminate\Support\Facades\Notification::send($timFinance, new SystemNotification(
                "LPJ Baru Masuk",
                "{$request->user()->name} telah mengunggah LPJ untuk SPPD {$result->sppd_number}.",
                "info"
            ));
            // ----------------------------------------

            return response()->json([
                'success' => true,
                'message' => 'Laporan Pertanggungjawaban (LPJ) berhasil dikirim.',
                'data'    => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

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
            'expenseReport',       // Karyawan bisa lihat rincian sisa/kurang uang setelah Finance proses
            'auditLogs' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }
        ])
            ->where('requester_employee_id', $userId) // SECURITY: Cegah Karyawan intip ID pengajuan orang lain
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $travelRequest
        ]);
    }
}