<x-layouts.app title="Laporan Pertanggungjawaban - Gov.Trip" role="Bendahara" headerTitle="Laporan Pertanggungjawaban (LPJ)">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Seluruh Berkas LPJ Masuk</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Daftar laporan pertanggungjawaban dari seluruh dinas luar</p>
            </div>
            <x-button variant="primary" size="sm" href="/bendahara/verifikasi">
                Periksa Antrean &rarr;
            </x-button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor LPJ</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai / Pelaksana</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Uang Muka</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Realisasi Riil</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status Audit</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-2026/044</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Bagas Wicaksono</td>
                        <td class="py-3 px-3 text-[#1E293B]">Jakarta</td>
                        <td class="py-3 px-3 text-[#64748B]">Rp 3.780.000</td>
                        <td class="py-3 px-3 font-semibold text-[#0066B3]">Rp 3.655.000</td>
                        <td class="py-3 px-3"><x-badge status="Menunggu">Perlu verifikasi</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/bendahara/verifikasi" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Audit</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-2026/043</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Citra Lestari</td>
                        <td class="py-3 px-3 text-[#1E293B]">Bandung</td>
                        <td class="py-3 px-3 text-[#64748B]">Rp 2.450.000</td>
                        <td class="py-3 px-3 font-semibold text-[#0B7A77]">Rp 2.250.000</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Terverifikasi</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/api/finance/verifications/1/lpj/download" target="_blank" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Unduh PDF</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-2026/042</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Andi Pratama</td>
                        <td class="py-3 px-3 text-[#1E293B]">Semarang</td>
                        <td class="py-3 px-3 text-[#64748B]">Rp 1.950.000</td>
                        <td class="py-3 px-3 font-semibold text-[#0B7A77]">Rp 1.950.000</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Terverifikasi</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/api/finance/verifications/1/lpj/download" target="_blank" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Unduh PDF</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
