<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalDecisionRequest;
use App\Models\TravelRequest;
use App\Models\Employee;
use App\Models\User;
use App\Models\Approval;
use App\Services\ApprovalService;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Exception;

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
            'data' => $queue
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
            'data' => $travelRequest
        ]);
    }

    public function dashboard(Request $request)
    {
        // PERBAIKAN: Cari data employee dari user yang login
        $employee = Employee::where('user_id', $request->user()->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan.'
            ], 404);
        }

        $departmentId = $employee->department_id;

        // Bantuan query dasar: Cari semua pengajuan dari departemen Atasan ini
        $baseQuery = TravelRequest::whereHas('requester', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });

        // 1. Hitung Angka Summary (4 Kotak di Atas)
        $menungguCount = (clone $baseQuery)
            ->where('status', 'Menunggu Persetujuan Atasan')
            ->count();

        $disetujuiBulanIniCount = (clone $baseQuery)
            ->where('status', '!=', 'Menunggu Persetujuan Atasan')
            ->where('status', '!=', 'Ditolak')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $dikembalikanCount = (clone $baseQuery)
            ->where('status', 'Direvisi') // Sesuaikan dengan enum/status "Dikembalikan" di DB-mu
            ->whereMonth('created_at', now()->month)
            ->count();

        $sedangDinasCount = (clone $baseQuery)
            ->where('status', 'Selesai')
            ->whereDate('start_date', '<=', now()) // <-- GANTI DI SINI
            ->whereDate('end_date', '>=', now())   // <-- GANTI DI SINI
            ->count();

        // 2. Ambil List "Pengajuan menunggu" (Tampilkan 5 teratas)
        $menungguList = (clone $baseQuery)
            ->with(['requester', 'destinationCity'])
            ->where('status', 'Menunggu Persetujuan Atasan')
            ->latest()
            ->take(5)
            ->get();

        // 3. Ambil List "Perjalanan tim" (Yang sedang jalan hari ini)
        $perjalananTimList = (clone $baseQuery)
            ->with(['requester', 'destinationCity'])
            ->where('status', 'Selesai')
            ->whereDate('start_date', '<=', now()) // <-- GANTI DI SINI
            ->whereDate('end_date', '>=', now())   // <-- GANTI DI SINI
            ->latest()
            ->take(5)
            ->get();

        // Kembalikan JSON rapi untuk FE
        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'menunggu' => $menungguCount,
                    'disetujui_bulan_ini' => $disetujuiBulanIniCount,
                    'dikembalikan' => $dikembalikanCount,
                    'sedang_dinas' => $sedangDinasCount,
                ],
                'menunggu_list' => $menungguList,
                'perjalanan_tim' => $perjalananTimList
            ]
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

            if ($travelRequest->status === 'Disetujui') {
                // PERBAIKAN 1: Notif ke Karyawan (Cari User-nya dari tabel requester/employee)
                if ($travelRequest->requester && $travelRequest->requester->user_id) {
                    $requesterUser = User::find($travelRequest->requester->user_id);
                    if ($requesterUser) {
                        $requesterUser->notify(new SystemNotification(
                            "Pengajuan ke {$travelRequest->destinationCity->city_name} disetujui Atasan",
                            "Diteruskan ke Admin Travel untuk pembelian tiket.",
                            "success"
                        ));
                    }
                }

                // PERBAIKAN 2: Notif ke Group Admin Travel (Cari via tabel Employee dulu)
                $adminTravelEmployees = Employee::whereHas('department', function ($q) {
                    $q->where('code', 'ADMIN_TRAVEL');
                })->get();

                // Ambil ID User dari data pegawai Admin Travel
                $adminUserIds = $adminTravelEmployees->pluck('user_id')->filter();
                $adminTravelUsers = User::whereIn('id', $adminUserIds)->get();

                if ($adminTravelUsers->isNotEmpty()) {
                    Notification::send($adminTravelUsers, new SystemNotification(
                        "Pengajuan Tiket Baru",
                        "Segera proses tiket untuk {$travelRequest->requester->name}.",
                        "info"
                    ));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Persetujuan berhasil diproses.',
                'data' => $travelRequest
            ], 200);

        } catch (Exception $e) {
            // Menangkap Exception dari Service
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5'
        ]);

        $travelRequest = TravelRequest::findOrFail($id);
        
        $travelRequest->update([
            'status' => 'Direvisi',
            'rejection_reason' => $request->rejection_reason
        ]);

        Approval::create([
            'travel_request_id' => $travelRequest->id,
            'approver_employee_id' => $request->user()->employee->id,
            'status' => 'Ditolak',
            'notes' => $request->rejection_reason,
            'action_at' => now(),
        ]);

        $requesterUser = User::find($travelRequest->requester->user_id);
        if ($requesterUser) {
            $requesterUser->notify(new SystemNotification(
                "Pengajuan Dikembalikan", 
                "Atasan meminta revisi: " . $request->rejection_reason, 
                "error"
            ));
        }

        return response()->json([
            'success' => true, 
            'message' => 'Pengajuan dikembalikan ke karyawan untuk direvisi.'
        ]);
    }

    public function history(Request $request)
    {
        // PERBAIKAN: Cari data employee dari user yang login
        $employee = Employee::where('user_id', $request->user()->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan.'
            ], 404);
        }

        // Ambil pengajuan yang departemennya sama dengan Atasan, 
        // TAPI statusnya SUDAH BUKAN 'Menunggu Persetujuan Atasan'
        $history = TravelRequest::with(['requester'])
            ->whereHas('requester', function ($q) use ($employee) {
                $q->where('department_id', $employee->department_id);
            })
            ->where('status', '!=', 'Menunggu Persetujuan Atasan')
            ->latest()
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $history]);
    }
}