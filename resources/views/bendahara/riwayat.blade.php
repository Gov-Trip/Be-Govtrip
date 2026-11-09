<x-layouts.app title="Riwayat Verifikasi - Gov.Trip" role="Bendahara" headerTitle="Riwayat Verifikasi Keuangan">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Log Audit & Verifikasi Keuangan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Daftar laporan yang telah selesai diverifikasi oleh bendahara</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor LPJ</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal Audit</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nominal Sah</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-2026/043</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Citra Lestari</td>
                        <td class="py-3 px-3 text-[#1E293B]">Bandung</td>
                        <td class="py-3 px-3 text-[#64748B]">20 Okt 2026</td>
                        <td class="py-3 px-3 font-semibold text-[#0B7A77]">Rp 2.250.000</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Selesai Diverifikasi</x-badge></td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-2026/042</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Andi Pratama</td>
                        <td class="py-3 px-3 text-[#1E293B]">Semarang</td>
                        <td class="py-3 px-3 text-[#64748B]">18 Okt 2026</td>
                        <td class="py-3 px-3 font-semibold text-[#0B7A77]">Rp 1.950.000</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Selesai Diverifikasi</x-badge></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
