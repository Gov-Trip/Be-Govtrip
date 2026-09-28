<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogisticsRequest;
use App\Services\TravelLogisticsService;
use App\Models\TravelRequest;
use App\Models\Employee;
use App\Models\User;
use App\Models\AuditLog;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Exception;

class TravelLogisticsController extends Controller
{
    protected TravelLogisticsService $logisticsService;

    public function __construct(TravelLogisticsService $logisticsService) 
    {
        $this->logisticsService = $logisticsService;
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'Disetujui');

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
        // 1. Hitung Angka Summary (4 Kotak di atas)
        
        // Total Pegawai Aktif (Lebih akurat menghitung dari tabel employees)
        $totalPegawai = Employee::count();

        // Perjalanan Berjalan (Hari Ini)
        $perjalananBerjalanCount = TravelRequest::where('status', 'Selesai')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();

        // SPPD Terbit (Bulan Ini)
        $sppdTerbitCount = TravelRequest::whereNotNull('sppd_number')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Dokumen Belum Lengkap (Misal: Status masih di 'Disetujui' dan butuh input tiket)
        $dokumenBelumLengkapCount = TravelRequest::where('status', 'Disetujui')->count();


        // 2. Ambil List Data
        
        // List Perjalanan Berjalan (Ambil 5 teratas)
        $perjalananBerjalanList = TravelRequest::with(['requester', 'destinationCity'])
            ->where('status', 'Selesai')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->take(5)
            ->get();

        // List Dokumen Perlu Tindak Lanjut (Antrean tugas Admin Travel)
        $tindakLanjutList = TravelRequest::with(['requester'])
            ->where('status', 'Disetujui') // Butuh dicarikan tiket
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_pegawai' => $totalPegawai,
                    'perjalanan_berjalan' => $perjalananBerjalanCount,
                    'sppd_terbit_bulan_ini' => $sppdTerbitCount,
                    'dokumen_belum_lengkap' => $dokumenBelumLengkapCount,
                ],
                'perjalanan_berjalan_list' => $perjalananBerjalanList,
                'tindak_lanjut_list' => $tindakLanjutList
            ]
        ]);
    }

    public function show($id)
    {
        $travelRequest = TravelRequest::with([
            'requester.department', 
            'originCity', 
            'destinationCity',
            'approvals.approver' 
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $travelRequest
        ]);
    }

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

            // PERBAIKAN PENCARIAN NOTIFIKASI FINANCE
            $financeEmployees = Employee::whereHas('department', function($q) {
                $q->where('code', 'FINANCE');
            })->get();

            // Ambil ID User dari pegawai Finance
            $financeUserIds = $financeEmployees->pluck('user_id')->filter();
            $timFinance = User::whereIn('id', $financeUserIds)->get();

            if ($timFinance->isNotEmpty()) {
                Notification::send($timFinance, new SystemNotification(
                    "Laporan Menunggu Verifikasi (Uang Muka)",
                    "SPPD untuk {$result->requester->name} siap diproses keuangannya.",
                    "warning"
                ));
            }

            return response()->json([
                'success' => true, 
                'message' => 'Data logistik dan tiket berhasil disimpan. Status berubah menjadi Siap Berangkat.', 
                'data'    => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function generateSppd(Request $request, $id)
    {
        try {
            $travelRequest = TravelRequest::findOrFail($id);

            if ($travelRequest->sppd_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'SPPD sudah diterbitkan: ' . $travelRequest->sppd_number
                ], 400);
            }

            // Gunakan format angka standar (Misal: 090/087/SPPD/09/2026)
            $bulan = now()->format('m'); 
            $tahun = now()->year;        
            
            $nomorUrut = str_pad($travelRequest->id, 3, '0', STR_PAD_LEFT); 
            $sppdNumber = "090/{$nomorUrut}/SPPD/{$bulan}/{$tahun}";

            $travelRequest->sppd_number = $sppdNumber;
            // $travelRequest->status = 'Menunggu Uang Muka'; // Jika alurnya mengubah status
            $travelRequest->save();

            AuditLog::create([
                'travel_request_id' => $travelRequest->id,
                'user_id'           => $request->user()->id,
                'action'            => 'GENERATE_SPPD',
                'description'       => "SPPD diterbitkan dengan nomor {$sppdNumber}"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nomor SPPD berhasil diterbitkan.',
                'data' => [
                    'sppd_number' => $sppdNumber
                ]
            ]);

        } catch (Exception $e) { 
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}