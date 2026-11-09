<x-layouts.app title="Konfigurasi Sistem - Gov.Trip" role="Super Admin" headerTitle="Konfigurasi Sistem">
    <x-card padding="lg">
        <div class="pb-4 border-b border-[#E8EEF3] mb-6">
            <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Pengaturan Operasional & Aturan Bisnis</h3>
            <p class="text-[#64748B] text-[12.5px] mt-0.5">Penetapan format penomoran dokumen, tenggat waktu LPJ, dan alur validasi dinas</p>
        </div>

        <form onsubmit="event.preventDefault(); alert('Konfigurasi sistem berhasil disimpan ke sistem Gov.Trip!');" class="flex flex-col gap-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Penomoran & Batas Waktu -->
                <div class="flex flex-col gap-4">
                    <span class="text-[13.5px] font-semibold text-[#123B72]">Format Dokumen & Batas Waktu</span>

                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Format Penomoran SPPD Digital</label>
                        <input type="text" value="090/{urut}/SPPD/{bulan}/{tahun}"
                               class="px-3.5 py-2.5 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        <span class="text-[11px] text-[#64748B] mt-1">Variabel tersedia: {urut}, {bulan}, {tahun}, {unit}</span>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Batas Maksimal Penyampaian LPJ (Hari)</label>
                        <input type="number" value="7"
                               class="px-3.5 py-2.5 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                        <span class="text-[11px] text-[#64748B] mt-1">Terhitung sejak tanggal selesai pelaksanaan perjalanan</span>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Pengingat Otomatis Sebelum Jatuh Tempo (Hari)</label>
                        <input type="number" value="2"
                               class="px-3.5 py-2.5 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                    </div>
                </div>

                <!-- Kebijakan & Fitur Sistem -->
                <div class="flex flex-col gap-4">
                    <span class="text-[13.5px] font-semibold text-[#123B72]">Alur Persetujuan & Notifikasi</span>

                    <div class="flex flex-col gap-3 pt-1">
                        <label class="flex items-center justify-between p-3 rounded-lg border border-[#E8EEF3] cursor-pointer">
                            <div>
                                <span class="font-medium text-[#1E293B] block text-[13px]">Wajib Persetujuan Kepala Dinas</span>
                                <span class="text-[11.5px] text-[#64748B]">Untuk perjalanan dinas luar provinsi</span>
                            </div>
                            <input type="checkbox" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                        </label>

                        <label class="flex items-center justify-between p-3 rounded-lg border border-[#E8EEF3] cursor-pointer">
                            <div>
                                <span class="font-medium text-[#1E293B] block text-[13px]">Verifikasi QR Code / TTE Resmi</span>
                                <span class="text-[11.5px] text-[#64748B]">Menyematkan barcode verifikasi pada lembar SPPD</span>
                            </div>
                            <input type="checkbox" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                        </label>

                        <label class="flex items-center justify-between p-3 rounded-lg border border-[#E8EEF3] cursor-pointer">
                            <div>
                                <span class="font-medium text-[#1E293B] block text-[13px]">Pengiriman Email Notifikasi Otomatis</span>
                                <span class="text-[11.5px] text-[#64748B]">Kirim notifikasi setiap terjadi perubahan status</span>
                            </div>
                            <input type="checkbox" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-[#E8EEF3]">
                <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-all cursor-pointer">
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
