<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequest;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\TravelRequestService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;

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
            'data' => $myRequests,
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
                    'Tujuan: '.$travelRequest->destinationCity->city_name,
                    'warning'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Draft pengajuan perjalanan dinas berhasil dibuat.',
                'data' => [
                    $travelRequest->only([
                        'id',
                        'requester_employee_id',
                        'origin_city_id',
                        'destination_city_id',
                        'departure_date',
                        'return_date',
                        'purpose',
                        'status',
                    ]),
                ],
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
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
            },
        ])
            ->where('requester_employee_id', $userId) // SECURITY: Cegah Karyawan intip ID pengajuan orang lain
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $travelRequest,
        ]);
    }

    // 5. Download / Preview Dokumen SPPD (Dibuat oleh Admin Travel)
    public function downloadSppd(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;

            $travelRequest = TravelRequest::with([
                'requester.department',
                'originCity',
                'destinationCity',
                'assignees.employee',
            ])
                ->where('requester_employee_id', $userId)
                ->findOrFail($id);

            if (! $travelRequest->sppd_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen SPPD belum diterbitkan oleh Admin Travel.',
                ], 403);
            }

            // Menggunakan Facade Pdf yang sudah di-import di atas
            $pdf = Pdf::loadView('pdf.sppd', [
                'travelRequest' => $travelRequest,
            ]);

            $pdf->setPaper('A4', 'portrait');
            $fileName = 'SPPD_'.str_replace('/', '_', $travelRequest->sppd_number).'.pdf';

            return $pdf->stream($fileName);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh SPPD: '.$e->getMessage(),
            ], 500);
        }
    }

    // 6. Download / Preview Dokumen LPJ Final (Diverifikasi oleh Finance)
    public function downloadLpjFinal(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;

            $travelRequest = TravelRequest::with([
                'requester.department',
                'originCity',
                'destinationCity',
                'assignees.employee',
                'approvals.approver',
                'logistics',
                'expenseReport',
                'attachments',
            ])
                ->where('requester_employee_id', $userId)
                ->findOrFail($id);

            if ($travelRequest->status !== 'Selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'LPJ Final belum dapat diunduh karena proses verifikasi Finance belum berstatus Selesai.',
                ], 403);
            }

            $advancePayment = $travelRequest->advance_payment ?? 0;
            $realExpense = $travelRequest->expenseReport->total_expense ?? 0;
            $balance = $advancePayment - $realExpense;

            // Menggunakan Facade Pdf yang sudah di-import di atas
            $pdf = Pdf::loadView('pdf.lpj_final', [
                'travelRequest' => $travelRequest,
                'advancePayment' => $advancePayment,
                'realExpense' => $realExpense,
                'balance' => $balance,
            ]);

            $pdf->setPaper('A4', 'portrait');
            $sppdNumber = $travelRequest->sppd_number ?? $travelRequest->id;
            $fileName = 'LPJ_FINAL_'.str_replace('/', '_', $sppdNumber).'.pdf';

            return $pdf->stream($fileName);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh LPJ Final: '.$e->getMessage(),
            ], 500);
        }
    }
}
