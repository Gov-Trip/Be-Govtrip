<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequest;
use App\Services\TravelRequestService;
use App\Models\TravelRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\AuditLog;
use App\Models\ExpenseReport;
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
    public function dashboard(Request $request)
    {
        try {
            // 1. Cari data Employee dari User yang sedang login
            $employee = Employee::where('user_id', $request->user()->id)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data profil pegawai tidak ditemukan.'
                ], 404);
            }

            // 2. Base query khusus untuk pengajuan milik pegawai ini saja
            $baseQuery = TravelRequest::where('requester_employee_id', $employee->id);

            // 3. Hitung angka summary untuk kotak-kotak di atas dashboard
            // (Silakan sesuaikan string status di bawah dengan enum aslimu)
            $menungguCount = (clone $baseQuery)
                ->whereIn('status', ['Menunggu Persetujuan Atasan', 'Menunggu Admin Travel', 'Menunggu Uang Muka', 'Menunggu Verifikasi LPJ'])
                ->count();

            $aktifCount = (clone $baseQuery)
                ->whereIn('status', ['Disetujui', 'Siap Berangkat'])
                ->count();

            $selesaiCount = (clone $baseQuery)
                ->where('status', 'Selesai')
                ->count();

            $revisiAtauDitolakCount = (clone $baseQuery)
                ->whereIn('status', ['Ditolak', 'Direvisi', 'Revisi LPJ'])
                ->count();

            // 4. Ambil 5 riwayat pengajuan terbaru untuk tabel ringkas
            $recentRequests = (clone $baseQuery)
                ->with(['destinationCity', 'approvals.approver']) // Load relasi yang sering dipakai FE
                ->latest()
                ->take(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => [
                        'menunggu_proses' => $menungguCount,
                        'perjalanan_aktif' => $aktifCount,
                        'selesai' => $selesaiCount,
                        'perlu_perhatian' => $revisiAtauDitolakCount, // Ditolak atau butuh revisi
                    ],
                    'recent_requests' => $recentRequests
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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

            // --- 🔔 TRIGGER NOTIFIKASI KE ATASAN ---
            // 1. Cari data Employee dari User yang sedang login
            $employee = Employee::where('user_id', $request->user()->id)->first();

            if ($employee) {
                $atasanUser = null;

                // 2. Cek apakah pegawai ini memiliki atasan langsung (direct_supervisor_id)
                if ($employee->direct_supervisor_id) {
                    $supervisor = Employee::find($employee->direct_supervisor_id);
                    if ($supervisor) {
                        $atasanUser = User::find($supervisor->user_id);
                    }
                } else {
                    // (Opsional/Fallback): Jika tidak ada atasan langsung, cari posisi 'Kepala' di departemen yang sama
                    $kepala = Employee::where('department_id', $employee->department_id)
                        ->where('position', 'like', '%Kepala%')
                        ->first();
                    if ($kepala) {
                        $atasanUser = User::find($kepala->user_id);
                    }
                }

                // 3. Jika Atasan (User) ditemukan, kirim notifikasinya
                if ($atasanUser) {
                    $atasanUser->notify(new SystemNotification(
                        "Pengajuan baru dari {$employee->name}",
                        "Tujuan: " . $travelRequest->destinationCity->city_name,
                        "warning"
                    ));
                }
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

    public function submitReport($id, $user, array $data, $ip = null, $userAgent = null)
    {
        // 1. Cari data pegawai dari user yang sedang login
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            throw new Exception('Data pegawai tidak ditemukan.');
        }

        // 2. Pastikan tiket ini benar-benar milik pegawai yang bersangkutan
        $travelRequest = TravelRequest::where('id', $id)
            ->where('requester_employee_id', $employee->id)
            ->first();

        if (!$travelRequest) {
            throw new Exception('Pengajuan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // 3. Validasi status: Hanya bisa submit LPJ jika statusnya sesuai
        // (Sesuaikan array status ini dengan alur bisnismu, misal 'Selesai' atau 'Siap Berangkat' atau 'Revisi LPJ')
        $allowedStatuses = ['Selesai', 'Revisi LPJ', 'Siap Berangkat'];
        if (!in_array($travelRequest->status, $allowedStatuses)) {
            throw new Exception('LPJ tidak dapat dikirim pada status saat ini: ' . $travelRequest->status);
        }

        // 4. Simpan atau Update data ke tabel expense_reports
        $expenseReport = ExpenseReport::updateOrCreate(
            ['travel_request_id' => $travelRequest->id],
            [
                'employee_id' => $employee->id, // Wajib diisi sesuai migration
                'total_advance' => $data['total_advance'] ?? 0,
                'total_real_expense' => $data['total_real_expense'] ?? 0, // Nama kolom asli
                'balance_amount' => $data['balance_amount'] ?? 0,
                'activity_summary' => $data['activity_summary'] ?? 'Ringkasan kegiatan tidak diisi.', // Wajib karena tidak nullable di db
                'status' => 'SUBMITTED'
            ]
        );
        // 5. Ubah status tiket agar masuk ke Dasbor Bendahara
        $travelRequest->status = 'Menunggu Verifikasi LPJ';
        $travelRequest->save();

        // 6. Catat ke Audit Log
        AuditLog::create([
            'travel_request_id' => $travelRequest->id,
            'user_id' => $user->id,
            'action' => 'SUBMIT_SPJ',
            'description' => "Mengirim Laporan Pertanggungjawaban (LPJ) dengan total realisasi Rp " . number_format((float) $expenseReport->total_real_expense, 0, ',', '.'),
            'ip_address' => $ip,
            'user_agent' => $userAgent
        ]);

        return $travelRequest;
    }
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