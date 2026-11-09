<x-layouts.app title="Dashboard Bendahara - Gov.Trip" role="Bendahara" headerTitle="Dashboard Keuangan & LPJ">
    <!-- Hero Banner -->
    <x-hero-banner
        title="Halo, {{ $userName ?? 'Ibu Bendahara' }}!"
        subtitle="Verifikasi bukti pengeluaran riil LPJ dan kelola pencairan uang muka perjalanan secara akuntabel."
        actionText="Mulai Verifikasi LPJ"
        actionHref="/bendahara/verifikasi"
    />

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Antrean Verifikasi"
            value="4"
            sublabel="LPJ menunggu audit berkas"
            iconBg="bg-[#FFF1D1]"
            iconColor="text-[#8A5A00]"
            href="/bendahara/verifikasi"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Realisasi Anggaran"
            value="Rp 148,5 Jt"
            sublabel="42.4% dari total pagu"
            iconBg="bg-[#DFF5F4]"
            iconColor="text-[#0B7A77]"
            href="/bendahara/anggaran"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Uang Muka Berjalan"
            value="Rp 14,8 Jt"
            sublabel="4 perjalanan aktif"
            iconBg="bg-[#E3F0FA]"
            iconColor="text-[#0066B3]"
            href="/bendahara/pencairan"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Sisa Pagu Anggaran"
            value="Rp 201,5 Jt"
            sublabel="Tahun Anggaran 2026"
            iconBg="bg-[#FFF1D1]"
            iconColor="text-[#8A5A00]"
            href="/bendahara/anggaran"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>
        </x-stat-card>
    </div>

    <!-- Antrean Verifikasi LPJ Table Card -->
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-4">
            <div>
                <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">Antrean Verifikasi LPJ Terbaru</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Laporan pertanggungjawaban yang masuk dan siap diverifikasi</p>
            </div>
            <x-button variant="outline" size="sm" href="/bendahara/verifikasi">Lihat Semua</x-button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor LPJ</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nominal Riil</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-2026/044</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Bagas Wicaksono</td>
                        <td class="py-3 px-3 text-[#1E293B]">Jakarta</td>
                        <td class="py-3 px-3 font-semibold text-[#0066B3]">Rp 3.655.000</td>
                        <td class="py-3 px-3"><x-badge status="Menunggu">Perlu verifikasi</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/bendahara/verifikasi" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Periksa Berkas &rarr;</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-2026/045</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Dewi Anggraini</td>
                        <td class="py-3 px-3 text-[#1E293B]">Surabaya</td>
                        <td class="py-3 px-3 font-semibold text-[#0066B3]">Rp 5.240.000</td>
                        <td class="py-3 px-3"><x-badge status="Menunggu">Perlu verifikasi</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/bendahara/verifikasi" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Periksa Berkas &rarr;</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-2026/046</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Nadia Maharani</td>
                        <td class="py-3 px-3 text-[#1E293B]">Bogor</td>
                        <td class="py-3 px-3 font-semibold text-[#0066B3]">Rp 1.520.000</td>
                        <td class="py-3 px-3"><x-badge status="Menunggu">Perlu verifikasi</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/bendahara/verifikasi" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Periksa Berkas &rarr;</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
