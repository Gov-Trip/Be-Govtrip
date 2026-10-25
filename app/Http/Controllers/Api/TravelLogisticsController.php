<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogisticsRequest;
use App\Models\RefCity;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\TravelLogisticsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TravelLogisticsController extends Controller
{
    protected TravelLogisticsService $logisticsService;

    public function __construct(TravelLogisticsService $logisticsService)
    {
        $this->logisticsService = $logisticsService;
    }

    // 0. Master Data Referensi Kota, Plafon SBM, & Standar Transportasi untuk Admin Travel
    public function masterData()
    {
        $cities = RefCity::orderBy('city_name', 'asc')->get();
        $policies = TravelPolicy::with('destinationCity')->orderBy('destination_city_id')->get();
        $transportTypes = [
            'Pesawat Udara',
            'Kereta Api',
            'Bus / Travel',
            'Kendaraan Dinas',
            'Kapal Laut',
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'cities'          => $cities,
                'sbm_policies'    => $policies,
                'transport_types' => $transportTypes,
            ],
        ]);
    }

    // 1. List Antrean Tugas Admin Travel
    public function index(Request $request)
    {
        $status = $request->query('status', 'Disetujui');

        $queue = TravelRequest::with(['requester.department', 'originCity', 'destinationCity', 'logistics'])
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $queue,
        ]);
    }

    // 2. Dashboard Ringkas Admin Travel
    public function dashboard(Request $request)
    {
        $totalPegawai = User::where('is_active', true)->count();

        $perjalananBerjalanCount = TravelRequest::whereIn('status', ['Siap Berangkat', 'Selesai'])
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();

        $sppdTerbitCount = TravelRequest::whereNotNull('sppd_number')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $dokumenBelumLengkapCount = TravelRequest::where('status', 'Disetujui')->count();

        $perjalananBerjalanList = TravelRequest::with(['requester', 'originCity', 'destinationCity', 'logistics'])
            ->whereIn('status', ['Siap Berangkat', 'Selesai'])
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->take(5)
            ->get();

        $tindakLanjutList = TravelRequest::with(['requester', 'originCity', 'destinationCity'])
            ->where('status', 'Disetujui')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    'total_pegawai'         => $totalPegawai,
                    'perjalanan_berjalan'   => $perjalananBerjalanCount,
                    'sppd_terbit_bulan_ini' => $sppdTerbitCount,
                    'dokumen_belum_lengkap' => $dokumenBelumLengkapCount,
                ],
                'perjalanan_berjalan_list' => $perjalananBerjalanList,
                'tindak_lanjut_list'       => $tindakLanjutList,
            ],
        ]);
    }

    // 3. Detail Pengajuan + Kalkulasi Otomatis Plafon Master SBM
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

        // Sertakan rincian standar biaya dari Master Data SBM agar tampil di UI Admin Travel
        $standardizedBudget = $this->logisticsService->calculateStandardizedBudget($travelRequest);

        return response()->json([
            'success' => true,
            'data'    => [
                'travel_request'      => $travelRequest,
                'standardized_budget' => $standardizedBudget,
            ],
        ]);
    }

    // 4. Simpan Logistik (Tiket & Akomodasi) + Kunci Anggaran Standar SBM untuk Finance
    public function store(StoreLogisticsRequest $request, $id)
    {
        try {
            $result = $this->logisticsService->inputLogistics(
                $id,
                $request->user(),
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            // Beri notifikasi ke Tim Finance bahwa rincian logistik & anggaran siap diverifikasi
            $timFinance = User::whereHas('employee.department', function ($q) {
                $q->where('code', 'FINANCE');
            })->get();

            if ($timFinance->isNotEmpty()) {
                Notification::send($timFinance, new SystemNotification(
                    'Verifikasi Dana Logistik Baru',
                    "Logistik & anggaran standar untuk {$result->requester->name} siap diverifikasi Finance.",
                    'warning'
                ));
            }

            // Beri notifikasi ke Karyawan bahwa tiket & hotel sudah dipesan
            $requesterUser = User::find($result->requester->user_id ?? null);
            if ($requesterUser) {
                $requesterUser->notify(new SystemNotification(
                    'Logistik Perjalanan Telah Diinput',
                    "Tiket ({$request->transport_type}) telah disiapkan oleh Admin Travel dan sedang diverifikasi Finance.",
                    'info'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Data logistik dan anggaran terstandarisasi berhasil disimpan untuk diverifikasi Finance.',
                'data'    => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // 5. Kembalikan / Tolak Pengajuan jika ada kendala di tahap Logistik
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        try {
            $result = $this->logisticsService->rejectLogistics(
                $id,
                $request->user(),
                $validated['rejection_reason'],
                $request->ip(),
                $request->userAgent()
            );

            $requesterUser = User::find($result->requester->user_id ?? null);
            if ($requesterUser) {
                $requesterUser->notify(new SystemNotification(
                    'Kendala Pengadaan Logistik',
                    'Catatan Admin Travel: ' . $validated['rejection_reason'],
                    'error'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan dikembalikan ke karyawan beserta catatan logistik.',
                'data'    => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}