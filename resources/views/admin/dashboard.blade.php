<x-layouts.app title="Dashboard Admin Travel - Gov.Trip" role="Admin" headerTitle="Dashboard Admin Travel">
    <!-- Hero Banner -->
    <x-hero-banner
        title="Halo, {{ $userName ?? 'Admin Travel' }}!"
        subtitle="Kelola pemesanan tiket transportasi, penerbitan SPPD digital, dan pantau perjalanan dinas aktif."
        actionText="Input Logistik & SPPD"
        actionHref="/admin/perjalanan"
    />

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Total Pegawai"
            value="128"
            sublabel="di 4 unit kerja"
            iconBg="bg-[#E3F0FA]"
            iconColor="text-[#0066B3]"
            href="/admin/pegawai"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Perjalanan Berjalan"
            value="3"
            sublabel="Yogyakarta, Jakarta, Bogor"
            iconBg="bg-[#DFF5F4]"
            iconColor="text-[#0B7A77]"
            href="/admin/monitoring"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
        </x-stat-card>

        <x-stat-card
            title="SPPD Digital Terbit"
            value="34"
            sublabel="Tahun 2026"
            iconBg="bg-[#FFF1D1]"
            iconColor="text-[#8A5A00]"
            href="/admin/sppd"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Dokumen Belum Lengkap"
            value="5"
            sublabel="Perlu tindak lanjut"
            iconBg="bg-[#FBE8E8]"
            iconColor="text-[#B02A2A]"
            href="/admin/dokumen"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
        </x-stat-card>
    </div>

    <!-- Active Trips Table Card -->
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-4">
            <div>
                <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">Perjalanan Dinas yang Sedang Berjalan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Monitoring pegawai yang berada di lokasi dinas</p>
            </div>
            <x-button variant="outline" size="sm" href="/admin/monitoring">Live Monitor &rarr;</x-button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor Kode</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Periode Tanggal</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">PD-2026/087</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Liyana Agustin</td>
                        <td class="py-3 px-3 text-[#1E293B]">Yogyakarta</td>
                        <td class="py-3 px-3 text-[#64748B]">21–23 Sep 2026</td>
                        <td class="py-3 px-3"><x-badge status="Berjalan">Berjalan</x-badge></td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">PD-2026/088</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Bagas Wicaksono</td>
                        <td class="py-3 px-3 text-[#1E293B]">Jakarta</td>
                        <td class="py-3 px-3 text-[#64748B]">20–22 Sep 2026</td>
                        <td class="py-3 px-3"><x-badge status="Berjalan">Berjalan</x-badge></td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">PD-2026/089</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Nadia Maharani</td>
                        <td class="py-3 px-3 text-[#1E293B]">Bogor</td>
                        <td class="py-3 px-3 text-[#64748B]">21 Sep 2026</td>
                        <td class="py-3 px-3"><x-badge status="Berjalan">Berjalan</x-badge></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
