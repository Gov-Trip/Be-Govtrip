<x-layouts.app title="SPPD Digital - Gov.Trip" role="Admin" headerTitle="Manajemen SPPD Digital">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Penerbitan Surat Perintah Perjalanan Dinas</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Kelola penomoran dan cetak format standar SPPD digital</p>
            </div>
            <a href="/admin/perjalanan" class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[10px] transition-colors cursor-pointer">
                + Terbitkan SPPD Baru
            </a>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor SPPD</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">090/087/SPPD/IX/2026</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Liyana Agustin, S.AP</td>
                        <td class="py-3 px-3 text-[#1E293B]">Yogyakarta</td>
                        <td class="py-3 px-3 text-[#64748B]">21–23 Sep 2026</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Sudah terbit</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/api/travel-requests/1/download-sppd" target="_blank" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Unduh SPPD</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">090/088/SPPD/IX/2026</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Bagas Wicaksono, S.E.</td>
                        <td class="py-3 px-3 text-[#1E293B]">Jakarta</td>
                        <td class="py-3 px-3 text-[#64748B]">20–22 Sep 2026</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Sudah terbit</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/api/travel-requests/1/download-sppd" target="_blank" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Unduh SPPD</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">090/090/SPPD/X/2026</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Fajar Nugraha</td>
                        <td class="py-3 px-3 text-[#1E293B]">Balikpapan</td>
                        <td class="py-3 px-3 text-[#64748B]">2–4 Nov 2026</td>
                        <td class="py-3 px-3"><x-badge status="Menunggu">Belum terbit</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/admin/perjalanan" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">Proses Tiket</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
