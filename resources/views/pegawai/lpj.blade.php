<x-layouts.app title="LPJ Pegawai - Gov.Trip" role="Pegawai" headerTitle="Laporan Pertanggungjawaban (LPJ)">
    @php
        $initAdvance = $selectedTrip ? (float)($selectedTrip->advance_payment > 0 ? $selectedTrip->advance_payment : $selectedTrip->estimated_total_cost) : 2450000;
        $initReal = $selectedTrip?->expenseReport?->total_real_expense ?? max(0, $initAdvance - 150000);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Upload Form (col-span-8) -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <x-card>
                <div class="border-b border-[#E8EEF3] pb-4 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Unggah & Pertanggungjawaban Berkas LPJ</h3>
                            <p class="text-[#64748B] text-[12.5px] mt-0.5">Sampaikan laporan riil dan kuitansi kegiatan perjalanan dinas Anda ke Bendahara Keuangan</p>
                        </div>
                        @if($selectedTrip)
                            <x-badge :status="$selectedTrip->status">{{ $selectedTrip->status }}</x-badge>
                        @endif
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-5 p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-4 rounded-xl bg-[#FDE8E8] text-[#9B1C1C] border border-[#f8b4b4] text-[13px] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($selectedTrip && $selectedTrip->status === 'Revisi LPJ')
                    <div class="mb-5 p-4 rounded-xl bg-[#FFF1D1] text-[#8A5A00] border border-[#ffe099] text-[13px] flex items-start gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mt-0.5 shrink-0"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div>
                            <span class="font-bold block">Catatan Revisi dari Bendahara Keuangan:</span>
                            <p class="mt-0.5 leading-relaxed">{{ $selectedTrip->rejection_reason ?? 'Terdapat berkas kuitansi yang kurang atau buram.' }}</p>
                        </div>
                    </div>
                @endif

                @if(empty($eligibleTrips) || $eligibleTrips->count() === 0)
                    <div class="py-12 text-center text-[#64748B]">
                        <p class="text-[14px] font-medium text-[#1E293B]">Tidak ada perjalanan dinas yang menunggu LPJ.</p>
                        <p class="text-[12.5px] mt-1">Hanya perjalanan yang sudah berstatus <strong>Siap Berangkat</strong> atau <strong>Revisi LPJ</strong> yang dapat diunggah laporannya.</p>
                    </div>
                @else
                    <form action="{{ route('pegawai.lpj.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                        @csrf

                        <!-- Pilih Perjalanan -->
                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Pilih Perjalanan Dinas</label>
                            <select name="travel_request_id" id="tripSelector" onchange="onSelectTrip(this.value)" class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">
                                @foreach($eligibleTrips as $trip)
                                    <option value="{{ $trip->id }}"
                                            data-advance="{{ (int)($trip->advance_payment > 0 ? $trip->advance_payment : $trip->estimated_total_cost) }}"
                                            data-dest="{{ $trip->destinationCity?->city_name }}"
                                            data-sppd="{{ $trip->sppd_number }}"
                                            data-status="{{ $trip->status }}"
                                            {{ ($selectedTrip && $selectedTrip->id === $trip->id) ? 'selected' : '' }}>
                                        {{ $trip->sppd_number ?? ('PD-'.strtoupper(substr($trip->id, 0, 6))) }} &middot; {{ $trip->destinationCity?->city_name }} (Status: {{ $trip->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Checklist berkas kelengkapan -->
                        <div class="flex flex-col gap-2.5 pb-2">
                            <span class="text-[13px] font-semibold text-[#123B72]">Kelengkapan Berkas Pertanggungjawaban:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-[12.5px]">
                                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                    <input type="checkbox" name="has_tiket" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                                    <span class="text-[#1E293B]">Boarding Pass / Tiket Transport PP</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                    <input type="checkbox" name="has_kuitansi" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                                    <span class="text-[#1E293B]">Kuitansi / Bill Hotel Resmi</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                    <input type="checkbox" name="has_laporan" checked required class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                                    <span class="text-[#1E293B]">Laporan Narasi Hasil Kegiatan</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-[#E8EEF3] bg-white cursor-pointer">
                                    <input type="checkbox" name="has_dokumentasi" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                                    <span class="text-[#1E293B]">Foto Dokumentasi Pelaksanaan</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Uang Muka Diterima (Pagu)</label>
                                <input type="text" id="displayAdvance" value="Rp {{ number_format($initAdvance, 0, ',', '.') }}" readonly
                                       class="px-3.5 py-2.5 bg-slate-50 border border-[#E8EEF3] rounded-[10px] text-[13.5px] text-[#64748B] font-semibold" />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Total Realisasi Pengeluaran Riil (Rp)</label>
                                <input type="number" id="inputRealExpense" name="total_real_expense" value="{{ (int)$initReal }}" required oninput="calcDifference()"
                                       class="px-3.5 py-2.5 bg-white border border-[#E8EEF3] rounded-[10px] text-[13.5px] text-[#1E293B] font-bold focus:outline-none focus:border-[#0066B3]" />
                            </div>
                        </div>

                        <!-- Info Selisih -->
                        <div class="p-3.5 rounded-[10px] bg-[#F1F7FB] border border-[#E8EEF3] flex items-center justify-between text-[13px]">
                            <span class="text-[#64748B] font-medium">Status Selisih (Setor Kas / Kurang Bayar):</span>
                            <span id="diffLabel" class="font-bold text-[#0B7A77]">Sisa Rp 150.000 (Setor kembali ke Kas)</span>
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Ringkasan Kegiatan & Hasil Dinas</label>
                            <textarea name="activity_summary" rows="3" required placeholder="Tuliskan ringkasan hasil kegiatan yang dicapai..."
                                      class="w-full px-3.5 py-2.5 text-[13.5px] text-[#1E293B] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">{{ $selectedTrip?->expenseReport?->activity_summary ?? 'Kegiatan koordinasi dinas telah terlaksana 100%. Seluruh target agenda tercapai dengan baik dan bukti pertanggungjawaban terlampir lengkap.' }}</textarea>
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[#64748B] text-[12.5px] font-medium mb-1.5">Unggah Berkas Gabungan Bukti Pengeluaran (PDF / Scan Kuitansi)</label>
                            <div class="border-2 border-dashed border-[#1684C4] bg-[#E3F0FA] hover:bg-[#d8ecf9] rounded-[12px] p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center"
                                 onclick="document.getElementById('lpjFileInput').click()">
                                <input type="file" id="lpjFileInput" name="lpj_file" class="hidden" onchange="document.getElementById('lpjFileName').innerText = this.files[0]?.name || ''" />
                                <div class="w-10 h-10 rounded-full bg-white text-[#0066B3] flex items-center justify-center shadow-xs mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                </div>
                                <span class="text-[13px] font-semibold text-[#0066B3]">Pilih berkas LPJ atau tarik ke sini</span>
                                <span class="text-[11.5px] text-[#64748B] mt-0.5">Format PDF gabungan (kuitansi + tiket + laporan), maks. 10 MB</span>
                                <span id="lpjFileName" class="text-[12px] font-medium text-[#123B72] mt-2"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E8EEF3]">
                            <button type="submit" class="px-6 py-2.5 text-[13px] font-semibold rounded-[10px] bg-[#0066B3] hover:bg-[#005596] text-white shadow-sm transition-all cursor-pointer">
                                Kirim LPJ ke Bendahara Keuangan
                            </button>
                        </div>
                    </form>
                @endif
            </x-card>
        </div>

        <!-- Right: Riwayat LPJ Card (col-span-4) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card>
                <div class="border-b border-[#E8EEF3] pb-3 mb-4">
                    <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Riwayat LPJ Anda</h3>
                    <p class="text-[#64748B] text-[12px] mt-0.5">Arsip laporan pertanggungjawaban</p>
                </div>

                <div class="divide-y divide-[#E8EEF3] text-[13px]">
                    @php
                        $archivedTrips = collect($eligibleTrips ?? [])->whereIn('status', ['Menunggu Verifikasi LPJ', 'Selesai']);
                    @endphp
                    @forelse($archivedTrips as $arch)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <span class="font-semibold text-[#1E293B] block">{{ $arch->destinationCity?->city_name }}</span>
                                <span class="text-[11.5px] text-[#64748B]">{{ $arch->sppd_number ?? ('PD-'.strtoupper(substr($arch->id, 0, 6))) }}</span>
                            </div>
                            <x-badge :status="$arch->status" />
                        </div>
                    @empty
                        <div class="py-4 text-center text-[#64748B] text-[12.5px]">
                            Belum ada riwayat LPJ sebelumnya.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

    <script>
        let currentAdvance = {{ (int)$initAdvance }};

        function onSelectTrip(tripId) {
            const selector = document.getElementById('tripSelector');
            const selectedOpt = selector.options[selector.selectedIndex];
            currentAdvance = parseInt(selectedOpt.getAttribute('data-advance')) || 0;
            document.getElementById('displayAdvance').value = 'Rp ' + currentAdvance.toLocaleString('id-ID');
            calcDifference();
        }

        function calcDifference() {
            const realVal = parseInt(document.getElementById('inputRealExpense').value) || 0;
            const diff = currentAdvance - realVal;
            const label = document.getElementById('diffLabel');

            if (diff > 0) {
                label.innerText = 'Sisa Uang Muka: Rp ' + diff.toLocaleString('id-ID') + ' (Wajib disetor ke Kas)';
                label.className = 'font-bold text-[#0B7A77]';
            } else if (diff < 0) {
                label.innerText = 'Kurang Bayar: Rp ' + Math.abs(diff).toLocaleString('id-ID') + ' (Akan diganti kas)';
                label.className = 'font-bold text-[#8A5A00]';
            } else {
                label.innerText = 'Nihil / Pas (Realisasi 100% sesuai uang muka)';
                label.className = 'font-bold text-[#0066B3]';
            }
        }

        document.addEventListener('DOMContentLoaded', calcDifference);
    </script>
</x-layouts.app>
