<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceVerifyRequest;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\FinanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class FinanceController extends Controller
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    // 1. Tampilkan antrean yang butuh diverifikasi Finance
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = TravelRequest::with(['requester.department', 'originCity', 'destinationCity', 'logistics', 'expenseReport']);

        if ($status) {
            $query->where('status', $status);
        } else {
            // Default: tampilkan semua yang siap diverifikasi Finance
            $query->whereIn('status', ['Siap Berangkat', 'Menunggu Verifikasi LPJ']);
        }

        $queue = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $queue,
        ]);
    }

    // 2. Dashboard Ringkas Finance
    public function dashboard(Request $request)
    {
        $perluDiverifikasi = TravelRequest::whereIn('status', ['Siap Berangkat', 'Menunggu Verifikasi LPJ'])->count();

        $terverifikasiBulanIni = TravelRequest::where('status', 'Selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $dikembalikan = TravelRequest::whereIn('status', ['Revisi Admin Travel', 'Revisi LPJ'])
            ->whereMonth('updated_at', now()->month)
            ->count();

        // Total Realisasi Anggaran yang sudah dicairkan/diverifikasi bulan ini
        $totalRealisasi = (float) TravelRequest::where('status', 'Selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('advance_payment');

        // Antrean Verifikasi (5 teratas)
        $antreanVerifikasi = TravelRequest::with(['requester.department', 'originCity', 'destinationCity', 'logistics', 'expenseReport'])
            ->whereIn('status', ['Siap Berangkat', 'Menunggu Verifikasi LPJ'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    'perlu_diverifikasi'      => $perluDiverifikasi,
                    'terverifikasi_bulan_ini' => $terverifikasiBulanIni,
                    'dikembalikan'            => $dikembalikan,
                    'total_realisasi'         => $totalRealisasi,
                ],
                'antrean_verifikasi' => $antreanVerifikasi,
            ],
        ]);
    }

    // 3. Detail "Stopmap" Lengkap Sebelum Mencairkan Dana
    public function show($id)
    {
        $travelRequest = TravelRequest::with([
            'requester.department',
            'originCity',
            'destinationCity',
            'assignees.employee',
            'approvals.approver',
            'logistics',
            'expenseReport',
            'attachments',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $travelRequest,
        ]);
    }

    // 4. Verifikasi Administrasi & Pencairan Dana (ACC / Revisi)
    public function verify(FinanceVerifyRequest $request, $id)
    {
        try {
            $result = $this->financeService->verifyExpense(
                $id,
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            if ($result->status === 'Selesai') {
                // Jika disetujui: Beri tahu Karyawan bahwa SPPD & Dana sudah siap
                $requesterUser = User::find($result->requester->user_id ?? null);
                if ($requesterUser) {
                    $requesterUser->notify(new SystemNotification(
                        'SPPD dan Dana Siap! ✅',
                        'Anggaran perjalanan telah diverifikasi Finance. Dokumen SPPD siap diunduh.',
                        'success'
                    ));
                }
            } else {
                // Jika ditolak: Beri tahu Admin Travel untuk memperbaiki logistik/administrasi
                $adminTravelUsers = User::whereHas('employee.department', function ($q) {
                    $q->where('code', 'ADMIN_TRAVEL');
                })->get();

                if ($adminTravelUsers->isNotEmpty()) {
                    Notification::send($adminTravelUsers, new SystemNotification(
                        'Revisi Administrasi Logistik ⚠️',
                        "Finance mengembalikan berkas {$result->requester->name}. Catatan: " . ($result->rejection_reason ?? '-'),
                        'error'
                    ));
                }
            }

            return response()->json([
                'success' => true,
                'message' => $result->status === 'Selesai'
                    ? 'Verifikasi berhasil. Dana disetujui dan proses perjalanan dinas selesai.'
                    : 'Berkas dikembalikan ke Admin Travel untuk diperbaiki.',
                'data'    => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // 5. Endpoint Khusus Tolak / Revisi (Jika FE menembak form penolakan terpisah)
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        try {
            $result = $this->financeService->verifyExpense(
                $id,
                $request->user(),
                [
                    'is_valid'         => false,
                    'notes'            => $validated['rejection_reason'],
                    'rejection_reason' => $validated['rejection_reason'],
                ],
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Berkas berhasil dikembalikan untuk revisi.',
                'data'    => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // 6. Download Dokumen Audit / LPJ Final (PDF)
    public function downloadFinalAuditReport($id)
    {
        try {
            $travelRequest = TravelRequest::with([
                'requester.department',
                'originCity',
                'destinationCity',
                'assignees.employee',
                'approvals.approver',
                'logistics',
                'expenseReport',
                'attachments',
                'auditLogs' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                },
            ])->findOrFail($id);

            if ($travelRequest->status !== 'Selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen Final belum bisa dicetak karena proses belum berstatus Selesai.',
                ], 403);
            }

            $advancePayment = (float) ($travelRequest->advance_payment ?? 0);
            $realExpense = (float) ($travelRequest->expenseReport->total_real_expense ?? $advancePayment);
            $balance = $advancePayment - $realExpense;

            $pdf = Pdf::loadView('pdf.lpj_final', [
                'travelRequest'  => $travelRequest,
                'advancePayment' => $advancePayment,
                'realExpense'    => $realExpense,
                'balance'        => $balance,
            ]);

            $pdf->setPaper('A4', 'portrait');

            $sppd = $travelRequest->sppd_number ?? $travelRequest->id;
            $fileName = 'LPJ_FINAL_' . str_replace('/', '_', $sppd) . '.pdf';

            return $pdf->stream($fileName);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencetak dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 7. Riwayat Verifikasi Finance
    public function history(Request $request)
    {
        $history = TravelRequest::with(['requester.department', 'originCity', 'destinationCity', 'logistics', 'expenseReport'])
            ->whereIn('status', ['Selesai', 'Revisi Admin Travel', 'Revisi LPJ'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $history,
        ]);
    }
}