<x-layouts.app title="Audit Log - Gov.Trip" role="Super Admin" headerTitle="Audit Log & Rekam Jejak Sistem">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Rekam Jejak Operasional Sistem</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Catatan seluruh aksi login, perubahan data, dan transaksi anggaran</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse text-[13px]">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Waktu</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pengguna</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Role</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Modul</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Aksi</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3]">
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-3 text-[#64748B]">19 Okt 2026, 10:42</td>
                        <td class="py-3 px-3 font-semibold text-[#1E293B]">Bambang Wijaya</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[11px] bg-[#123B72] text-white">Super Admin</span></td>
                        <td class="py-3 px-3 text-[#123B72] font-medium">Master Data</td>
                        <td class="py-3 px-3 font-mono text-[12px] text-[#0066B3]">UPDATE_SBM_UANG_HARIAN</td>
                        <td class="py-3 px-3 text-[#64748B]">Memperbarui SBM Uang Harian DKI Jakarta</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-3 text-[#64748B]">19 Okt 2026, 09:15</td>
                        <td class="py-3 px-3 font-semibold text-[#1E293B]">Ibu Bendahara</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[11px] bg-[#FFF1D1] text-[#8A5A00]">Bendahara</span></td>
                        <td class="py-3 px-3 text-[#123B72] font-medium">Finance LPJ</td>
                        <td class="py-3 px-3 font-mono text-[12px] text-[#0B7A77]">VERIFY_EXPENSE_REPORT</td>
                        <td class="py-3 px-3 text-[#64748B]">Menyetujui LPJ-2026/043 Citra Lestari (Bandung)</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-3 text-[#64748B]">18 Okt 2026, 16:30</td>
                        <td class="py-3 px-3 font-semibold text-[#1E293B]">Kang Tiket</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[11px] bg-[#E3F0FA] text-[#0066B3]">Admin Travel</span></td>
                        <td class="py-3 px-3 text-[#123B72] font-medium">SPPD & Logistik</td>
                        <td class="py-3 px-3 font-mono text-[12px] text-[#0066B3]">GENERATE_SPPD_DIGITAL</td>
                        <td class="py-3 px-3 text-[#64748B]">Menerbitkan SPPD 090/088/SPPD/IX/2026</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
