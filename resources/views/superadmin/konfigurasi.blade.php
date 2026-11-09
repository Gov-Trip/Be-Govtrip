<x-layouts.app title="Konfigurasi Sistem - Gov.Trip" role="Super Admin" headerTitle="Konfigurasi Sistem & Master SBM">
    <div class="flex flex-col gap-6">
        <!-- Master Kota & Plafon SBM 2026 -->
        <x-card padding="lg">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E8EEF3] gap-3 mb-5">
                <div>
                    <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Master Kota & Standar Biaya Masukan (SBM 2026)</h3>
                    <p class="text-[#64748B] text-[12.5px] mt-0.5">Plafon acuan baku kalkulasi otomatis uang harian, penginapan hotel, dan tiket transportasi</p>
                </div>
                <span class="text-[12px] font-semibold text-[#0066B3] bg-[#E3F0FA] px-3 py-1 rounded-full">
                    Sesuai PMK RI SBM TA 2026
                </span>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse text-[13px]">
                    <thead class="border-b border-[#E8EEF3] bg-slate-50">
                        <tr>
                            <th class="py-2.5 px-3 text-[#123B72] font-semibold">Kota Tujuan</th>
                            <th class="py-2.5 px-3 text-[#64748B] font-medium">Provinsi</th>
                            <th class="py-2.5 px-3 text-[#64748B] font-medium">Tingkat Jabatan</th>
                            <th class="py-2.5 px-3 text-[#64748B] font-medium text-right">Uang Harian / Hari</th>
                            <th class="py-2.5 px-3 text-[#64748B] font-medium text-right">Plafon Hotel / Malam</th>
                            <th class="py-2.5 px-3 text-[#64748B] font-medium text-right">Maks. Transport PP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8EEF3]">
                        @forelse($policies ?? [] as $pol)
                            <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                                <td class="py-3 px-3 font-semibold text-[#1E293B]">{{ $pol->destinationCity?->city_name ?? '-' }}</td>
                                <td class="py-3 px-3 text-[#64748B]">{{ $pol->destinationCity?->province_name ?? '-' }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $pol->grade_group === 'Manager' ? 'bg-[#FFF1D1] text-[#8A5A00]' : 'bg-[#E3F0FA] text-[#0066B3]' }}">
                                        {{ $pol->grade_group }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-[#1E293B]">
                                    Rp {{ number_format($pol->daily_allowance, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-[#1E293B]">
                                    Rp {{ number_format($pol->max_hotel_rate, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-[#0066B3]">
                                    Rp {{ number_format($pol->max_transport_rate, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-[#64748B]">Belum ada data kebijakan SBM yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Pengaturan Operasional & Aturan Bisnis -->
        <x-card padding="lg">
            <div class="pb-4 border-b border-[#E8EEF3] mb-6">
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Pengaturan Penomoran Dokumen & Bisnis</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Penetapan format penomoran SPPD otomatis dan batas waktu penyerahan LPJ</p>
            </div>

            <form onsubmit="event.preventDefault(); alert('Konfigurasi sistem berhasil disimpan ke sistem Gov.Trip!');" class="flex flex-col gap-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Penomoran & Batas Waktu -->
                    <div class="flex flex-col gap-4">
                        <span class="text-[13.5px] font-semibold text-[#123B72]">Format Dokumen & Batas Waktu</span>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Format Penomoran SPPD Digital</label>
                            <input type="text" value="090/SPPD-{urut}/{bulan}/{tahun}"
                                   class="px-3.5 py-2.5 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                            <span class="text-[11px] text-[#64748B] mt-1">Variabel: {urut}, {bulan}, {tahun}, {unit}</span>
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Batas Maksimal Penyampaian LPJ (Hari)</label>
                            <input type="number" value="7"
                                   class="px-3.5 py-2.5 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                            <span class="text-[11px] text-[#64748B] mt-1">Terhitung sejak tanggal kepulangan dinas</span>
                        </div>
                    </div>

                    <!-- Kebijakan & Fitur Sistem -->
                    <div class="flex flex-col gap-4">
                        <span class="text-[13.5px] font-semibold text-[#123B72]">Alur Persetujuan & Notifikasi</span>

                        <div class="flex flex-col gap-3 pt-1">
                            <label class="flex items-center justify-between p-3 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                <div>
                                    <span class="font-medium text-[#1E293B] block text-[13px]">Fast-Track Bypass Otomatis</span>
                                    <span class="text-[11.5px] text-[#64748B]">Berdasarkan matriks hak akses RBAC Super Admin</span>
                                </div>
                                <input type="checkbox" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                            </label>

                            <label class="flex items-center justify-between p-3 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                <div>
                                    <span class="font-medium text-[#1E293B] block text-[13px]">Verifikasi QR Code / TTE Resmi</span>
                                    <span class="text-[11.5px] text-[#64748B]">Menyematkan barcode validasi pada lembar cetak SPPD digital</span>
                                </div>
                                <input type="checkbox" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                            </label>

                            <label class="flex items-center justify-between p-3 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                <div>
                                    <span class="font-medium text-[#1E293B] block text-[13px]">Kunci Anggaran Logistik Otomatis</span>
                                    <span class="text-[11.5px] text-[#64748B]">Sinkronkan kalkulasi advance payment dengan SBM</span>
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
    </div>
</x-layouts.app>
