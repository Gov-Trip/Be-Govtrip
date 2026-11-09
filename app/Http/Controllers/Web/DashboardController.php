<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\RefCity;
use App\Models\TravelRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // ==========================================
    // PEGAWAI
    // ==========================================

    public function pegawaiDashboard(): View
    {
        $user = Auth::user();
        $userName = $user?->employee?->name ?? 'Andi Pratama';
        $totalTrips = TravelRequest::count();
        $pendingCount = TravelRequest::where('status', 'like', '%Menunggu%')->count();
        $sppdCount = TravelRequest::whereNotNull('sppd_number')->count();

        $recentTrips = TravelRequest::with('destinationCity')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($t) {
                return [
                    'number' => $t->sppd_number ?? ('PD-'.str_pad($t->id, 3, '0', STR_PAD_LEFT)),
                    'destination' => $t->destinationCity?->city_name ?? 'Yogyakarta',
                    'dates' => date('d M', strtotime($t->start_date)).'–'.date('d M', strtotime($t->end_date)),
                    'status' => $t->status,
                ];
            })
            ->toArray();

        return view('pegawai.dashboard', compact('userName', 'totalTrips', 'pendingCount', 'sppdCount', 'recentTrips'));
    }

    public function pegawaiPengajuan(): View
    {
        $cities = RefCity::all();

        return view('pegawai.pengajuan', compact('cities'));
    }

    public function pegawaiStorePengajuan(Request $request): RedirectResponse
    {
        $request->validate([
            'destination_city_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string',
        ]);

        $employee = Auth::user()?->employee ?? Employee::first();

        $travel = TravelRequest::create([
            'requester_employee_id' => $employee?->id ?? 1,
            'origin_city_id' => 1,
            'destination_city_id' => $request->destination_city_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'purpose' => $request->purpose,
            'estimated_total_cost' => (int) $request->input('cost_transport', 900000) + (int) $request->input('cost_accommodation', 1600000) + (int) $request->input('cost_daily', 1350000),
            'status' => 'Menunggu Persetujuan Atasan',
        ]);

        return redirect('/pegawai/perjalanan')->with('success', 'Pengajuan perjalanan dinas berhasil dikirim ke atasan langsung.');
    }

    public function pegawaiPerjalanan(): View
    {
        $trips = TravelRequest::with('destinationCity')->latest()->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'number' => 'PD-2026/'.str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'sppd' => $t->sppd_number,
                'destination' => $t->destinationCity?->city_name ?? 'Yogyakarta',
                'dates' => date('d M', strtotime($t->start_date)).'–'.date('d M Y', strtotime($t->end_date)),
                'transport' => 'Kereta api / Pesawat',
                'status' => $t->status,
                'purpose' => $t->purpose,
            ];
        })->toArray();

        return view('pegawai.perjalanan', compact('trips'));
    }

    public function pegawaiSppd(): View
    {
        return view('pegawai.sppd');
    }

    public function pegawaiLpj(): View
    {
        return view('pegawai.lpj');
    }

    public function pegawaiStoreLpj(Request $request): RedirectResponse
    {
        return back()->with('success', 'Laporan Pertanggungjawaban (LPJ) berhasil diunggah dan diteruskan ke Bendahara Keuangan.');
    }

    public function pegawaiDraft(): View
    {
        return view('pegawai.draft');
    }

    public function pegawaiNotifikasi(): View
    {
        return view('pegawai.notifikasi');
    }

    public function pegawaiProfil(): View
    {
        return view('pegawai.profil');
    }

    // ==========================================
    // ATASAN
    // ==========================================

    public function atasanDashboard(): View
    {
        $userName = Auth::user()?->employee?->name ?? 'Bapak Kepala Bidang';

        return view('atasan.dashboard', compact('userName'));
    }

    public function atasanInbox(): View
    {
        return view('atasan.inbox');
    }

    public function atasanDecision(Request $request): RedirectResponse
    {
        $decision = $request->input('decision', 'Disetujui');

        return redirect('/atasan/riwayat')->with('success', "Keputusan berhasil disimpan: {$decision}");
    }

    public function atasanTim(): View
    {
        return view('atasan.tim');
    }

    public function atasanRiwayat(): View
    {
        return view('atasan.riwayat');
    }

    public function atasanNotifikasi(): View
    {
        return view('atasan.notifikasi');
    }

    // ==========================================
    // BENDAHARA
    // ==========================================

    public function bendaharaDashboard(): View
    {
        $userName = Auth::user()?->employee?->name ?? 'Ibu Bendahara';

        return view('bendahara.dashboard', compact('userName'));
    }

    public function bendaharaVerifikasi(): View
    {
        return view('bendahara.verifikasi');
    }

    public function bendaharaVerify(Request $request): RedirectResponse
    {
        $action = $request->input('action', 'APPROVE');
        $msg = $action === 'APPROVE' ? 'LPJ berhasil diverifikasi & disahkan.' : 'LPJ dikembalikan untuk perbaikan.';

        return redirect('/bendahara/verifikasi')->with('success', $msg);
    }

    public function bendaharaPencairan(): View
    {
        return view('bendahara.pencairan');
    }

    public function bendaharaAnggaran(): View
    {
        return view('bendahara.anggaran');
    }

    public function bendaharaLogistik(): View
    {
        return view('bendahara.logistik');
    }

    public function bendaharaLpj(): View
    {
        return view('bendahara.lpj');
    }

    public function bendaharaRiwayat(): View
    {
        return view('bendahara.riwayat');
    }

    public function bendaharaNotifikasi(): View
    {
        return view('bendahara.notifikasi');
    }

    // ==========================================
    // ADMIN TRAVEL
    // ==========================================

    public function adminDashboard(): View
    {
        $userName = Auth::user()?->employee?->name ?? 'Admin Travel';

        return view('admin.dashboard', compact('userName'));
    }

    public function adminPegawai(): View
    {
        $employees = Employee::with('department')->get();

        return view('admin.pegawai', compact('employees'));
    }

    public function adminPerjalanan(): View
    {
        return view('admin.perjalanan');
    }

    public function adminStoreLogistics(Request $request): RedirectResponse
    {
        return redirect('/admin/sppd')->with('success', 'Tiket & logistik berhasil disimpan. SPPD digital telah diterbitkan.');
    }

    public function adminDokumen(): View
    {
        return view('admin.dokumen');
    }

    public function adminSppd(): View
    {
        return view('admin.sppd');
    }

    public function adminMonitoring(): View
    {
        return view('admin.monitoring');
    }

    public function adminNotifikasi(): View
    {
        return view('admin.notifikasi');
    }

    // ==========================================
    // SUPER ADMIN
    // ==========================================

    public function superadminDashboard(): View
    {
        $userName = Auth::user()?->employee?->name ?? 'Pak Super Admin';

        return view('superadmin.dashboard', compact('userName'));
    }

    public function superadminPengguna(): View
    {
        return view('superadmin.pengguna');
    }

    public function superadminAkses(): View
    {
        return view('superadmin.akses');
    }

    public function superadminKonfigurasi(): View
    {
        return view('superadmin.konfigurasi');
    }

    public function superadminAudit(): View
    {
        return view('superadmin.audit');
    }

    public function superadminUnit(): View
    {
        return view('superadmin.unit');
    }

    public function superadminNotifikasi(): View
    {
        return view('superadmin.notifikasi');
    }
}
