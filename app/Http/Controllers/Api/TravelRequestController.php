<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequest;
use App\Models\Employee;
use App\Models\RefCity;
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

    // 0A. Dashboard Karyawan (Kartu Statistik & Riwayat Terbaru)
    public function dashboard(Request $request)
    {
        $employeeId = $request->user()->employee->id;

        $totalPengajuan = TravelRequest::where('requester_employee_id', $employeeId)->count();
        $dalamProses = TravelRequest::where('requester_employee_id', $employeeId)
            ->whereIn('status', ['Draft', 'Menunggu Persetujuan Atasan', 'Menunggu Approval Atasan', 'Disetujui'])
            ->count();
        $perluRevisi = TravelRequest::where('requester_employee_id', $employeeId)
            ->whereIn('status', ['Revisi', 'Direvisi', 'Revisi LPJ'])
            ->count();
        $selesai = TravelRequest::where('requester_employee_id', $employeeId)
            ->whereIn('status', ['Siap Berangkat', 'Selesai'])
            ->count();

        $recentRequests = TravelRequest::with(['originCity', 'destinationCity'])
            ->where('requester_employee_id', $employeeId)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    'total_pengajuan' => $totalPengajuan,
                    'dalam_proses'    => $dalamProses,
                    'perlu_revisi'    => $perluRevisi,
                    'selesai'         => $selesai,
                ],
                'recent_requests' => $recentRequests,
            ],
        ]);
    }

    // 0B. Data Master untuk Dropdown Form FE (Daftar Kota & Pegawai Rombongan)
    public function formOptions(Request $request)
    {
        $cities = RefCity::orderBy('city_name', 'asc')->get(['id', 'province_name', 'city_name']);
        $employees = Employee::with('department:id,name')
            ->orderBy('name', 'asc')
            ->get(['id', 'department_id', 'nip', 'name', 'position', 'grade']);

        return response()->json([
            'success' => true,
            'data'    => [
                'cities'    => $cities,
                'employees' => $employees,
            ],
        ]);
    }

    // 1. Tampilkan daftar pengajuan milik Karyawan yang sedang login
    public function index(Request $request)
    {
        $employeeId = $request->user()->employee->id;
        $status = $request->query('status');

        $query = TravelRequest::with(['originCity', 'destinationCity', 'assignees.employee'])
            ->where('requester_employee_id', $employeeId);

        if ($status) {
            $query->where('status', $status);
        }

        $myRequests = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $myRequests,
        ]);
    }

    // 2. Simpan pengajuan baru sebagai DRAFT
    public function store(StoreTravelRequest $request)
    {
        try {
            $travelRequest = $this->travelService->createTravelRequest(
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Draft pengajuan perjalanan dinas berhasil dibuat.',
                'data'    => $travelRequest->load(['originCity', 'destinationCity', 'assignees.employee']),
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // 3A. Update Draft atau Perbaikan Revisi
    public function update(StoreTravelRequest $request, $id)
    {
        try {
            $travelRequest = $this->travelService->updateTravelRequest(
                $id,
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Data pengajuan berhasil diperbarui.',
                'data'    => $travelRequest->load(['originCity', 'destinationCity', 'assignees.employee']),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // 3B. Kirim Draft atau Ajukan Ulang Revisi ke Atasan Langsung
    public function submit(Request $request, $id)
    {
        try {
            $travelRequest = $this->travelService->submitToAtasan(
                $id,
                $request->user(),
                $request->ip(),
                $request->userAgent()
            );

            // Cari akun User milik Atasan Langsung dari tabel Employee
            $employee = $request->user()->employee;
            if ($employee && $employee->direct_supervisor_id) {
                $supervisorEmp = Employee::find($employee->direct_supervisor_id);
                if ($supervisorEmp && $supervisorEmp->user_id) {
                    $atasanUser = User::find($supervisorEmp->user_id);
                    if ($atasanUser) {
                        $atasanUser->notify(new SystemNotification(
                            "Pengajuan Baru dari {$employee->name}",
                            'Tujuan: ' . ($travelRequest->destinationCity->city_name ?? '-'),
                            'warning'
                        ));
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim ke Atasan.',
                'data'    => $travelRequest,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // 4. Lihat detail 1 pengajuan beserta relasinya
    public function show(Request $request, $id)
    {
        $employeeId = $request->user()->employee->id;

        $travelRequest = TravelRequest::with([
            'requester.department',
            'originCity',
            'destinationCity',
            'assignees.employee',
            'approvals.approver',
            'logistics',
            'expenseReport',
            'auditLogs' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
        ])
            ->where('requester_employee_id', $employeeId)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $travelRequest,
        ]);
    }

    // 5. PREVIEW DOKUMEN (Agar Karyawan Bisa Melihat Isi SPPD & Dana Sebelum Download PDF)
    public function previewDocument(Request $request, $id)
    {
        $employeeId = $request->user()->employee->id;

        $travelRequest = TravelRequest::with([
            'requester.department',
            'originCity',
            'destinationCity',
            'assignees.employee',
            'approvals.approver',
            'logistics',
            'expenseReport',
        ])
            ->where('requester_employee_id', $employeeId)
            ->findOrFail($id);

        $advancePayment = (float) ($travelRequest->advance_payment ?? 0);
        $realExpense = (float) ($travelRequest->expenseReport->total_real_expense ?? 0);
        $balance = $advancePayment - $realExpense;

        return response()->json([
            'success' => true,
            'data'    => [
                'can_download_sppd' => ! empty($travelRequest->sppd_number),
                'can_download_lpj'  => $travelRequest->status === 'Selesai',
                'document_summary'  => [
                    'sppd_number'          => $travelRequest->sppd_number ?? 'Belum Terbit',
                    'status'               => $travelRequest->status,
                    'requester_name'       => $travelRequest->requester->name ?? '-',
                    'requester_nip'        => $travelRequest->requester->nip ?? '-',
                    'department'           => $travelRequest->requester->department->name ?? '-',
                    'origin_city'          => $travelRequest->originCity->city_name ?? '-',
                    'destination_city'     => $travelRequest->destinationCity->city_name ?? '-',
                    'start_date'           => $travelRequest->start_date,
                    'end_date'             => $travelRequest->end_date,
                    'purpose'              => $travelRequest->purpose,
                    'estimated_total_cost' => (float) $travelRequest->estimated_total_cost,
                    'advance_payment'      => $advancePayment,
                    'real_expense'         => $realExpense,
                    'balance'              => $balance,
                ],
                'assignees'         => $travelRequest->assignees,
                'logistics'         => $travelRequest->logistics,
                'approvals'         => $travelRequest->approvals,
                'expense_report'    => $travelRequest->expenseReport,
            ],
        ]);
    }

    // 6. Download Dokumen SPPD (PDF)
    public function downloadSppd(Request $request, $id)
    {
        try {
            $employeeId = $request->user()->employee->id;

            $travelRequest = TravelRequest::with([
                'requester.department',
                'originCity',
                'destinationCity',
                'assignees.employee',
                'logistics',
            ])
                ->where('requester_employee_id', $employeeId)
                ->findOrFail($id);

            if (! $travelRequest->sppd_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen SPPD belum diterbitkan oleh Admin Travel.',
                ], 403);
            }

            $pdf = Pdf::loadView('pdf.sppd', [
                'travelRequest' => $travelRequest,
            ]);

            $pdf->setPaper('A4', 'portrait');
            $fileName = 'SPPD_' . str_replace('/', '_', $travelRequest->sppd_number) . '.pdf';

            return $pdf->stream($fileName);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh SPPD: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 7. Download Dokumen LPJ / Administrasi Final (PDF)
    public function downloadLpjFinal(Request $request, $id)
    {
        try {
            $employeeId = $request->user()->employee->id;

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
                ->where('requester_employee_id', $employeeId)
                ->findOrFail($id);

            if ($travelRequest->status !== 'Selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen Final belum dapat diunduh karena verifikasi Finance belum berstatus Selesai.',
                ], 403);
            }

            $advancePayment = $travelRequest->advance_payment ?? 0;
            $realExpense = $travelRequest->expenseReport->total_real_expense ?? 0;
            $balance = $advancePayment - $realExpense;

            $pdf = Pdf::loadView('pdf.lpj_final', [
                'travelRequest'  => $travelRequest,
                'advancePayment' => $advancePayment,
                'realExpense'    => $realExpense,
                'balance'        => $balance,
            ]);

            $pdf->setPaper('A4', 'portrait');
            $sppdNumber = $travelRequest->sppd_number ?? $travelRequest->id;
            $fileName = 'LPJ_FINAL_' . str_replace('/', '_', $sppdNumber) . '.pdf';

            return $pdf->stream($fileName);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh LPJ Final: ' . $e->getMessage(),
            ], 500);
        }
    }
}