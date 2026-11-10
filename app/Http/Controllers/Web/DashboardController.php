<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ExpenseReport;
use App\Models\RefCity;
use App\Models\TravelAttachment;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use App\Services\ApprovalService;
use App\Services\FinanceService;
use App\Services\PermissionMatrixService;
use App\Services\TravelLogisticsService;
use App\Services\TravelRequestService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected TravelRequestService $travelService,
        protected ApprovalService $approvalService,
        protected TravelLogisticsService $logisticsService,
        protected FinanceService $financeService,
        protected PermissionMatrixService $matrixService
    ) {}

    /**
     * Resolve the active user or a representative seeded user for demo access.
     */
    protected function currentUser(string $fallbackRole = 'Pegawai'): User
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $emailMap = [
            'Pegawai' => 'karyawan@gov.trip',
            'Atasan' => 'atasan@gov.trip',
            'Kabid' => 'manager@gov.trip',
            'Kadis' => 'kadis@gov.trip',
            'Bendahara' => 'bendahara@gov.trip',
            'Admin' => 'admintravel@gov.trip',
            'Super Admin' => 'admin@gov.trip',
        ];

        $targetEmail = $emailMap[$fallbackRole] ?? 'karyawan@gov.trip';

        $user = User::where('email', $targetEmail)->first() ?? User::first();
        if (! $user) {
            $user = User::firstOrCreate(
                ['email' => $targetEmail],
                ['password' => Hash::make('password123'), 'is_active' => true]
            );

            if (! $user->employee) {
                $deptCode = match ($fallbackRole) {
                    'Super Admin' => 'SUPER_ADMIN',
                    'Bendahara' => 'FINANCE',
                    'Admin' => 'ADMIN_TRAVEL',
                    default => 'PROGRAM',
                };
                $dept = Department::firstOrCreate(['code' => $deptCode], ['name' => $deptCode]);
                Employee::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'department_id' => $dept->id,
                        'nip' => 'NIP-'.strtoupper(substr(uniqid(), 0, 8)),
                        'name' => match ($fallbackRole) {
                            'Super Admin' => 'Pak Super Admin',
                            'Bendahara' => 'Ibu Bendahara',
                            'Admin' => 'Admin Travel',
                            'Kadis' => 'Bapak Kepala Dinas',
                            'Kabid' => 'Bapak Kepala Bidang',
                            'Atasan' => 'Ibu Kepala Seksi',
                            default => 'Andi Pratama',
                        },
                        'position' => match ($fallbackRole) {
                            'Kadis' => 'Kepala Dinas',
                            'Kabid' => 'Kepala Bidang Program',
                            'Atasan' => 'Kepala Seksi Perencanaan',
                            default => 'Staf Pegawai',
                        },
                        'grade' => match ($fallbackRole) {
                            'Kadis' => 'Director',
                            'Kabid' => 'Manager',
                            'Atasan' => 'Supervisor',
                            default => 'Staff',
                        },
                    ]
                );
                $user->load('employee.department');
            }
        }

        return $user;
    }

    // ==========================================
    // MODUL PEGAWAI
    // ==========================================

    public function pegawaiDashboard(): View
    {
        $user = $this->currentUser('Pegawai');
        $employee = $user->employee;
        $userName = $employee?->name ?? 'Andi Pratama';

        $empId = $employee?->id;

        $totalTrips = TravelRequest::when($empId, fn ($q) => $q->where('requester_employee_id', $empId))->count();
        $pendingCount = TravelRequest::when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->whereIn('status', ['Menunggu Persetujuan Atasan', 'Menunggu Persetujuan', 'Disetujui', 'Menunggu Verifikasi LPJ'])
            ->count();
        $sppdCount = TravelRequest::when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->whereNotNull('sppd_number')
            ->count();
        $revisiCount = TravelRequest::when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->whereIn('status', ['Direvisi', 'Revisi', 'Revisi LPJ'])
            ->count();

        $activeTrip = TravelRequest::with(['destinationCity', 'approvals', 'logistics'])
            ->when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->whereIn('status', ['Menunggu Persetujuan Atasan', 'Disetujui', 'Siap Berangkat', 'Menunggu Verifikasi LPJ'])
            ->latest()
            ->first();

        $recentTrips = TravelRequest::with(['destinationCity', 'originCity'])
            ->when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'number' => $t->sppd_number ?? ('PD-'.strtoupper(substr($t->id, 0, 8))),
                    'destination' => $t->destinationCity?->city_name ?? 'Kota Tujuan',
                    'dates' => Carbon::parse($t->start_date)->translatedFormat('d M').'–'.Carbon::parse($t->end_date)->translatedFormat('d M Y'),
                    'status' => $t->status,
                ];
            })
            ->toArray();

        return view('pegawai.dashboard', compact('userName', 'totalTrips', 'pendingCount', 'sppdCount', 'revisiCount', 'recentTrips', 'activeTrip'));
    }

    public function pegawaiPengajuan(): View
    {
        $user = $this->currentUser('Pegawai');
        $cities = RefCity::orderBy('city_name')->get();
        $employees = Employee::with('department')->orderBy('name')->get();
        $policies = TravelPolicy::with('destinationCity')->get();

        return view('pegawai.pengajuan', compact('cities', 'employees', 'policies', 'user'));
    }

    public function pegawaiStorePengajuan(Request $request): RedirectResponse
    {
        $request->validate([
            'destination_city_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string',
        ]);

        $user = $this->currentUser('Pegawai');
        $originCity = RefCity::first();

        $assigneeIds = $request->input('assignee_ids', []);
        if (is_string($assigneeIds)) {
            $assigneeIds = array_filter(explode(',', $assigneeIds));
        }

        $data = [
            'origin_city_id' => $request->input('origin_city_id', $originCity?->id ?? 1),
            'destination_city_id' => $request->destination_city_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'purpose' => $request->purpose,
            'assignee_ids' => $assigneeIds,
        ];

        try {
            $travelRequest = $this->travelService->createTravelRequest(
                $user,
                $data,
                $request->ip(),
                $request->userAgent()
            );

            // Upload Dokumen Pendukung (Surat Undangan / Nota Dinas / Telaahan Staf)
            if ($request->hasFile('attachment_document')) {
                $file = $request->file('attachment_document');
                $path = $file->store('attachments', 'public');
                TravelAttachment::create([
                    'travel_request_id' => $travelRequest->id,
                    'file_type' => 'SURAT_TUGAS',
                    'file_path' => $path,
                ]);
            }

            // Periksa apakah pengguna memilih untuk langsung mengirim (Kirim Pengajuan)
            if ($request->input('action_type') === 'submit') {
                $submitted = $this->travelService->submitToAtasan(
                    $travelRequest->id,
                    $user,
                    $request->ip(),
                    $request->userAgent()
                );

                if ($submitted->status === 'Disetujui') {
                    return redirect()->route('pegawai.perjalanan')->with(
                        'success',
                        "Pengajuan berhasil dikirim dan OTOMATIS DISETUJUI via Hak Akses Bypass Matriks Super Admin! Nomor SPPD diterbitkan: {$submitted->sppd_number}."
                    );
                }

                return redirect()->route('pegawai.perjalanan')->with(
                    'success',
                    'Pengajuan perjalanan dinas berhasil dikirim ke Atasan Langsung untuk diverifikasi.'
                );
            }

            return redirect()->route('pegawai.perjalanan')->with(
                'success',
                'Draft pengajuan perjalanan dinas berhasil disimpan.'
            );
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan pengajuan: '.$e->getMessage());
        }
    }

    public function pegawaiSubmitPengajuan(Request $request, string $id): RedirectResponse
    {
        $user = $this->currentUser('Pegawai');

        try {
            $submitted = $this->travelService->submitToAtasan(
                $id,
                $user,
                $request->ip(),
                $request->userAgent()
            );

            if ($submitted->status === 'Disetujui') {
                return redirect()->route('pegawai.perjalanan')->with(
                    'success',
                    "Pengajuan berhasil diajukan dan OTOMATIS DISETUJUI via Hak Akses Bypass Super Admin! Nomor SPPD: {$submitted->sppd_number}"
                );
            }

            return redirect()->route('pegawai.perjalanan')->with(
                'success',
                'Pengajuan berhasil dikirim ke Atasan Langsung untuk diverifikasi.'
            );
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengirim pengajuan: '.$e->getMessage());
        }
    }

    public function pegawaiEditPengajuan(string $id): View
    {
        $user = $this->currentUser('Pegawai');
        $travel = TravelRequest::with(['assignees.employee', 'destinationCity', 'originCity', 'attachments'])->findOrFail($id);
        $cities = RefCity::orderBy('city_name')->get();
        $employees = Employee::with('department')->orderBy('name')->get();

        return view('pegawai.pengajuan', compact('travel', 'cities', 'employees', 'user'));
    }

    public function pegawaiUpdatePengajuan(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'destination_city_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string',
        ]);

        $user = $this->currentUser('Pegawai');
        $assigneeIds = $request->input('assignee_ids', []);
        if (is_string($assigneeIds)) {
            $assigneeIds = array_filter(explode(',', $assigneeIds));
        }

        $data = [
            'origin_city_id' => $request->input('origin_city_id', 1),
            'destination_city_id' => $request->destination_city_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'purpose' => $request->purpose,
            'assignee_ids' => $assigneeIds,
        ];

        try {
            $updated = $this->travelService->updateTravelRequest(
                $id,
                $user,
                $data,
                $request->ip(),
                $request->userAgent()
            );

            // Upload Dokumen Pendukung Baru jika ada perubahan lampiran
            if ($request->hasFile('attachment_document')) {
                $file = $request->file('attachment_document');
                $path = $file->store('attachments', 'public');
                TravelAttachment::create([
                    'travel_request_id' => $updated->id,
                    'file_type' => 'SURAT_TUGAS',
                    'file_path' => $path,
                ]);
            }

            if ($request->input('action_type') === 'submit') {
                $submitted = $this->travelService->submitToAtasan(
                    $id,
                    $user,
                    $request->ip(),
                    $request->userAgent()
                );

                if ($submitted->status === 'Disetujui') {
                    return redirect()->route('pegawai.perjalanan')->with(
                        'success',
                        "Perubahan berhasil disimpan & OTOMATIS DISETUJUI via Fast-Track Bypass Super Admin! Nomor SPPD: {$submitted->sppd_number}"
                    );
                }

                return redirect()->route('pegawai.perjalanan')->with(
                    'success',
                    'Pengajuan revisi berhasil diajukan ulang ke Atasan Langsung.'
                );
            }

            return redirect()->route('pegawai.perjalanan')->with('success', 'Data pengajuan berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui pengajuan: '.$e->getMessage());
        }
    }

    public function pegawaiPerjalanan(): View
    {
        $user = $this->currentUser('Pegawai');
        $empId = $user->employee?->id;

        $trips = TravelRequest::with(['destinationCity', 'originCity', 'assignees.employee', 'logistics', 'expenseReport', 'attachments', 'approvals.approver'])
            ->when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->latest()
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'number' => 'PD-2026/'.strtoupper(substr($t->id, 0, 6)),
                    'sppd' => $t->sppd_number,
                    'destination' => $t->destinationCity?->city_name ?? 'Kota Tujuan',
                    'dates' => Carbon::parse($t->start_date)->translatedFormat('d M').'–'.Carbon::parse($t->end_date)->translatedFormat('d M Y'),
                    'transport' => $t->logistics?->transport_type ?? 'Kereta api / Pesawat',
                    'status' => $t->status,
                    'purpose' => $t->purpose,
                    'rejection_reason' => $t->rejection_reason,
                    'advance_payment' => (float) $t->advance_payment,
                    'estimated_total_cost' => (float) $t->estimated_total_cost,
                    'assignees_count' => $t->assignees->count(),
                    'attachment_name' => $t->attachments->first() ? basename($t->attachments->first()->file_path) : null,
                    'attachment_url' => $t->attachments->first() ? asset('storage/'.$t->attachments->first()->file_path) : null,
                ];
            })
            ->toArray();

        return view('pegawai.perjalanan', compact('trips'));
    }

    public function pegawaiSppd(): View
    {
        $user = $this->currentUser('Pegawai');
        $empId = $user->employee?->id;

        $sppdList = TravelRequest::with(['destinationCity', 'logistics', 'requester'])
            ->when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->whereNotNull('sppd_number')
            ->latest()
            ->get();

        return view('pegawai.sppd', compact('sppdList'));
    }

    public function pegawaiLpj(): View
    {
        $user = $this->currentUser('Pegawai');
        $empId = $user->employee?->id;

        // Ambil perjalanan yang siap buat LPJ atau sedang dalam tahap LPJ
        $eligibleTrips = TravelRequest::with(['destinationCity', 'logistics', 'expenseReport'])
            ->when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->whereIn('status', ['Siap Berangkat', 'Revisi LPJ', 'Menunggu Verifikasi LPJ', 'Selesai'])
            ->latest()
            ->get();

        $selectedTrip = $eligibleTrips->firstWhere('status', 'Siap Berangkat')
            ?? $eligibleTrips->firstWhere('status', 'Revisi LPJ')
            ?? $eligibleTrips->first();

        return view('pegawai.lpj', compact('eligibleTrips', 'selectedTrip'));
    }

    public function pegawaiStoreLpj(Request $request): RedirectResponse
    {
        $request->validate([
            'travel_request_id' => 'required',
            'total_real_expense' => 'required|numeric|min:0',
            'activity_summary' => 'required|string',
        ]);

        $user = $this->currentUser('Pegawai');
        $travel = TravelRequest::findOrFail($request->travel_request_id);

        if (! in_array($travel->status, ['Siap Berangkat', 'Revisi LPJ', 'Menunggu Verifikasi LPJ'])) {
            return back()->with('error', "Perjalanan berstatus {$travel->status} tidak dapat mengunggah LPJ.");
        }

        $advance = (float) ($travel->advance_payment > 0 ? $travel->advance_payment : $travel->estimated_total_cost);
        $real = (float) $request->total_real_expense;
        $balance = $advance - $real;

        DB::beginTransaction();
        try {
            ExpenseReport::updateOrCreate(
                ['travel_request_id' => $travel->id],
                [
                    'employee_id' => $travel->requester_employee_id,
                    'total_advance' => $advance,
                    'total_real_expense' => $real,
                    'balance_amount' => $balance,
                    'activity_summary' => $request->activity_summary,
                    'status' => 'SUBMITTED',
                ]
            );

            $travel->update([
                'status' => 'Menunggu Verifikasi LPJ',
                'rejection_reason' => null,
            ]);

            AuditLog::create([
                'travel_request_id' => $travel->id,
                'user_id' => $user->id,
                'action' => 'SUBMIT_LPJ',
                'module' => 'EXPENSE_REPORT',
                'description' => 'LPJ diserahkan ke Bendahara Keuangan. Realisasi pengeluaran: Rp '.number_format($real, 0, ',', '.').', Sisa: Rp '.number_format($balance, 0, ',', '.'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('pegawai.perjalanan')->with(
                'success',
                'Laporan Pertanggungjawaban (LPJ) berhasil diunggah dan diteruskan ke Bendahara Keuangan.'
            );
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan LPJ: '.$e->getMessage());
        }
    }

    public function pegawaiDraft(): View
    {
        $user = $this->currentUser('Pegawai');
        $empId = $user->employee?->id;

        $drafts = TravelRequest::with(['destinationCity', 'assignees.employee'])
            ->when($empId, fn ($q) => $q->where('requester_employee_id', $empId))
            ->whereIn('status', ['Draft', 'draft'])
            ->latest()
            ->get();

        return view('pegawai.draft', compact('drafts'));
    }

    public function pegawaiNotifikasi(): View
    {
        $user = $this->currentUser('Pegawai');
        $logs = AuditLog::with('travelRequest')
            ->where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        if ($logs->isEmpty()) {
            $logs = AuditLog::with('travelRequest')->latest()->take(5)->get();
        }

        $notifications = $this->formatNotifications($logs);

        return view('pegawai.notifikasi', compact('notifications'));
    }

    public function pegawaiProfil(): View
    {
        $user = $this->currentUser('Pegawai');
        $employee = $user->employee;

        return view('pegawai.profil', compact('user', 'employee'));
    }

    // ==========================================
    // MODUL ATASAN
    // ==========================================

    public function atasanDashboard(): View
    {
        $user = $this->currentUser('Atasan');
        $userName = $user->employee?->name ?? 'Bapak Kepala Bidang';

        $pendingApprovals = TravelRequest::whereIn('status', [
            'Menunggu Persetujuan Atasan',
            'Menunggu Approval Atasan',
            'Menunggu Atasan',
            'Menunggu Persetujuan Kabid',
            'Menunggu Persetujuan Kadis',
            'Menunggu Persetujuan',
        ])->count();
        $approvedCount = TravelRequest::whereIn('status', ['Disetujui', 'Siap Berangkat', 'Selesai'])->count();
        $teamCount = Employee::where('department_id', $user->employee?->department_id)->count();

        // Anggota sedang dinas
        $activeTrips = TravelRequest::with(['requester', 'destinationCity'])
            ->whereIn('status', ['Siap Berangkat', 'Disetujui'])
            ->latest()
            ->take(3)
            ->get();
        $activeDinasCount = $activeTrips->count();
        $activeNames = $activeTrips->map(fn ($t) => $t->requester?->name)->filter()->implode(', ');
        if (empty($activeNames)) {
            $activeNames = 'Tidak ada penugasan aktif';
        }

        // Pagu dan serapan anggaran tim
        $totalPagu = TravelRequest::sum('estimated_total_cost');
        $realizedPagu = ExpenseReport::where('status', 'VERIFIED')->sum('total_real_expense');
        $pctPagu = $totalPagu > 0 ? round(($realizedPagu / $totalPagu) * 100, 1) : 0;

        $recentPending = TravelRequest::with(['requester', 'destinationCity'])
            ->whereIn('status', [
                'Menunggu Persetujuan Atasan',
                'Menunggu Approval Atasan',
                'Menunggu Atasan',
                'Menunggu Persetujuan Kabid',
                'Menunggu Persetujuan Kadis',
                'Menunggu Persetujuan',
            ])
            ->latest()
            ->take(5)
            ->get();

        return view('atasan.dashboard', compact(
            'userName', 'pendingApprovals', 'approvedCount', 'teamCount', 'recentPending',
            'activeDinasCount', 'activeNames', 'totalPagu', 'realizedPagu', 'pctPagu'
        ));
    }

    public function atasanInbox(): View
    {
        $user = $this->currentUser('Atasan');
        $roleLabel = $this->matrixService->resolveUserRoleLabel($user);

        $pendingRequests = TravelRequest::with(['requester.department', 'destinationCity', 'originCity', 'assignees.employee', 'attachments', 'approvals.approver'])
            ->whereIn('status', [
                'Menunggu Persetujuan Atasan',
                'Menunggu Approval Atasan',
                'Menunggu Atasan',
                'Menunggu Persetujuan Kabid',
                'Menunggu Persetujuan Kadis',
                'Menunggu Persetujuan',
            ])
            ->latest()
            ->get();

        return view('atasan.inbox', compact('pendingRequests', 'user', 'roleLabel'));
    }

    public function atasanDecision(Request $request): RedirectResponse
    {
        $request->validate([
            'submission_id' => 'required',
            'decision' => 'required',
        ]);

        $user = $this->currentUser('Atasan');
        $decision = $request->input('decision');

        // Normalisasi teks keputusan
        if (in_array(strtolower($decision), ['perlu revisi', 'minta revisi', 'revisi', 'direvisi'])) {
            $mappedDecision = 'Direvisi';
        } elseif (in_array(strtolower($decision), ['tolak', 'ditolak', 'reject'])) {
            $mappedDecision = 'Ditolak';
        } else {
            $mappedDecision = 'Disetujui';
        }

        try {
            $res = $this->approvalService->processApproval(
                $request->submission_id,
                $user,
                [
                    'decision' => $mappedDecision,
                    'notes' => $request->notes,
                    'final_approval' => $request->boolean('final_approval'),
                ],
                $request->ip(),
                $request->userAgent()
            );

            $msg = match ($mappedDecision) {
                'Disetujui' => match ($res->status) {
                    'Menunggu Persetujuan Kabid' => 'Pengajuan berhasil disetujui Atasan Langsung dan diteruskan ke Kepala Bidang.',
                    'Menunggu Persetujuan Kadis' => 'Pengajuan berhasil disetujui Kepala Bidang dan diteruskan ke Kepala Dinas.',
                    default => "Pengajuan berhasil disetujui final. Nomor SPPD Digital ({$res->sppd_number}) otomatis diterbitkan.",
                },
                'Ditolak' => 'Pengajuan berhasil ditolak dengan catatan alasan penolakan.',
                default => 'Pengajuan berhasil dikembalikan ke Pegawai untuk perbaikan/revisi.',
            };

            return redirect()->route('atasan.inbox')->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses keputusan: '.$e->getMessage());
        }
    }

    public function atasanTim(): View
    {
        $user = $this->currentUser('Atasan');
        $deptId = $user->employee?->department_id;
        $team = Employee::with([
            'department',
            'travelRequests' => fn ($q) => $q->latest()->with(['destinationCity', 'logistics']),
        ])->when($deptId, fn ($q) => $q->where('department_id', $deptId))->get();

        return view('atasan.tim', compact('team'));
    }

    public function atasanRiwayat(): View
    {
        $user = $this->currentUser('Atasan');
        $history = TravelRequest::with(['requester.department', 'destinationCity', 'approvals.approver'])
            ->whereIn('status', ['Disetujui', 'Direvisi', 'Ditolak', 'Siap Berangkat', 'Selesai'])
            ->latest()
            ->paginate(15);

        return view('atasan.riwayat', compact('history'));
    }

    public function atasanNotifikasi(): View
    {
        $logs = AuditLog::with('travelRequest')->latest()->take(20)->get();
        $notifications = $this->formatNotifications($logs);

        return view('atasan.notifikasi', compact('notifications'));
    }

    // ==========================================
    // MODUL BENDAHARA (FINANCE)
    // ==========================================

    public function bendaharaDashboard(): View
    {
        $user = $this->currentUser('Bendahara');
        $userName = $user->employee?->name ?? 'Ibu Bendahara';

        $pendingLpj = TravelRequest::where('status', 'Menunggu Verifikasi LPJ')->count();
        $verifiedLpj = TravelRequest::where('status', 'Selesai')->count();
        $totalAdvance = TravelRequest::sum('advance_payment');
        $totalRealExpense = ExpenseReport::where('status', 'VERIFIED')->sum('total_real_expense');
        $totalPagu = max(350000000, (float) ($totalAdvance + $totalRealExpense));
        $sisaPagu = max(0, $totalPagu - $totalRealExpense);
        $pctPagu = $totalPagu > 0 ? round(($totalRealExpense / $totalPagu) * 100, 1) : 0;

        $recentVerifications = TravelRequest::with(['requester', 'destinationCity', 'expenseReport'])
            ->whereIn('status', ['Menunggu Verifikasi LPJ', 'Selesai', 'Revisi LPJ'])
            ->latest()
            ->take(5)
            ->get();

        return view('bendahara.dashboard', compact(
            'userName', 'pendingLpj', 'verifiedLpj', 'totalAdvance', 'totalRealExpense',
            'totalPagu', 'sisaPagu', 'pctPagu', 'recentVerifications'
        ));
    }

    public function bendaharaVerifikasi(): View
    {
        $user = $this->currentUser('Bendahara');
        $queue = TravelRequest::with(['requester.department', 'destinationCity', 'logistics', 'expenseReport'])
            ->where('status', 'Menunggu Verifikasi LPJ')
            ->latest()
            ->get();

        return view('bendahara.verifikasi', compact('queue', 'user'));
    }

    public function bendaharaVerify(Request $request): RedirectResponse
    {
        $request->validate([
            'lpj_id' => 'required',
            'action' => 'required',
        ]);

        $user = $this->currentUser('Bendahara');
        $isValid = strtoupper($request->action) === 'APPROVE';

        try {
            $this->financeService->verifyExpense(
                $request->lpj_id,
                $user,
                [
                    'is_valid' => $isValid,
                    'notes' => $request->notes,
                    'advance_payment' => $request->advance_payment,
                ],
                $request->ip(),
                $request->userAgent()
            );

            $msg = $isValid
                ? 'LPJ berhasil diverifikasi & disahkan. Status perjalanan resmi SELESAI.'
                : 'LPJ dikembalikan ke Pegawai dengan catatan revisi.';

            return redirect()->route('bendahara.verifikasi')->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses verifikasi: '.$e->getMessage());
        }
    }

    public function bendaharaPencairan(): View
    {
        $disbursements = TravelRequest::with(['requester', 'destinationCity', 'expenseReport'])
            ->whereIn('status', ['Siap Berangkat', 'Selesai'])
            ->latest()
            ->get();

        return view('bendahara.pencairan', compact('disbursements'));
    }

    public function bendaharaAnggaran(): View
    {
        $policies = TravelPolicy::with('destinationCity')->get();
        $totalBudget = TravelRequest::sum('estimated_total_cost');
        $totalPagu = max(350000000, (float) $totalBudget);
        $realizedBudget = ExpenseReport::where('status', 'VERIFIED')->sum('total_real_expense');
        $totalAdvance = TravelRequest::whereIn('status', ['Siap Berangkat', 'Menunggu Verifikasi LPJ'])->sum('advance_payment');

        $deptBreakdown = Department::with(['employees.travelRequests'])->get()->map(function ($dept) {
            $realisasi = 0;
            foreach ($dept->employees as $emp) {
                $realisasi += $emp->travelRequests->where('status', 'Selesai')->sum('estimated_total_cost');
            }
            $pagu = 100000000;
            $sisa = max(0, $pagu - $realisasi);
            $pct = $pagu > 0 ? round(($realisasi / $pagu) * 100, 1) : 0;

            return [
                'name' => $dept->name,
                'pagu' => $pagu,
                'realisasi' => $realisasi,
                'sisa' => $sisa,
                'pct' => $pct,
            ];
        });

        return view('bendahara.anggaran', compact('policies', 'totalPagu', 'totalBudget', 'realizedBudget', 'totalAdvance', 'deptBreakdown'));
    }

    public function bendaharaLogistik(): View
    {
        $logistics = TravelRequest::with(['requester', 'destinationCity', 'logistics'])
            ->whereIn('status', ['Siap Berangkat', 'Menunggu Verifikasi LPJ', 'Selesai'])
            ->latest()
            ->get();

        return view('bendahara.logistik', compact('logistics'));
    }

    public function bendaharaLpj(): View
    {
        $lpjList = ExpenseReport::with(['travelRequest.destinationCity', 'employee'])->latest()->get();

        return view('bendahara.lpj', compact('lpjList'));
    }

    public function bendaharaRiwayat(): View
    {
        $history = TravelRequest::with(['requester', 'destinationCity', 'expenseReport'])
            ->whereIn('status', ['Selesai', 'Revisi LPJ'])
            ->latest()
            ->paginate(15);

        return view('bendahara.riwayat', compact('history'));
    }

    public function bendaharaNotifikasi(): View
    {
        $logs = AuditLog::with('travelRequest')->latest()->take(20)->get();
        $notifications = $this->formatNotifications($logs);

        return view('bendahara.notifikasi', compact('notifications'));
    }

    // ==========================================
    // MODUL ADMIN TRAVEL
    // ==========================================

    public function adminDashboard(): View
    {
        $user = $this->currentUser('Admin');
        $userName = $user->employee?->name ?? 'Admin Travel';

        $totalEmployees = Employee::count();
        $waitingLogistics = TravelRequest::where('status', 'Disetujui')->count();
        $readyCount = TravelRequest::where('status', 'Siap Berangkat')->count();
        $totalSppd = TravelRequest::whereNotNull('sppd_number')->count();

        $recentApproved = TravelRequest::with(['requester', 'destinationCity', 'logistics'])
            ->whereIn('status', ['Disetujui', 'Siap Berangkat'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('userName', 'totalEmployees', 'waitingLogistics', 'readyCount', 'totalSppd', 'recentApproved'));
    }

    public function adminPerjalanan(): View
    {
        $user = $this->currentUser('Admin');

        // Antrean perjalanan yang sudah DISETUJUI dan menunggu input tiket/hotel
        $approvedTrips = TravelRequest::with(['requester.department', 'destinationCity', 'originCity', 'assignees.employee', 'logistics'])
            ->where('status', 'Disetujui')
            ->latest()
            ->get()
            ->map(function ($trip) {
                $budget = $this->logisticsService->calculateStandardizedBudget($trip);
                $trip->sbm_budget = $budget;

                return $trip;
            });

        return view('admin.perjalanan', compact('approvedTrips', 'user'));
    }

    public function adminStoreLogistics(Request $request): RedirectResponse
    {
        $request->validate([
            'trip_id' => 'required',
            'transport_type' => 'required|string',
            'booking_code' => 'required|string',
            'hotel_name' => 'required|string',
            'departure_datetime' => 'required',
        ]);

        $user = $this->currentUser('Admin');

        try {
            $data = [
                'transport_type' => $request->transport_type,
                'booking_code_or_ticket' => $request->booking_code,
                'hotel_name' => $request->hotel_name,
                'departure_datetime' => $request->departure_datetime,
                'return_datetime' => $request->return_datetime ?? $request->departure_datetime,
                'estimated_total_cost' => $request->estimated_total_cost,
            ];

            $this->logisticsService->inputLogistics(
                $request->trip_id,
                $user,
                $data,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('admin.sppd')->with(
                'success',
                'Tiket transportasi & hotel berhasil diinput. Anggaran telah dikunci standar SBM dan status menjadi SIAP BERANGKAT.'
            );
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses logistik: '.$e->getMessage());
        }
    }

    public function adminRejectLogistics(Request $request): RedirectResponse
    {
        $request->validate([
            'trip_id' => 'required',
            'reason' => 'required|string',
        ]);

        $user = $this->currentUser('Admin');

        try {
            $this->logisticsService->rejectLogistics(
                $request->trip_id,
                $user,
                $request->reason,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('admin.perjalanan')->with(
                'success',
                'Pengajuan berhasil dikembalikan ke Pegawai untuk penyesuaian jadwal/kuota.'
            );
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengembalikan pengajuan: '.$e->getMessage());
        }
    }

    public function adminPegawai(): View
    {
        $employees = Employee::with('department')->get();

        return view('admin.pegawai', compact('employees'));
    }

    public function adminDokumen(): View
    {
        $documents = TravelRequest::with(['requester', 'destinationCity', 'logistics'])
            ->whereNotNull('sppd_number')
            ->latest()
            ->paginate(15);

        return view('admin.dokumen', compact('documents'));
    }

    public function adminSppd(): View
    {
        $sppdTrips = TravelRequest::with(['requester', 'destinationCity', 'logistics'])
            ->whereNotNull('sppd_number')
            ->latest()
            ->paginate(15);

        return view('admin.sppd', compact('sppdTrips'));
    }

    public function adminMonitoring(): View
    {
        $allTrips = TravelRequest::with(['requester', 'destinationCity', 'logistics', 'expenseReport'])
            ->latest()
            ->paginate(15);

        return view('admin.monitoring', compact('allTrips'));
    }

    public function adminNotifikasi(): View
    {
        $logs = AuditLog::with('travelRequest')->latest()->take(20)->get();
        $notifications = $this->formatNotifications($logs);

        return view('admin.notifikasi', compact('notifications'));
    }

    // ==========================================
    // MODUL SUPER ADMIN
    // ==========================================

    public function superadminDashboard(): View
    {
        $user = $this->currentUser('Super Admin');
        $userName = $user->employee?->name ?? 'Pak Super Admin';

        $userCount = User::count();
        $deptCount = Department::count();
        $tripCount = TravelRequest::count();
        $auditCount = AuditLog::count();

        // Calculate distribution
        $totalEmployees = Employee::count();
        $staffCount = Employee::where('grade', 'Staff')->orWhere('position', 'like', '%Staf%')->count();
        $managerCount = Employee::where('grade', 'Manager')->orWhere('position', 'like', '%Kepala%')->count();
        $financeCount = Employee::whereHas('department', fn ($q) => $q->where('code', 'FINANCE'))->count();
        $adminCount = max(1, $totalEmployees - ($staffCount + $managerCount + $financeCount));

        $roleStats = [
            'staff' => ['count' => $staffCount, 'pct' => $totalEmployees > 0 ? round(($staffCount / $totalEmployees) * 100) : 60],
            'manager' => ['count' => $managerCount, 'pct' => $totalEmployees > 0 ? round(($managerCount / $totalEmployees) * 100) : 25],
            'finance' => ['count' => $financeCount, 'pct' => $totalEmployees > 0 ? round(($financeCount / $totalEmployees) * 100) : 10],
            'admin' => ['count' => $adminCount, 'pct' => $totalEmployees > 0 ? round(($adminCount / $totalEmployees) * 100) : 5],
        ];

        $recentAudits = AuditLog::with(['user.employee', 'travelRequest'])->latest()->take(6)->get();

        return view('superadmin.dashboard', compact('userName', 'userCount', 'deptCount', 'tripCount', 'auditCount', 'recentAudits', 'roleStats'));
    }

    public function superadminPengguna(): View
    {
        $users = User::with(['employee.department'])->get();
        $departments = Department::all();

        return view('superadmin.pengguna', compact('users', 'departments'));
    }

    public function superadminStorePengguna(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:employees,nip',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string',
            'grade' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            Employee::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'nip' => $request->nip,
                'name' => $request->name,
                'position' => $request->position,
                'grade' => $request->grade,
            ]);

            DB::commit();

            return redirect()->route('superadmin.pengguna')->with('success', 'Akun pengguna & profil pegawai berhasil dibuat.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat pengguna: '.$e->getMessage());
        }
    }

    public function superadminAkses(): View
    {
        $roles = $this->matrixService->getRoles();
        $matrix = $this->matrixService->getMatrix();

        return view('superadmin.akses', compact('roles', 'matrix'));
    }

    public function superadminUpdateAkses(Request $request): RedirectResponse
    {
        $matrixInput = $request->input('matrix', []);
        $this->matrixService->updateMatrix($matrixInput);

        return redirect()->route('superadmin.akses')->with('success', 'Matriks otorisasi hak akses (RBAC) berhasil disimpan ke sistem.');
    }

    public function superadminResetAkses(): RedirectResponse
    {
        $this->matrixService->resetToDefault();

        return redirect()->route('superadmin.akses')->with('success', 'Matriks hak akses RBAC berhasil dikembalikan ke pengaturan awal standar.');
    }

    public function superadminKonfigurasi(): View
    {
        $cities = RefCity::all();
        $policies = TravelPolicy::with('destinationCity')->get();

        return view('superadmin.konfigurasi', compact('cities', 'policies'));
    }

    public function superadminAudit(): View
    {
        $logs = AuditLog::with(['user.employee', 'travelRequest.destinationCity'])->latest()->paginate(25);

        return view('superadmin.audit', compact('logs'));
    }

    public function superadminUnit(): View
    {
        $units = Department::withCount('employees')->get();

        return view('superadmin.unit', compact('units'));
    }

    public function superadminNotifikasi(): View
    {
        $logs = AuditLog::with('travelRequest')->latest()->take(20)->get();
        $notifications = $this->formatNotifications($logs);

        return view('superadmin.notifikasi', compact('notifications'));
    }

    /**
     * Map AuditLog collection into structured notification array.
     */
    protected function formatNotifications($logs): array
    {
        return $logs->map(function ($log) {
            $action = $log->action ?? 'AUDIT';
            $category = match (true) {
                str_contains($action, 'APPROVE') || str_contains($action, 'SUBMIT') || str_contains($action, 'CREATE') || str_contains($action, 'VALID') => 'success',
                str_contains($action, 'REJECT') || str_contains($action, 'REVISI') => 'warning',
                str_contains($action, 'DELETE') || str_contains($action, 'FAIL') => 'danger',
                default => 'info',
            };

            $title = match ($action) {
                'CREATE_DRAFT' => 'Draft Pengajuan Dibuat',
                'SUBMIT_TO_ATASAN' => 'Pengajuan Diserahkan ke Atasan',
                'APPROVE_BY_ATASAN' => 'Pengajuan Disetujui Atasan',
                'REJECT_BY_ATASAN' => 'Pengajuan Perlu Revisi / Ditolak',
                'INPUT_LOGISTICS' => 'Tiket & SPPD Digital Diterbitkan',
                'REJECT_LOGISTICS' => 'Penyesuaian Jadwal & Kuota Tiket',
                'SUBMIT_LPJ' => 'LPJ Diserahkan ke Bendahara',
                'VERIFY_EXPENSE_APPROVED' => 'LPJ Berhasil Diverifikasi Sah',
                'VERIFY_EXPENSE_REJECTED' => 'Catatan Revisi LPJ dari Bendahara',
                default => ucwords(strtolower(str_replace('_', ' ', $action))),
            };

            return [
                'title' => $title,
                'description' => $log->description,
                'time' => $log->created_at ? $log->created_at->diffForHumans() : 'Baru saja',
                'unread' => $log->created_at ? $log->created_at->greaterThan(now()->subDays(2)) : true,
                'category' => $category,
            ];
        })->toArray();
    }

    // ==========================================
    // DOWNLOAD DOKUMEN DIGITAL (PDF)
    // ==========================================

    public function downloadSppd(string $id): Response
    {
        $travelRequest = TravelRequest::with([
            'requester.department',
            'originCity',
            'destinationCity',
            'assignees.employee',
            'approvals.approver',
            'logistics',
        ])->findOrFail($id);

        $qrData = 'SPPD-VERIFIED|'.$travelRequest->sppd_number.'|NIP:'.$travelRequest->requester?->nip.'|GOV.TRIP-2026';
        $qrBase64 = 'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="#0066B3"/><text x="50" y="55" fill="#fff" font-size="12" text-anchor="middle" font-family="sans-serif">TTE SAH</text></svg>');

        $pdf = Pdf::loadView('pdf.sppd', compact('travelRequest', 'qrBase64'));
        $pdf->setPaper('A4', 'portrait');

        $sppdNumber = $travelRequest->sppd_number ?? ('PD-'.$travelRequest->id);
        $fileName = 'SPPD_'.str_replace(['/', '\\'], '_', $sppdNumber).'.pdf';

        return $pdf->stream($fileName);
    }

    public function downloadLpjFinal(string $id): Response
    {
        $travelRequest = TravelRequest::with([
            'requester.department',
            'originCity',
            'destinationCity',
            'assignees.employee',
            'approvals.approver',
            'logistics',
            'expenseReport',
            'auditLogs',
        ])->findOrFail($id);

        $advancePayment = (float) ($travelRequest->advance_payment > 0 ? $travelRequest->advance_payment : $travelRequest->estimated_total_cost);
        $realExpense = (float) ($travelRequest->expenseReport?->total_real_expense ?? $advancePayment);
        $balance = $advancePayment - $realExpense;

        $pdf = Pdf::loadView('pdf.lpj_final', [
            'travelRequest' => $travelRequest,
            'advancePayment' => $advancePayment,
            'realExpense' => $realExpense,
            'balance' => $balance,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $sppdNumber = $travelRequest->sppd_number ?? ('PD-'.$travelRequest->id);
        $fileName = 'LPJ_FINAL_'.str_replace(['/', '\\'], '_', $sppdNumber).'.pdf';

        return $pdf->stream($fileName);
    }
}
