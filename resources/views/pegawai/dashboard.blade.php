<x-layouts.app title="Dashboard Pegawai - Gov.Trip" role="Pegawai" headerTitle="Dashboard">
    <!-- Hero Banner -->
    <x-hero-banner
        title="Halo, {{ $userName ?? 'Andi Pratama' }}!"
        subtitle="Pantau perjalanan dinas, status persetujuan, dan pengajuan SPPD secara terpusat."
        actionText="Ajukan perjalanan baru"
        actionHref="/pegawai/pengajuan"
    />

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Total Perjalanan"
            :value="$totalTrips ?? '6'"
            sublabel="4 selesai, 1 berjalan"
            iconBg="bg-[#E3F0FA]"
            iconColor="text-[#0066B3]"
            href="/pegawai/perjalanan"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Menunggu Persetujuan"
            :value="$pendingCount ?? '1'"
            sublabel="Perjalanan ke Bandung"
            iconBg="bg-[#FFF1D1]"
            iconColor="text-[#8A5A00]"
            href="/pegawai/perjalanan"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </x-stat-card>

        <x-stat-card
            title="SPPD Siap Diunduh"
            :value="$sppdCount ?? '1'"
            sublabel="Nomor 090/087/SPPD"
            iconBg="bg-[#DFF5F4]"
            iconColor="text-[#0B7A77]"
            href="/pegawai/sppd"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Batas Unggah LPJ"
            value="3 hari"
            sublabel="Perjalanan Makassar"
            iconBg="bg-[#FBE8E8]"
            iconColor="text-[#B02A2A]"
            href="/pegawai/lpj"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </x-stat-card>
    </div>

    <!-- Stepper Timeline Card -->
    <x-card>
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">
                    Alur Perjalanan Dinas Aktif: PD-2026/087 (Yogyakarta)
                </h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">
                    Tahapan proses dari pengajuan hingga pelaporan LPJ
                </p>
            </div>
            <x-badge status="Berjalan">Berjalan</x-badge>
        </div>

        @php
            $steps = [
                ['label' => 'Draft', 'sublabel' => '14 Sep', 'status' => 'completed'],
                ['label' => 'Persetujuan Atasan', 'sublabel' => 'Disetujui', 'status' => 'completed'],
                ['label' => 'Kepala Bidang', 'sublabel' => 'Disetujui', 'status' => 'completed'],
                ['label' => 'Penerbitan SPPD', 'sublabel' => 'Terbit', 'status' => 'completed'],
                ['label' => 'Pelaksanaan & LPJ', 'sublabel' => 'Sedang berjalan', 'status' => 'current'],
            ];
        @endphp
        <x-stepper :steps="$steps" orientation="horizontal" />
    </x-card>

    <!-- Recent Trips Table Card -->
    <x-card>
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">
                    Perjalanan Dinas Terbaru
                </h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">
                    Daftar riwayat perjalanan dinas dan status terkininya
                </p>
            </div>

            <x-button variant="outline" size="sm" href="/pegawai/perjalanan">
                Lihat Semua
            </x-button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor / Kode</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @php
                        $trips = $recentTrips ?? [
                            ['number' => 'PD-2026/087', 'destination' => 'Yogyakarta', 'dates' => '21–23 Sep', 'status' => 'Berjalan'],
                            ['number' => 'PD-2026/091', 'destination' => 'Bandung', 'dates' => '6–8 Okt', 'status' => 'Menunggu'],
                            ['number' => 'PD-2026/079', 'destination' => 'Makassar', 'dates' => '14–16 Sep', 'status' => 'LPJ perlu diunggah'],
                        ];
                    @endphp
                    @foreach($trips as $trip)
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 font-semibold text-[#123B72]">{{ $trip['number'] }}</td>
                            <td class="py-3 px-3 text-[#1E293B]">{{ $trip['destination'] }}</td>
                            <td class="py-3 px-3 text-[#64748B]">{{ $trip['dates'] }}</td>
                            <td class="py-3 px-3"><x-badge :status="$trip['status']" /></td>
                            <td class="py-3 px-3 text-right">
                                <a href="/pegawai/perjalanan" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
