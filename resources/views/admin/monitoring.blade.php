<x-layouts.app title="Monitoring Perjalanan - Gov.Trip" role="Admin" headerTitle="Monitoring Perjalanan Real-Time">
    <div class="flex flex-col gap-6">
        <!-- Live Active Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-card class="border-t-4 border-t-[#0066B3]">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#E8EEF3]">
                    <span class="font-bold text-[14px] text-[#123B72]">Liyana Agustin</span>
                    <x-badge status="Berjalan">Hari 1 dari 3</x-badge>
                </div>
                <div class="flex flex-col gap-1 text-[12.5px] text-[#64748B]">
                    <span>Lokasi: <strong class="text-[#1E293B]">Yogyakarta</strong></span>
                    <span>Penginapan: <strong class="text-[#1E293B]">Hotel Tentrem (Room 304)</strong></span>
                    <span>Check-in Terakhir: <strong class="text-[#0B7A77]">Hari ini, 09.30 WIB</strong></span>
                    <span>Kontak: <strong class="text-[#1E293B]">0812-3456-7890</strong></span>
                </div>
            </x-card>

            <x-card class="border-t-4 border-t-[#0066B3]">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#E8EEF3]">
                    <span class="font-bold text-[14px] text-[#123B72]">Bagas Wicaksono</span>
                    <x-badge status="Berjalan">Hari 2 dari 3</x-badge>
                </div>
                <div class="flex flex-col gap-1 text-[12.5px] text-[#64748B]">
                    <span>Lokasi: <strong class="text-[#1E293B]">Jakarta Pusat</strong></span>
                    <span>Penginapan: <strong class="text-[#1E293B]">Hotel Borobudur (Room 512)</strong></span>
                    <span>Check-in Terakhir: <strong class="text-[#0B7A77]">Kemarin, 14.15 WIB</strong></span>
                    <span>Kontak: <strong class="text-[#1E293B]">0813-9876-5432</strong></span>
                </div>
            </x-card>

            <x-card class="border-t-4 border-t-[#0066B3]">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#E8EEF3]">
                    <span class="font-bold text-[14px] text-[#123B72]">Nadia Maharani</span>
                    <x-badge status="Berjalan">Hari 1 dari 1</x-badge>
                </div>
                <div class="flex flex-col gap-1 text-[12.5px] text-[#64748B]">
                    <span>Lokasi: <strong class="text-[#1E293B]">Bogor</strong></span>
                    <span>Penginapan: <strong class="text-[#1E293B]">Dinas Luar Harian (PP)</strong></span>
                    <span>Check-in Terakhir: <strong class="text-[#0B7A77]">Hari ini, 08.00 WIB</strong></span>
                    <span>Kontak: <strong class="text-[#1E293B]">0821-1122-3344</strong></span>
                </div>
            </x-card>
        </div>

        <!-- Jadwal Keberangkatan Mendatang -->
        <x-card>
            <div class="pb-4 border-b border-[#E8EEF3] mb-4">
                <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">Jadwal Keberangkatan Mendatang</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Pegawai dengan SPPD terbit yang akan segera berangkat</p>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="border-b border-[#E8EEF3]">
                        <tr>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal Berangkat</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Transportasi</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status Tiket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 font-semibold text-[#1E293B]">Fajar Nugraha</td>
                            <td class="py-3 px-3 text-[#123B72]">Balikpapan</td>
                            <td class="py-3 px-3 text-[#64748B]">2 Nov 2026 &middot; 07.30 WIB</td>
                            <td class="py-3 px-3 text-[#64748B]">Garuda Indonesia (GA-512)</td>
                            <td class="py-3 px-3"><x-badge status="Disetujui">Issued & Terbit</x-badge></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.app>
