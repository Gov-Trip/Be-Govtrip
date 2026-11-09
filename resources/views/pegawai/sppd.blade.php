<x-layouts.app title="SPPD Digital - Gov.Trip" role="Pegawai" headerTitle="SPPD Digital">
    <div class="flex flex-col gap-6">
        <!-- Active SPPD Showcase Card -->
        <x-card padding="lg" class="border-l-4 border-l-[#0066B3]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E8EEF3] gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#E3F0FA] text-[#0066B3] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    <div>
                        <h3 class="text-[#123B72] text-[17px] font-bold">SPPD Resmi: 090/087/SPPD/IX/2026</h3>
                        <span class="text-[#64748B] text-[12.5px]">Tujuan: Yogyakarta &middot; 21–23 Sep 2026</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-badge status="Terbit">Terbit Resmi</x-badge>
                    <a href="/api/travel-requests/1/download-sppd" target="_blank"
                       class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[10px] transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>Cetak / Unduh PDF</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 pt-5 text-[13px]">
                <div>
                    <span class="text-[#64748B] block text-[12px]">Pelaksana Dinas</span>
                    <span class="font-semibold text-[#1E293B] block mt-0.5">Andi Pratama</span>
                    <span class="text-[12px] text-[#64748B]">NIP: 19880412 201101 2 002</span>
                </div>
                <div>
                    <span class="text-[#64748B] block text-[12px]">Unit Kerja / SKPD</span>
                    <span class="font-semibold text-[#1E293B] block mt-0.5">Bidang Perencanaan & Program</span>
                    <span class="text-[12px] text-[#64748B]">Instansi Pemerintah Daerah</span>
                </div>
                <div>
                    <span class="text-[#64748B] block text-[12px]">Pejabat Penandatangan</span>
                    <span class="font-semibold text-[#1E293B] block mt-0.5">Dr. H. Bambang Soeprapto, M.Si.</span>
                    <span class="text-[12px] text-[#64748B]">Kepala Dinas</span>
                </div>
                <div class="sm:col-span-2 md:col-span-3 pt-2">
                    <span class="text-[#64748B] block text-[12px]">Keperluan / Dasar Pelaksanaan</span>
                    <p class="font-medium text-[#1E293B] mt-0.5 leading-relaxed">
                        Sosialisasi regulasi perjalanan dinas dan standar biaya masukan bersama Kemenkeu serta rapat koordinasi percepatan pelaksanaan program daerah.
                    </p>
                </div>
            </div>
        </x-card>

        <!-- Riwayat SPPD Card -->
        <x-card>
            <div class="pb-4 border-b border-[#E8EEF3] mb-4">
                <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Arsip SPPD Digital</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Daftar Surat Perintah Perjalanan Dinas yang telah terbit sebelumnya</p>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="border-b border-[#E8EEF3]">
                        <tr>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor SPPD</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pejabat</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 font-semibold text-[#123B72]">090/079/SPPD/IX/2026</td>
                            <td class="py-3 px-3 font-medium text-[#1E293B]">Makassar</td>
                            <td class="py-3 px-3 text-[#64748B]">14–16 Sep 2026</td>
                            <td class="py-3 px-3 text-[#64748B]">Kepala Dinas</td>
                            <td class="py-3 px-3"><x-badge status="Terbit">Terbit</x-badge></td>
                            <td class="py-3 px-3 text-right">
                                <a href="/api/travel-requests/1/download-sppd" target="_blank" class="text-[#0066B3] hover:underline font-medium text-[12px]">Unduh</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 font-semibold text-[#123B72]">090/065/SPPD/VIII/2026</td>
                            <td class="py-3 px-3 font-medium text-[#1E293B]">Semarang</td>
                            <td class="py-3 px-3 text-[#64748B]">12–13 Agu 2026</td>
                            <td class="py-3 px-3 text-[#64748B]">Kepala Dinas</td>
                            <td class="py-3 px-3"><x-badge status="Terbit">Terbit</x-badge></td>
                            <td class="py-3 px-3 text-right">
                                <a href="/api/travel-requests/1/download-sppd" target="_blank" class="text-[#0066B3] hover:underline font-medium text-[12px]">Unduh</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.app>
