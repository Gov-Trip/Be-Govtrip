<x-layouts.app title="Dokumen Perjalanan - Gov.Trip" role="Admin" headerTitle="Dokumen Perjalanan">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Kelengkapan Dokumen Perjalanan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Pemantauan surat tugas, tiket, dan nota dinas antar instansi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-[12px] border border-[#E8EEF3] bg-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-[14px] text-[#123B72]">PD-2026/087 &middot; Yogyakarta</span>
                        <x-badge status="Disetujui">Lengkap</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">Pegawai: Liyana Agustin (Bidang Perencanaan)</span>
                    <div class="mt-3 divide-y divide-[#E8EEF3] text-[12.5px]">
                        <div class="py-1.5 flex justify-between">
                            <span class="text-[#1E293B]">Surat Tugas Resmi</span>
                            <span class="text-[#0B7A77] font-medium">&check; Lengkap</span>
                        </div>
                        <div class="py-1.5 flex justify-between">
                            <span class="text-[#1E293B]">Tiket Kereta Eksekutif</span>
                            <span class="text-[#0B7A77] font-medium">&check; Terbit</span>
                        </div>
                        <div class="py-1.5 flex justify-between">
                            <span class="text-[#1E293B]">SPPD Resmi TTE</span>
                            <span class="text-[#0B7A77] font-medium">&check; Ditandatangani</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-[12px] border border-[#E8EEF3] bg-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-[14px] text-[#123B72]">PD-2026/091 &middot; Bandung</span>
                        <x-badge status="Belum lengkap">Belum lengkap</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">Pegawai: Rudi Hermawan (Bidang Perencanaan)</span>
                    <div class="mt-3 divide-y divide-[#E8EEF3] text-[12.5px]">
                        <div class="py-1.5 flex justify-between">
                            <span class="text-[#1E293B]">Surat Tugas Resmi</span>
                            <span class="text-[#B02A2A] font-medium">&cross; Belum diunggah</span>
                        </div>
                        <div class="py-1.5 flex justify-between">
                            <span class="text-[#1E293B]">Tiket Kereta Api</span>
                            <span class="text-[#8A5A00] font-medium">Menunggu Surat</span>
                        </div>
                        <div class="py-1.5 flex justify-between">
                            <span class="text-[#1E293B]">SPPD Resmi</span>
                            <span class="text-[#8A5A00] font-medium">Draft</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-card>
</x-layouts.app>
