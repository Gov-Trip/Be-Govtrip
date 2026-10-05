<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceVerifyRequest;
use App\Services\FinanceService;
use App\Models\TravelRequest;
use App\Models\User;
use App\Models\Employee;
use App\Models\ExpenseReport;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class FinanceController extends Controller
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'Siap Berangkat');

        $queue = TravelRequest::with(['requester', 'destinationCity'])
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $queue
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

        $totalRealisasi = ExpenseReport::whereHas('travelRequest', function($q) {
                $q->where('status', 'Selesai')
                  ->whereMonth('updated_at', now()->month)
                  ->whereYear('updated_at', now()->year);
            })->sum('total_real_expense');

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
            ]
        ]);
    }

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
            'data'    => $travelRequest
        ]);
    }

    public function verify(FinanceVerifyRequest $request, $id)
    {
        try {
            $travelRequest = TravelRequest::findOrFail($id);

            $result = $this->financeService->verifyExpense(
                $id,
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            // PERBAIKAN: Kirim lonceng ke tabel User, bukan Employee
            if ($result->requester && $result->requester->user_id) {
                $requesterUser = User::find($result->requester->user_id);
                if ($requesterUser) {
                    $requesterUser->notify(new SystemNotification(
                        "SPPD dan Dana Siap!", 
                        "Dana Advance telah disetujui. Dokumen SPPD siap diunduh.", 
                        "success"
                    ));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Laporan SPJ berhasil diverifikasi.',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function rejectLpj(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5'
        ]);

        $travelRequest = TravelRequest::with('expenseReport')->findOrFail($id);

        $travelRequest->update([
            'status' => 'Revisi LPJ',
            'rejection_reason' => $request->rejection_reason
        ]);

        if ($travelRequest->expenseReport) {
            $travelRequest->expenseReport->update([
                'status' => 'REJECTED',
                'finance_notes' => $request->rejection_reason
            ]);
        }

        $requesterUser = User::find($travelRequest->requester->user_id);
        if ($requesterUser) {
            $requesterUser->notify(new SystemNotification(
                "LPJ Ditolak Bendahara", 
                "Alasan: " . $request->rejection_reason, 
                "error"
            ));
        }

        return response()->json([
            'success' => true, 
            'message' => 'LPJ dikembalikan untuk direvisi.'
        ]);
    }

    public function downloadFinalAuditReport($id)
    {
        try {
            // PERBAIKAN: Membuang relasi 'assignees' yang tidak ada di struktur baru
            $travelRequest = TravelRequest::with([
                'requester.department',
                'originCity',
                'destinationCity',
                'approvals.approver',
                'logistics',
                'expenseReport',
                'attachments',
                'auditLogs' => function ($query) {
                    $query->whereIn('action', ['SUBMIT_SPJ', 'VERIFY_SPJ']);
                }
            ])->findOrFail($id);

            if ($travelRequest->status !== 'Selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'LPJ Final belum bisa dicetak karena proses belum berstatus Selesai.'
                ], 403);
            }

            $advancePayment = $travelRequest->advance_payment ?? 0;
            $realExpense = $travelRequest->expenseReport->total_real_expense ?? 0;
            $balance = $advancePayment - $realExpense;

            $pdf = Pdf::loadView('pdf.lpj_final', [
                'travelRequest'  => $travelRequest, 
                'advancePayment' => $advancePayment, 
                'realExpense'    => $realExpense, 
                'balance'        => $balance
            ]);
            
            $pdf->setPaper('A4', 'portrait');

            $sppd = $travelRequest->sppd_number ?? $travelRequest->id;
            $fileName = 'LPJ_FINAL_' . str_replace('/', '_', $sppd) . '.pdf';

            return $pdf->stream($fileName);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencetak dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $history = TravelRequest::with(['requester', 'destinationCity'])
            ->whereIn('status', ['Selesai', 'Revisi LPJ']) 
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $history
        ]);
    }
}