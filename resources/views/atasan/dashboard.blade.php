<x-layouts.app title="Dashboard Atasan - Gov.Trip" role="Atasan" headerTitle="Dashboard Atasan">
    <!-- Hero Banner -->
    <x-hero-banner
        title="Halo, {{ $userName ?? 'Bapak Kepala Bidang' }}!"
        subtitle="Kelola persetujuan perjalanan dinas bawahan dan pantau penugasan tim secara real-time."
        actionText="Buka Kotak Masuk (3)"
        actionHref="/atasan/inbox"
    />

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Menunggu Persetujuan"
            value="3"
            sublabel="butuh keputusan Anda"
            iconBg="bg-[#FFF1D1]"
            iconColor="text-[#8A5A00]"
            href="/atasan/inbox"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Anggota Sedang Dinas"
            value="3"
            sublabel="Bagas, Nadia, Liyana"
            iconBg="bg-[#E3F0FA]"
            iconColor="text-[#0066B3]"
            href="/atasan/tim"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Disetujui Bulan Ini"
            value="14"
            sublabel="+2 dibanding bulan lalu"
            iconBg="bg-[#DFF5F4]"
            iconColor="text-[#0B7A77]"
            href="/atasan/riwayat"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Pagu Anggaran Tim"
            value="Rp 85 Jt"
            sublabel="Terserap 56%"
            iconBg="bg-[#FFF1D1]"
            iconColor="text-[#8A5A00]"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </x-stat-card>
    </div>

    <!-- Antrean Persetujuan Cepat -->
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-4">
            <div>
                <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">Antrean Pengajuan Menunggu Keputusan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Pengajuan perjalanan dinas dari staf tim Anda</p>
            </div>
            <x-button variant="outline" size="sm" href="/atasan/inbox">Lihat Semua</x-button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-[12px] border border-[#E8EEF3] bg-white hover:border-[#0066B3] transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-[14px] text-[#123B72]">Rudi &middot; Surabaya</span>
                        <x-badge status="Menunggu">Menunggu</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">PD-2026/093 &middot; 12–14 Okt 2026</span>
                    <span class="text-[12px] text-[#64748B] block mt-1">Estimasi Biaya: <strong class="text-[#1E293B]">Rp 6.120.000</strong></span>
                </div>
                <div class="mt-4 pt-3 border-t border-[#E8EEF3] flex justify-end">
                    <a href="/atasan/inbox" class="text-[12.5px] font-semibold text-[#0066B3] hover:underline">Review & Putuskan &rarr;</a>
                </div>
            </div>

            <div class="p-4 rounded-[12px] border border-[#E8EEF3] bg-white hover:border-[#0066B3] transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-[14px] text-[#123B72]">Sari &middot; Yogyakarta</span>
                        <x-badge status="Menunggu">Menunggu</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">PD-2026/094 &middot; 15–17 Okt 2026</span>
                    <span class="text-[12px] text-[#64748B] block mt-1">Estimasi Biaya: <strong class="text-[#1E293B]">Rp 3.500.000</strong></span>
                </div>
                <div class="mt-4 pt-3 border-t border-[#E8EEF3] flex justify-end">
                    <a href="/atasan/inbox" class="text-[12.5px] font-semibold text-[#0066B3] hover:underline">Review & Putuskan &rarr;</a>
                </div>
            </div>

            <div class="p-4 rounded-[12px] border border-[#E8EEF3] bg-white hover:border-[#0066B3] transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-[14px] text-[#123B72]">Bagas &middot; Jakarta</span>
                        <x-badge status="Menunggu">Menunggu</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">PD-2026/095 &middot; 24–26 Okt 2026</span>
                    <span class="text-[12px] text-[#64748B] block mt-1">Estimasi Biaya: <strong class="text-[#1E293B]">Rp 4.250.000</strong></span>
                </div>
                <div class="mt-4 pt-3 border-t border-[#E8EEF3] flex justify-end">
                    <a href="/atasan/inbox" class="text-[12.5px] font-semibold text-[#0066B3] hover:underline">Review & Putuskan &rarr;</a>
                </div>
            </div>
        </div>
    </x-card>
</x-layouts.app>
