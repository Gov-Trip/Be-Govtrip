<x-layouts.app title="Status Anggaran - Gov.Trip" role="Bendahara" headerTitle="Status Anggaran & Pagu DPA">
    <!-- Stat Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card
            title="Total Pagu Anggaran DPA"
            value="Rp 350,0 Jt"
            sublabel="Tahun Anggaran 2026"
            iconBg="bg-[#E3F0FA]"
            iconColor="text-[#0066B3]"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Realisasi SPJ Terverifikasi"
            value="Rp 148,5 Jt"
            sublabel="42.4% terserap"
            iconBg="bg-[#DFF5F4]"
            iconColor="text-[#0B7A77]"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Uang Muka Berjalan"
            value="Rp 14,8 Jt"
            sublabel="sedang berjalan dinas"
            iconBg="bg-[#FFF1D1]"
            iconColor="text-[#8A5A00]"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </x-stat-card>
    </div>

    <!-- Pagu Per Sub-Kegiatan / Unit -->
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-4">
            <div>
                <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">Penyerapan Anggaran Per Unit Kerja</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Alokasi pagu perjalanan dinas semester berjalan</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Unit Kerja</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pagu DPA</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Realisasi</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Sisa Pagu</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">Bidang Perencanaan & Program</td>
                        <td class="py-3 px-3 text-[#1E293B]">Rp 150.000.000</td>
                        <td class="py-3 px-3 font-medium text-[#0B7A77]">Rp 72.400.000</td>
                        <td class="py-3 px-3 text-[#64748B]">Rp 77.600.000</td>
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#0066B3] h-full rounded-full" style="width: 48.2%"></div>
                                </div>
                                <span class="text-[12px] text-[#64748B]">48.2%</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">Divisi Keuangan & Aset</td>
                        <td class="py-3 px-3 text-[#1E293B]">Rp 80.000.000</td>
                        <td class="py-3 px-3 font-medium text-[#0B7A77]">Rp 34.100.000</td>
                        <td class="py-3 px-3 text-[#64748B]">Rp 45.900.000</td>
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#0066B3] h-full rounded-full" style="width: 42.6%"></div>
                                </div>
                                <span class="text-[12px] text-[#64748B]">42.6%</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">Logistik & Tata Usaha</td>
                        <td class="py-3 px-3 text-[#1E293B]">Rp 120.000.000</td>
                        <td class="py-3 px-3 font-medium text-[#0B7A77]">Rp 42.000.000</td>
                        <td class="py-3 px-3 text-[#64748B]">Rp 78.000.000</td>
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#0066B3] h-full rounded-full" style="width: 35.0%"></div>
                                </div>
                                <span class="text-[12px] text-[#64748B]">35.0%</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
