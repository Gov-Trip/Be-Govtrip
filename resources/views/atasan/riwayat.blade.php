<x-layouts.app title="Riwayat Persetujuan - Gov.Trip" role="Atasan" headerTitle="Riwayat Persetujuan">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Log Keputusan Persetujuan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Daftar keputusan yang telah Anda ambil terkait pengajuan perjalanan dinas bawahan</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor Pengajuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nama Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal Diputuskan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Keputusan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">PD-2026/088</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Bagas Wicaksono</td>
                        <td class="py-3 px-3 text-[#1E293B]">Jakarta</td>
                        <td class="py-3 px-3 text-[#64748B]">17 Sep 2026</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Disetujui</x-badge></td>
                        <td class="py-3 px-3 text-[#64748B]">Disetujui, diteruskan ke Kepala Bidang</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">PD-2026/087</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Liyana Agustin</td>
                        <td class="py-3 px-3 text-[#1E293B]">Yogyakarta</td>
                        <td class="py-3 px-3 text-[#64748B]">14 Sep 2026</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Disetujui</x-badge></td>
                        <td class="py-3 px-3 text-[#64748B]">Sesuai dengan agenda sosialisasi kementerian</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">PD-2026/081</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Sari Rahayu</td>
                        <td class="py-3 px-3 text-[#1E293B]">Solo</td>
                        <td class="py-3 px-3 text-[#64748B]">10 Sep 2026</td>
                        <td class="py-3 px-3"><x-badge status="Disetujui">Disetujui</x-badge></td>
                        <td class="py-3 px-3 text-[#64748B]">Disetujui penuh</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
