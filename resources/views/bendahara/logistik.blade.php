<x-layouts.app title="ACC Logistik & Pencairan - Gov.Trip" role="Bendahara" headerTitle="ACC Logistik & Pencairan Uang Muka">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Daftar Permintaan (col-span-5) -->
        <div class="lg:col-span-5 flex flex-col gap-3">
            <span class="text-[13px] text-[#64748B] font-medium px-1">Permintaan Tiket & Uang Muka Baru</span>

            <div class="p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Fajar &middot; Balikpapan</span>
                    <x-badge status="Menunggu">Perlu Logistik</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">PD-2026/090 &middot; 2–4 Nov 2026</span>
                <div class="flex justify-between items-center mt-2 text-[12px]">
                    <span class="text-[#64748B]">Estimasi Pagu:</span>
                    <span class="font-semibold text-[#0066B3]">Rp 5.400.000</span>
                </div>
            </div>

            <div class="p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[14px] text-[#1E293B]">Made &middot; Manado</span>
                    <x-badge status="Menunggu">Perlu Logistik</x-badge>
                </div>
                <span class="text-[12px] text-[#64748B] block">PD-2026/092 &middot; 9–11 Nov 2026</span>
                <div class="flex justify-between items-center mt-2 text-[12px]">
                    <span class="text-[#64748B]">Estimasi Pagu:</span>
                    <span class="font-semibold text-[#0066B3]">Rp 6.800.000</span>
                </div>
            </div>
        </div>

        <!-- Right: Detail ACC (col-span-7) -->
        <div class="lg:col-span-7">
            <x-card padding="lg">
                <div class="pb-4 border-b border-[#E8EEF3] mb-5">
                    <h3 class="text-[17px] font-bold text-[#123B72]">PD-2026/090 &middot; Fajar</h3>
                    <p class="text-[12.5px] text-[#64748B] mt-0.5">Penugasan Dinas Luar Kota ke Balikpapan</p>
                </div>

                <div class="flex flex-col gap-3 text-[13px] mb-5">
                    <div class="flex justify-between py-2 border-b border-[#E8EEF3]">
                        <span class="text-[#64748B]">Transportasi Diajukan</span>
                        <span class="font-semibold text-[#1E293B]">Pesawat Terbang Ekonomi (Garuda / Citilink)</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8EEF3]">
                        <span class="text-[#64748B]">Akomodasi Hotel</span>
                        <span class="font-semibold text-[#1E293B]">Hotel Bintang 3 (Sesuai SBM Gol. III)</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8EEF3]">
                        <span class="text-[#64748B]">Nominal Uang Muka Pencairan</span>
                        <span class="font-bold text-[#0066B3] text-[15px]">Rp 5.400.000</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-[10px] bg-[#DFF5F4] text-[#0B7A77] text-[12.5px] mb-5 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                    <span>Dana kas instansi tersedia dan pagu DPA mencukupi untuk dicairkan.</span>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-[#E8EEF3]">
                    <button type="button" onclick="alert('Permintaan logistik ditolak.');"
                            class="px-3.5 py-2 text-[12.5px] font-semibold text-[#B02A2A] border border-[#B02A2A] hover:bg-[#FBE8E8] rounded-[10px] transition-colors cursor-pointer">
                        Tolak
                    </button>
                    <button type="button" onclick="alert('Uang muka berhasil disetujui & dicairkan ke rekening pegawai!');"
                            class="px-4 py-2 text-[12.5px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-colors cursor-pointer">
                        ACC Pencairan Uang Muka
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
