<x-layouts.app title="Tim Saya - Gov.Trip" role="Atasan" headerTitle="Anggota Tim & Status">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Status Penugasan Anggota Tim</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Monitoring keberadaan dan aktivitas perjalanan dinas bawahan langsung</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nama Anggota</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Periode Tanggal</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Moda Transport</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Keperluan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3">
                            <span class="font-semibold text-[#1E293B] block">Bagas Wicaksono, S.E.</span>
                            <span class="text-[11.5px] text-[#64748B]">NIP: 19850615 200902 1 004</span>
                        </td>
                        <td class="py-3 px-3 font-medium text-[#123B72]">Jakarta Pusat</td>
                        <td class="py-3 px-3 text-[#64748B]">20–22 Sep 2026</td>
                        <td class="py-3 px-3 text-[#64748B]">Kereta Cepat</td>
                        <td class="py-3 px-3 text-[#64748B] max-w-[200px] truncate">Rapat koordinasi anggaran</td>
                        <td class="py-3 px-3"><x-badge status="Berjalan">Sedang dinas</x-badge></td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3">
                            <span class="font-semibold text-[#1E293B] block">Nadia Maharani, S.Si.</span>
                            <span class="text-[11.5px] text-[#64748B]">NIP: 19920120 201403 2 001</span>
                        </td>
                        <td class="py-3 px-3 font-medium text-[#123B72]">Bogor</td>
                        <td class="py-3 px-3 text-[#64748B]">21 Sep 2026</td>
                        <td class="py-3 px-3 text-[#64748B]">Kendaraan dinas</td>
                        <td class="py-3 px-3 text-[#64748B] max-w-[200px] truncate">Sinkronisasi tata kelola administrasi</td>
                        <td class="py-3 px-3"><x-badge status="Berjalan">Sedang dinas</x-badge></td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3">
                            <span class="font-semibold text-[#1E293B] block">Liyana Agustin, S.AP</span>
                            <span class="text-[11.5px] text-[#64748B]">NIP: 19880412 201101 2 002</span>
                        </td>
                        <td class="py-3 px-3 font-medium text-[#123B72]">Yogyakarta</td>
                        <td class="py-3 px-3 text-[#64748B]">21–23 Sep 2026</td>
                        <td class="py-3 px-3 text-[#64748B]">Kereta api</td>
                        <td class="py-3 px-3 text-[#64748B] max-w-[200px] truncate">Sosialisasi regulasi perjalanan dinas</td>
                        <td class="py-3 px-3"><x-badge status="Berjalan">Sedang dinas</x-badge></td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3">
                            <span class="font-semibold text-[#1E293B] block">Rudi Hermawan, S.Kom</span>
                            <span class="text-[11.5px] text-[#64748B]">NIP: 19830911 200604 1 003</span>
                        </td>
                        <td class="py-3 px-3 font-medium text-[#123B72]">Surabaya</td>
                        <td class="py-3 px-3 text-[#64748B]">12–14 Okt 2026</td>
                        <td class="py-3 px-3 text-[#64748B]">Pesawat</td>
                        <td class="py-3 px-3 text-[#64748B] max-w-[200px] truncate">Monitoring program regional</td>
                        <td class="py-3 px-3"><x-badge status="Menunggu">Terjadwal</x-badge></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
