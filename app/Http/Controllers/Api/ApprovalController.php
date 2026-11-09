<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalDecisionRequest;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\ApprovalService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ApprovalController extends Controller
{
    protected ApprovalService $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    // 1. List Card Antrean Persetujuan
    public function index(Request $request)
    {
        $departmentId = $request->user()->employee->department_id ?? null;
        $status = $request->query('status', 'Menunggu Persetujuan Atasan');

        $query = TravelRequest::with(['requester', 'originCity', 'destinationCity'])
            ->whereHas('requester', function ($q) use ($departmentId) {
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                }
            });

        if ($status === 'Menunggu Persetujuan Atasan') {
            $query->whereIn('status', ['Menunggu Persetujuan Atasan', 'Menunggu Approval Atasan']);
        } else {
            $query->where('status', $status);
        }

        $queue = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $queue,
        ]);
    }

    // 2. Detail Drawer / Modal di FE
    public function show($id)
    {
        $travelRequest = TravelRequest::with([
            'requester.department',
            'originCity',
            'destinationCity',
            'assignees.employee',
            'approvals.approver',
            'logistics',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $travelRequest,
        ]);
    }

    // 3. Dashboard Ringkas Atasan
    public function dashboard(Request $request)
    {
        $departmentId = $request->user()->employee->department_id ?? null;

        $baseQuery = TravelRequest::whereHas('requester', function ($q) use ($departmentId) {
            if ($departmentId) {
                $q->where('department_id', $departmentId);
            }
        });

        // 1. Hitung Angka Summary (4 Kotak di Atas)
        $menungguCount = (clone $baseQuery)
            ->whereIn('status', ['Menunggu Persetujuan Atasan', 'Menunggu Approval Atasan'])
            ->count();

        $disetujuiBulanIniCount = (clone $baseQuery)
            ->whereNotIn('status', ['Draft', 'Menunggu Persetujuan Atasan', 'Menunggu Approval Atasan', 'Ditolak', 'Direvisi', 'Perlu Revisi'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $dikembalikanCount = (clone $baseQuery)
            ->whereIn('status', ['Direvisi', 'Perlu Revisi'])
            ->whereMonth('created_at', now()->month)
            ->count();

        $sedangDinasCount = (clone $baseQuery)
            ->whereIn('status', ['Siap Berangkat', 'Selesai'])
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();

        // 2. Ambil List "Pengajuan menunggu" (5 teratas)
        $menungguList = (clone $baseQuery)
            ->with(['requester', 'originCity', 'destinationCity'])
            ->whereIn('status', ['Menunggu Persetujuan Atasan', 'Menunggu Approval Atasan'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Ambil List "Perjalanan tim" (Yang sedang jalan hari ini)
        $perjalananTimList = (clone $baseQuery)
            ->with(['requester', 'originCity', 'destinationCity'])
            ->whereIn('status', ['Siap Berangkat', 'Selesai'])
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    'menunggu'            => $menungguCount,
                    'disetujui_bulan_ini' => $disetujuiBulanIniCount,
                    'dikembalikan'        => $dikembalikanCount,
                    'sedang_dinas'        => $sedangDinasCount,
                ],
                'menunggu_list'  => $menungguList,
                'perjalanan_tim' => $perjalananTimList,
            ],
        ]);
    }

    // 4. Eksekusi Keputusan Atasan (Setuju / Ditolak / Perlu Revisi)
    public function decide(ApprovalDecisionRequest $request, $id)
    {
        try {
            $travelRequest = $this->approvalService->processApproval(
                $id,
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            $requesterUser = User::find($travelRequest->requester->user_id ?? null);
            $cityName = $travelRequest->destinationCity->city_name ?? '-';

            if ($travelRequest->status === 'Disetujui') {
                if ($requesterUser) {
                    $requesterUser->notify(new SystemNotification(
                        "Pengajuan ke {$cityName} Disetujui Atasan",
                        'Diteruskan ke Admin Travel untuk pengadaan logistik & tiket.',
                        'success'
                    ));
                }

                $adminTravelUsers = User::whereHas('employee.department', function ($q) {
                    $q->where('code', 'ADMIN_TRAVEL');
                })->get();

                if ($adminTravelUsers->isNotEmpty()) {
                    Notification::send($adminTravelUsers, new SystemNotification(
                        'Pengajuan Logistik Baru',
                        "Segera proses kebutuhan logistik untuk {$travelRequest->requester->name}.",
                        'info'
                    ));
                }
            } else {
                // Notifikasi ke Karyawan jika Ditolak atau Direvisi
                if ($requesterUser) {
                    $requesterUser->notify(new SystemNotification(
                        "Pengajuan ke {$cityName} {$travelRequest->status}",
                        'Catatan Atasan: ' . ($travelRequest->rejection_reason ?? '-'),
                        'error'
                    ));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Keputusan persetujuan berhasil diproses.',
                'data'    => $travelRequest,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Alias agar Route::post('/{id}', [ApprovalController::class, 'approve']) tidak error
    public function approve(ApprovalDecisionRequest $request, $id)
    {
        return $this->decide($request, $id);
    }

    // Endpoint khusus jika FE menembak form penolakan terpisah (/reject)
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
            'decision'         => 'nullable|in:Ditolak,Perlu Revisi,Direvisi',
        ]);

        $payload = [
            'decision'         => $validated['decision'] ?? 'Direvisi',
            'notes'            => $validated['rejection_reason'],
            'rejection_reason' => $validated['rejection_reason'],
        ];

        try {
            $travelRequest = $this->approvalService->processApproval(
                $id,
                $request->user(),
                $payload,
                $request->ip(),
                $request->userAgent()
            );

            $requesterUser = User::find($travelRequest->requester->user_id ?? null);
            if ($requesterUser) {
                $requesterUser->notify(new SystemNotification(
                    'Pengajuan Dikembalikan Atasan',
                    'Catatan: ' . $validated['rejection_reason'],
                    'error'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikembalikan untuk direvisi.',
                'data'    => $travelRequest,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // 5. Riwayat Persetujuan Departemen
    public function history(Request $request)
    {
        $departmentId = $request->user()->employee->department_id ?? null;

        $history = TravelRequest::with(['requester', 'originCity', 'destinationCity'])
            ->whereHas('requester', function ($q) use ($departmentId) {
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                }
            })
            ->whereNotIn('status', ['Draft', 'Menunggu Persetujuan Atasan', 'Menunggu Approval Atasan'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $history,
        ]);
    }
}