@props([
    'title' => 'Dashboard',
    'role' => 'Pegawai',
    'searchPlaceholder' => 'Cari data...',
    'notificationHref' => null,
    'hasNotification' => true,
])

@php
    $defaultNotifLinks = [
        'Admin' => '/admin/notifikasi',
        'Pegawai' => '/pegawai/notifikasi',
        'Atasan' => '/atasan/notifikasi',
        'Bendahara' => '/bendahara/notifikasi',
        'Super Admin' => '/superadmin/notifikasi',
    ];
    $resolvedNotifHref = $notificationHref ?? ($defaultNotifLinks[$role] ?? '/pegawai/notifikasi');
@endphp

<header class="h-[64px] bg-white border-b border-[#E8EEF3] px-6 sm:px-8 flex items-center justify-between sticky top-0 z-20 shrink-0">
    <!-- Left: Mobile hamburger + Page Title -->
    <div class="flex items-center gap-3">
        <button type="button" onclick="window.GovTrip?.toggleMobileSidebar()"
                class="lg:hidden p-1.5 text-[#64748B] hover:text-[#123B72] rounded-lg hover:bg-slate-100 transition-colors"
                aria-label="Buka menu navigasi">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <line x1="4" x2="20" y1="12" y2="12" />
                <line x1="4" x2="20" y1="6" y2="6" />
                <line x1="4" x2="20" y1="18" y2="18" />
            </svg>
        </button>

        <h1 class="text-[18px] sm:text-[20px] font-semibold text-[#123B72] tracking-tight">
            {{ $title }}
        </h1>
    </div>

    <!-- Right: Search + Notification + Custom Actions -->
    <div class="flex items-center gap-3">
        <!-- Search input -->
        <div class="hidden sm:flex items-center bg-[#F1F7FB] border border-[#E8EEF3] rounded-[10px] px-3 py-1.5 gap-2 w-[220px] md:w-[260px] focus-within:border-[#0066B3] focus-within:bg-white transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#757575" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
            <input type="text" placeholder="{{ $searchPlaceholder }}"
                   onkeyup="if(typeof handleHeaderSearch === 'function') handleHeaderSearch(this.value)"
                   class="bg-transparent text-[13px] text-[#1E293B] placeholder-[#757575] focus:outline-none w-full" />
        </div>

        {{ $slot }}

        <!-- Notification Bell Button -->
        <a href="{{ $resolvedNotifHref }}"
           class="relative w-[38px] h-[38px] rounded-[10px] bg-white border border-[#E8EEF3] hover:bg-[#F1F7FB] flex items-center justify-center text-[#64748B] hover:text-[#123B72] transition-colors"
           title="Notifikasi">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
            </svg>
            @if($hasNotification)
                <span class="absolute top-[8px] right-[8px] w-2 h-2 rounded-full bg-[#F5A000] ring-2 ring-white"></span>
            @endif
        </a>
    </div>
</header>
