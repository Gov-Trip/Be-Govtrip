@props([
    'role' => 'Pegawai',
])

@php
    $authUser = auth()->user();
    $authEmp = $authUser?->employee;

    // Dynamic badges from live database
    $pendingAtasan = \App\Models\TravelRequest::whereIn('status', ['Menunggu Persetujuan Atasan', 'Menunggu Approval Atasan', 'Menunggu Persetujuan'])->count();
    $pendingLpj = \App\Models\TravelRequest::where('status', 'Menunggu Verifikasi LPJ')->count();
    $waitingLogistics = \App\Models\TravelRequest::where('status', 'Disetujui')->count();
    $draftPegawai = \App\Models\TravelRequest::when($authEmp?->id, fn($q, $id) => $q->where('requester_employee_id', $id))->whereIn('status', ['Draft', 'draft'])->count();
    $recentNotifs = \App\Models\AuditLog::where('created_at', '>=', now()->subDays(2))->count();

    $rolesConfig = [
        'Admin' => [
            'badge' => 'Peran: Admin Travel',
            'user' => ['name' => 'Admin Travel', 'roleTitle' => 'Staff Logistik & Tiket', 'avatar' => 'AT'],
            'base' => '/admin',
            'items' => [
                ['label' => 'Dashboard', 'href' => '/admin', 'icon' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>'],
                ['label' => 'Data Pegawai', 'href' => '/admin/pegawai', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                ['label' => 'Data Perjalanan', 'href' => '/admin/perjalanan', 'badge' => $waitingLogistics > 0 ? $waitingLogistics : null, 'icon' => '<rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
                ['label' => 'Dokumen', 'href' => '/admin/dokumen', 'icon' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>'],
                ['label' => 'SPPD Digital', 'href' => '/admin/sppd', 'icon' => '<path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>'],
                ['label' => 'Monitoring Perjalanan', 'href' => '/admin/monitoring', 'icon' => '<circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>'],
                ['label' => 'Notifikasi', 'href' => '/admin/notifikasi', 'badge' => $recentNotifs > 0 ? min($recentNotifs, 9) : null, 'icon' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>'],
            ],
        ],
        'Pegawai' => [
            'badge' => 'Peran: Pegawai',
            'user' => ['name' => 'Andi Pratama', 'roleTitle' => 'Staf Perencanaan', 'avatar' => 'AP'],
            'base' => '/pegawai',
            'items' => [
                ['label' => 'Dashboard', 'href' => '/pegawai', 'icon' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>'],
                ['label' => 'Pengajuan Baru', 'href' => '/pegawai/pengajuan', 'icon' => '<path d="M12 5v14M5 12h14"/>'],
                ['label' => 'Perjalanan Saya', 'href' => '/pegawai/perjalanan', 'icon' => '<rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
                ['label' => 'SPPD Digital', 'href' => '/pegawai/sppd', 'icon' => '<path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>'],
                ['label' => 'Laporan Pertanggungjawaban', 'href' => '/pegawai/lpj', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>'],
                ['label' => 'Draft Pengajuan', 'href' => '/pegawai/draft', 'badge' => $draftPegawai > 0 ? $draftPegawai : null, 'icon' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>'],
                ['label' => 'Notifikasi', 'href' => '/pegawai/notifikasi', 'badge' => $recentNotifs > 0 ? min($recentNotifs, 9) : null, 'icon' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>'],
                ['label' => 'Profil', 'href' => '/pegawai/profil', 'icon' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
            ],
        ],
        'Atasan' => [
            'badge' => 'Peran: Atasan Langsung',
            'user' => ['name' => 'Bapak Kepala Bidang', 'roleTitle' => 'Kepala Bidang Program', 'avatar' => 'KB'],
            'base' => '/atasan',
            'items' => [
                ['label' => 'Dashboard', 'href' => '/atasan', 'icon' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>'],
                ['label' => 'Kotak Masuk Persetujuan', 'href' => '/atasan/inbox', 'badge' => $pendingAtasan > 0 ? $pendingAtasan : null, 'icon' => '<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>'],
                ['label' => 'Anggota Tim & Status', 'href' => '/atasan/tim', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                ['label' => 'Riwayat Persetujuan', 'href' => '/atasan/riwayat', 'icon' => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>'],
                ['label' => 'Notifikasi', 'href' => '/atasan/notifikasi', 'badge' => $recentNotifs > 0 ? min($recentNotifs, 9) : null, 'icon' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>'],
            ],
        ],
        'Bendahara' => [
            'badge' => 'Peran: Bendahara',
            'user' => ['name' => 'Ibu Bendahara', 'roleTitle' => 'Staff Keuangan', 'avatar' => 'IB'],
            'base' => '/bendahara',
            'items' => [
                ['label' => 'Dashboard', 'href' => '/bendahara', 'icon' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>'],
                ['label' => 'Verifikasi Berkas', 'href' => '/bendahara/verifikasi', 'badge' => $pendingLpj > 0 ? $pendingLpj : null, 'icon' => '<path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>'],
                ['label' => 'Pencairan Dana', 'href' => '/bendahara/pencairan', 'icon' => '<rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>'],
                ['label' => 'Status Anggaran', 'href' => '/bendahara/anggaran', 'icon' => '<line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/>'],
                ['label' => 'ACC Logistik', 'href' => '/bendahara/logistik', 'icon' => '<rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
                ['label' => 'Laporan Pertanggungjawaban', 'href' => '/bendahara/lpj', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>'],
                ['label' => 'Riwayat Verifikasi', 'href' => '/bendahara/riwayat', 'icon' => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>'],
                ['label' => 'Notifikasi', 'href' => '/bendahara/notifikasi', 'badge' => $recentNotifs > 0 ? min($recentNotifs, 9) : null, 'icon' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>'],
            ],
        ],
        'Super Admin' => [
            'badge' => 'Peran: Super Admin',
            'user' => ['name' => 'Pak Super Admin', 'roleTitle' => 'Kepala IT & Pengelola Sistem', 'avatar' => 'SA'],
            'base' => '/superadmin',
            'items' => [
                ['label' => 'Dashboard', 'href' => '/superadmin', 'icon' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>'],
                ['label' => 'Manajemen Pengguna', 'href' => '/superadmin/pengguna', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                ['label' => 'Role & Hak Akses', 'href' => '/superadmin/akses', 'icon' => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/>'],
                ['label' => 'Konfigurasi Sistem', 'href' => '/superadmin/konfigurasi', 'icon' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>'],
                ['label' => 'Audit Log', 'href' => '/superadmin/audit', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
                ['label' => 'Unit Kerja / SKPD', 'href' => '/superadmin/unit', 'icon' => '<path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4M5 21V10.85M19 21V10.85M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/>'],
                ['label' => 'Notifikasi', 'href' => '/superadmin/notifikasi', 'badge' => $recentNotifs > 0 ? min($recentNotifs, 9) : null, 'icon' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>'],
            ],
        ],
    ];

    $cfg = $rolesConfig[$role] ?? $rolesConfig['Pegawai'];
    $currentPath = request()->path();
@endphp

<aside {{ $attributes->merge(['class' => 'w-[252px] min-h-screen bg-[#123B72] flex flex-col justify-between p-[20px_14px] text-white select-none shrink-0 border-r border-[#123B72]']) }}>
    <!-- Top Header & Navigation -->
    <div class="flex flex-col gap-4">
        <!-- Brand & Role -->
        <div class="flex flex-col gap-2.5 px-2 pt-1">
            <a href="{{ $cfg['base'] }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center font-bold text-white text-[15px]">
                    G
                </div>
                <span class="font-bold text-[18px] text-white tracking-tight">
                    Gov.Trip
                </span>
            </a>

            <!-- Role badge -->
            <div class="bg-white/10 rounded-lg px-2.5 py-1 text-[12px] text-white/90 font-normal">
                {{ $cfg['badge'] }}
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex flex-col gap-1 mt-1">
            @foreach($cfg['items'] as $item)
                @php
                    $itemPath = ltrim($item['href'], '/');
                    $basePath = ltrim($cfg['base'], '/');
                    $isActive = ($item['href'] === $cfg['base'])
                        ? ($currentPath === $basePath)
                        : (str_starts_with($currentPath, $itemPath));
                @endphp
                <a href="{{ $item['href'] }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-[10px] text-[13.5px] transition-all font-medium {{ $isActive ? 'bg-white text-[#123B72] shadow-sm font-semibold' : 'text-[#C9D8EC] hover:bg-white/10 hover:text-white' }}">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-[18px] h-[18px] {{ $isActive ? 'text-[#123B72]' : 'text-[#C9D8EC]' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            {!! $item['icon'] !!}
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </div>

                    @if(!empty($item['badge']))
                        <span class="bg-[#F5A000] text-[#1E293B] text-[11px] font-semibold px-2 py-0.5 rounded-full">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>
    </div>

    <!-- User Card at Bottom -->
    <div class="mt-8 pt-4 border-t border-white/10">
        @php
            $cardName = $authEmp?->name ?? $authUser?->name ?? $cfg['user']['name'];
            $cardTitle = $authEmp?->position ?? $authEmp?->department?->name ?? $cfg['user']['roleTitle'];
            $cardAvatar = strtoupper(substr($cardName, 0, 2));
        @endphp
        <div class="flex items-center gap-2.5 p-2 bg-white/7 rounded-[12px]">
            <div class="w-[34px] h-[34px] rounded-full bg-[#1684C4] flex items-center justify-center text-white font-semibold text-[13px] shrink-0">
                {{ $cardAvatar }}
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-white text-[13px] font-medium truncate" title="{{ $cardName }}">
                    {{ $cardName }}
                </span>
                <span class="text-[#A9BEDA] text-[11.5px] truncate" title="{{ $cardTitle }}">
                    {{ $cardTitle }}
                </span>
            </div>
        </div>

        <!-- Logout Link -->
        <a href="/login" class="flex items-center gap-2 mt-3 px-2 py-1.5 text-[#C9D8EC] hover:text-white text-[12px] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" x2="9" y1="12" y2="12" />
            </svg>
            <span>Keluar ke Login</span>
        </a>
    </div>
</aside>
