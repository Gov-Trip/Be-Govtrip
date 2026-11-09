<x-layouts.app title="LPJ Pegawai - Gov.Trip" role="Pegawai" headerTitle="Laporan Pertanggungjawaban (LPJ)">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Upload Form (col-span-8) -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <x-card>
                <div class="border-b border-[#E8EEF3] pb-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Unggah Dokumen LPJ</h3>
                            <p class="text-[#64748B] text-[12.5px] mt-0.5">Perjalanan: PD-2026/079 &middot; Makassar (14–16 Sep 2026)</p>
                        </div>
                        <x-badge status="LPJ perlu diunggah">Batas: 23 Sep</x-badge>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-5 p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('pegawai.lpj.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                    @csrf
                    <!-- Checklist berkas kelengkapan -->
                    <div class="flex flex-col gap-2.5 pb-3">
                        <span class="text-[13px] font-semibold text-[#123B72]">Kelengkapan Berkas Pertanggungjawaban:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-[12.5px]">
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                <input type="checkbox" name="has_tiket" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                                <span class="text-[#1E293B]">Boarding Pass / Tiket Transport</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                <input type="checkbox" name="has_kuitansi" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                                <span class="text-[#1E293B]">Kuitansi / Bill Hotel Resmi</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                <input type="checkbox" name="has_laporan" required class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                                <span class="text-[#1E293B]">Laporan Narasi Hasil Kegiatan</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                <input type="checkbox" name="has_dokumentasi" class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                                <span class="text-[#1E293B]">Foto Dokumentasi Pelaksanaan</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Total Uang Muka Diterima</label>
                            <input type="text" value="Rp 2.450.000" readonly class="px-3.5 py-2.5 bg-slate-50 border border-[#E8EEF3] rounded-[10px] text-[13.5px] text-[#64748B]" />
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Total Realisasi Pengeluaran</label>
                            <input type="number" name="total_real_expense" value="2300000" required class="px-3.5 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13.5px] text-[#1E293B] focus:outline-none focus:border-[#0066B3]" />
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Ringkasan Kegiatan & Hasil Dinas</label>
                        <textarea name="activity_summary" rows="3" required placeholder="Tuliskan ringkasan hasil kegiatan yang dicapai..."
                                  class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">Kegiatan monitoring evaluasi dinas terkait telah terlaksana 100%. Data implementasi berhasil dikumpulkan dan diintegrasikan ke sistem pusat.</textarea>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Unggah Berkas Gabungan Bukti Pengeluaran (PDF)</label>
                        <div class="border-2 border-dashed border-[#1684C4] bg-[#E3F0FA] hover:bg-[#d8ecf9] rounded-[12px] p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center"
                             onclick="document.getElementById('lpjFileInput').click()">
                            <input type="file" id="lpjFileInput" name="lpj_file" class="hidden" onchange="document.getElementById('lpjFileName').innerText = this.files[0]?.name || ''" />
                            <div class="w-10 h-10 rounded-full bg-white text-[#0066B3] flex items-center justify-center shadow-xs mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0066B3]">Pilih berkas LPJ atau tarik ke sini</span>
                            <span class="text-[11.5px] text-[#64748B] mt-0.5">Format PDF gabungan (kuitansi + tiket + laporan), maks. 10 MB</span>
                            <span id="lpjFileName" class="text-[12px] font-medium text-[#123B72] mt-2"></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E8EEF3]">
                        <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold rounded-[10px] bg-[#0066B3] hover:bg-[#005596] text-white shadow-sm transition-all cursor-pointer">
                            Kirim LPJ ke Bendahara
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Right: Riwayat LPJ Card (col-span-4) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card>
                <div class="border-b border-[#E8EEF3] pb-3 mb-4">
                    <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Riwayat LPJ Terverifikasi</h3>
                    <p class="text-[#64748B] text-[12px] mt-0.5">Arsip laporan pertanggungjawaban</p>
                </div>

                <div class="divide-y divide-[#E8EEF3] text-[13px]">
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-[#1E293B] block">Semarang</span>
                            <span class="text-[12px] text-[#64748B]">Agustus 2026</span>
                        </div>
                        <x-badge status="Terverifikasi">Terverifikasi</x-badge>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-[#1E293B] block">Jakarta</span>
                            <span class="text-[12px] text-[#64748B]">Juli 2026</span>
                        </div>
                        <x-badge status="Terverifikasi">Terverifikasi</x-badge>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-[#1E293B] block">Medan</span>
                            <span class="text-[12px] text-[#64748B]">Juni 2026</span>
                        </div>
                        <x-badge status="Terverifikasi">Terverifikasi</x-badge>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
