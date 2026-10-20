<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceVerifyRequest;
use App\Models\ExpenseReport;
use App\Models\TravelRequest;
use App\Notifications\SystemNotification;
use App\Services\FinanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    // 1. Tampilkan antrean SPJ yang minta diverifikasi
    public function index(Request $request)
    {
        $status = $request->query('status', 'Siap Berangkat');

        $queue = TravelRequest::with(['requester', 'destinationCity'])
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $queue,
        ]);
    }

    public function dashboard(Request $request)
    {
        $perluDiverifikasi = TravelRequest::where('status', 'Menunggu Verifikasi LPJ')->count();

        $terverifikasiBulanIni = TravelRequest::where('status', 'Selesai')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $dikembalikan = TravelRequest::where('status', 'Revisi LPJ')
            ->whereMonth('updated_at', now()->month)
            ->count();

        // Total Realisasi (Menjumlahkan total_expense dari tabel expense_reports bulan ini)
        $totalRealisasi = ExpenseReport::whereHas('travelRequest', function ($q) {
            $q->where('status', 'Selesai')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year);
        })->sum('total_expense');

        // Antrean Verifikasi (Ambil 5 teratas)
        $antreanVerifikasi = TravelRequest::with(['requester', 'expenseReport'])
            ->where('status', 'Menunggu Verifikasi LPJ')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'perlu_diverifikasi' => $perluDiverifikasi,
                    'terverifikasi_bulan_ini' => $terverifikasiBulanIni,
                    'dikembalikan' => $dikembalikan,
                    'total_realisasi' => $totalRealisasi,
                ],
                'antrean_verifikasi' => $antreanVerifikasi,
            ],
        ]);
    }

    // SHOW: Detail "Stopmap" lengkap sebelum mencairkan dana
    public function show($id)
    {
        $travelRequest = TravelRequest::with([
            'requester.department',
            'originCity',
            'destinationCity',
            'approvals.approver',
            'logistics',
            'expenseReport',
            'attachments',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $travelRequest,
        ]);
    }

    // 2. Verifikasi nota & laporan (ACC / Revisi)
    public function verify(FinanceVerifyRequest $request, $id)
    {
        try {
            $travelRequest = TravelRequest::findOrFail($id);
            // $this->authorize('verify', $travelRequest); // Cek Policy bentrok kepentingan

            $result = $this->financeService->verifyExpense(
                $id,
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            $result->requester->notify(new SystemNotification(
                'SPPD dan Dana Siap!',
                'Dana Advance telah disetujui. Dokumen SPPD siap diunduh.',
                'success'
            ));

            return response()->json([
                'success' => true,
                'message' => 'Laporan SPJ berhasil diverifikasi.',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // 3. Download LPJ Final (Ultimate Audit Trail)
    public function downloadFinalAuditReport($id)
    {
        try {
            // Eager load semua relasi untuk "Kertas Kerja" Auditor
            $travelRequest = TravelRequest::with([
                'requester.department',
                'originCity',
                'destinationCity',
                'assignees.employee',
                'approvals.approver',   // Jejak ACC Atasan, Kabid, Kadis
                'logistics',            // Bukti tiket pesawat/hotel
                'expenseReport',        // Rincian uang
                'attachments',          // Nota-nota
                'auditLogs' => function ($query) {
                    $query->whereIn('action', ['SUBMIT_SPJ', 'VERIFY_SPJ']);
                },
            ])->findOrFail($id);

            if ($travelRequest->status !== 'Selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'LPJ Final belum bisa dicetak karena proses belum berstatus Selesai.',
                ], 403);
            }

            $advancePayment = $travelRequest->advance_payment ?? 0;
            $realExpense = $travelRequest->expenseReport->total_expense ?? 0;
            $balance = $advancePayment - $realExpense;

            // PERBAIKAN: Ubah 'pdf.template' ke 'pdf.lpj_final' dan ubah key 'data' menjadi 'travelRequest'
            $pdf = Pdf::loadView('pdf.lpj_final', [
                'travelRequest' => $travelRequest,
                'advancePayment' => $advancePayment,
                'realExpense' => $realExpense,
                'balance' => $balance,
            ]);

            $pdf->setPaper('A4', 'portrait');

            $sppd = $travelRequest->sppd_number ?? $travelRequest->id;
            $fileName = 'LPJ_FINAL_'.str_replace('/', '_', $sppd).'.pdf';

            // Aku ganti ke stream() agar kamu bisa lihat preview-nya di Bruno tanpa harus buka folder download PC-mu
            return $pdf->stream($fileName);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencetak dokumen: '.$e->getMessage(),
            ], 500);
        }
    }

    public function history(Request $request)
    {
        // Mengambil semua pengajuan yang sudah melewati tahap Bendahara
        // (Status Selesai atau Revisi LPJ)
        $history = TravelRequest::with(['requester'])
            ->whereIn('status', ['Selesai', 'Revisi LPJ']) // Sesuaikan enum statusmu
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }
}
