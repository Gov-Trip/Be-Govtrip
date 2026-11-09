<x-layouts.app title="Verifikasi Berkas LPJ - Gov.Trip" role="Bendahara" headerTitle="Verifikasi Berkas LPJ">
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Antrean LPJ (col-span-4) -->
        <div class="lg:col-span-4 flex flex-col gap-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-[13px] text-[#64748B] font-medium">Antrean Masuk ({{ $queue->count() }})</span>
                <span class="text-[11.5px] font-semibold text-[#0066B3] bg-[#E3F0FA] px-2 py-0.5 rounded-full">Perlu Verifikasi</span>
            </div>

            @forelse($queue as $index => $item)
                @php
                    $isFirst = $index === 0;
                    $lpjCode = 'LPJ-2026/' . strtoupper(substr($item->id, 0, 6));
                    $advance = (float)($item->advance_payment > 0 ? $item->advance_payment : $item->estimated_total_cost);
                    $real = (float)($item->expenseReport?->total_real_expense ?? $advance);
                    $diff = $advance - $real;
                @endphp
                <div onclick="selectLpj('{{ $item->id }}')"
                     id="card-{{ $item->id }}"
                     data-lpjid="{{ $item->id }}"
                     data-title="{{ $lpjCode }} &middot; {{ $item->requester?->name }}"
                     data-dest="Perjalanan Dinas ke {{ $item->destinationCity?->city_name }} ({{ $item->destinationCity?->province_name }})"
                     data-pagu="Rp {{ number_format($advance, 0, ',', '.') }}"
                     data-real="Rp {{ number_format($real, 0, ',', '.') }}"
                     data-diff="Rp {{ number_format($diff, 0, ',', '.') }}"
                     data-diffval="{{ $diff }}"
                     data-sppd="{{ $item->sppd_number ?? '-' }}"
                     data-summary="{{ $item->expenseReport?->activity_summary ?? $item->purpose }}"
                     class="lpj-card p-4 rounded-[12px] border {{ $isFirst ? 'bg-[#E3F0FA] border-[#0066B3] shadow-xs' : 'bg-white border-[#E8EEF3] hover:border-[#C9D8EC]' }} cursor-pointer transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-[14px] text-[#1E293B]">{{ $item->requester?->name }} &middot; {{ $item->destinationCity?->city_name }}</span>
                        <x-badge status="Menunggu">Perlu verifikasi</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">{{ $lpjCode }} &middot; {{ $item->sppd_number ?? 'SPPD Aktif' }}</span>
                    <div class="flex justify-between items-center mt-2 text-[12px]">
                        <span class="text-[#64748B]">Realisasi Riil:</span>
                        <span class="font-semibold text-[#0066B3]">Rp {{ number_format($real, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white rounded-[12px] border border-[#E8EEF3] text-[#64748B] text-[13px]">
                    Tidak ada antrean verifikasi LPJ saat ini.
                </div>
            @endforelse
        </div>

        <!-- Right: Detail Pemeriksaan (col-span-8) -->
        <div class="lg:col-span-8">
            @if($queue->isNotEmpty())
                @php
                    $active = $queue->first();
                    $activeAdvance = (float)($active->advance_payment > 0 ? $active->advance_payment : $active->estimated_total_cost);
                    $activeReal = (float)($active->expenseReport?->total_real_expense ?? $activeAdvance);
                    $activeDiff = $activeAdvance - $activeReal;
                @endphp
                <x-card padding="lg">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E8EEF3] gap-2 mb-5">
                        <div>
                            <h3 id="lpjTitle" class="text-[17px] font-bold text-[#123B72]">
                                LPJ-2026/{{ strtoupper(substr($active->id, 0, 6)) }} &middot; {{ $active->requester?->name }}
                            </h3>
                            <p id="lpjDest" class="text-[12.5px] text-[#64748B]">
                                Perjalanan Dinas ke {{ $active->destinationCity?->city_name }} ({{ $active->destinationCity?->province_name }})
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-badge status="Menunggu">Pemeriksaan Berkas SPJ</x-badge>
                            @if(!empty($active->sppd_number))
                                <a href="{{ route('travel-requests.download-sppd', $active->id) }}" target="_blank"
                                   class="px-3 py-1.5 text-[12px] font-medium text-[#0066B3] bg-[#E3F0FA] hover:bg-[#cde4f7] rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <span>Lihat SPPD</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Comparison Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                        <div class="p-3.5 rounded-[10px] bg-[#F1F7FB] border border-[#E8EEF3]">
                            <span class="text-[11.5px] text-[#64748B] block font-medium">Uang Muka Diterima (Pagu)</span>
                            <span id="lpjPagu" class="text-[16px] font-bold text-[#123B72] mt-0.5 block">
                                Rp {{ number_format($activeAdvance, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="p-3.5 rounded-[10px] bg-[#DFF5F4] border border-[#a2dedc]">
                            <span class="text-[11.5px] text-[#0B7A77] block font-medium">Realisasi Pengeluaran Riil</span>
                            <span id="lpjReal" class="text-[16px] font-bold text-[#0B7A77] mt-0.5 block">
                                Rp {{ number_format($activeReal, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="p-3.5 rounded-[10px] bg-[#FFF1D1] border border-[#ffe099]">
                            <span class="text-[11.5px] text-[#8A5A00] block font-medium">Sisa Uang Muka (Setor Kas)</span>
                            <span id="lpjDiff" class="text-[16px] font-bold text-[#8A5A00] mt-0.5 block">
                                Rp {{ number_format($activeDiff, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Ringkasan Kegiatan -->
                    <div class="mb-5 p-3.5 rounded-[10px] bg-slate-50 border border-[#E8EEF3] text-[12.5px]">
                        <span class="text-[#64748B] block font-semibold mb-1">Narasi Ringkasan Pelaksanaan Tugas:</span>
                        <p id="lpjSummary" class="text-[#1E293B] leading-relaxed">
                            {{ $active->expenseReport?->activity_summary ?? $active->purpose }}
                        </p>
                    </div>

                    <!-- Checklist Bukti Fisik -->
                    <div class="border-t border-[#E8EEF3] pt-4 mb-5">
                        <span class="text-[13px] font-semibold text-[#123B72] block mb-3">Status Verifikasi Bukti Fisik SPJ (Dicocokkan SBM):</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[12.5px]">
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8EEF3] bg-white">
                                <span class="text-[#1E293B]">Tiket / Boarding Pass PP</span>
                                <x-badge status="Lengkap">Valid</x-badge>
                            </div>
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8EEF3] bg-white">
                                <span class="text-[#1E293B]">Kuitansi Penginapan / Hotel</span>
                                <x-badge status="Lengkap">Valid</x-badge>
                            </div>
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8EEF3] bg-white">
                                <span class="text-[#1E293B]">Laporan Hasil Perjalanan Dinas</span>
                                <x-badge status="Lengkap">Valid</x-badge>
                            </div>
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8EEF3] bg-white">
                                <span class="text-[#1E293B]">Foto Dokumentasi Kegiatan</span>
                                <x-badge status="Lengkap">Lengkap</x-badge>
                            </div>
                        </div>
                    </div>

                    <!-- Action Form -->
                    <form action="{{ route('bendahara.verifikasi.post') }}" method="POST" class="pt-4 border-t border-[#E8EEF3] flex flex-col gap-3">
                        @csrf
                        <input type="hidden" name="lpj_id" id="formLpjId" value="{{ $active->id }}" />
                        <input type="hidden" name="action" id="formLpjAction" value="APPROVE" />
                        <input type="hidden" name="advance_payment" id="formAdvance" value="{{ (int)$activeAdvance }}" />

                        <div>
                            <label class="text-[#64748B] text-[12px] font-medium block mb-1">Catatan Audit Keuangan</label>
                            <textarea name="notes" id="formNotes" rows="2" placeholder="Catatan hasil verifikasi kuitansi dan bukti transfer..."
                                      class="w-full px-3 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">Bukti kuitansi dan tiket telah dicocokkan dengan standar SBM 2026. Sisa saldo uang muka telah disetorkan kembali ke kas daerah/kantor.</textarea>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                            <button type="submit" onclick="document.getElementById('formLpjAction').value = 'REJECT'"
                                    class="px-4 py-2 text-[12.5px] font-semibold text-[#B02A2A] border border-[#B02A2A] hover:bg-[#FBE8E8] rounded-[10px] transition-colors cursor-pointer">
                                Tolak / Minta Revisi LPJ
                            </button>
                            <button type="submit" onclick="document.getElementById('formLpjAction').value = 'APPROVE'"
                                    class="px-5 py-2 text-[12.5px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-colors cursor-pointer">
                                Verifikasi & Sahkan LPJ (Status: Selesai)
                            </button>
                        </div>
                    </form>
                </x-card>
            @else
                <x-card padding="lg">
                    <div class="py-16 text-center text-[#64748B]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" class="mx-auto text-slate-300 mb-3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <h4 class="font-bold text-[#1E293B] text-[15px]">Semua LPJ Telah Diverifikasi</h4>
                        <p class="text-[12.5px] mt-1">Tidak ada dokumen pertanggungjawaban yang menunggu validasi bendahara saat ini.</p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>

    <script>
        function selectLpj(id) {
            document.querySelectorAll('.lpj-card').forEach(c => {
                c.className = 'lpj-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all';
            });
            const active = document.getElementById('card-' + id);
            if (active) {
                active.className = 'lpj-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all';

                const lpjid = active.getAttribute('data-lpjid');
                document.getElementById('formLpjId').value = lpjid;
                document.getElementById('lpjTitle').innerHTML = active.getAttribute('data-title');
                document.getElementById('lpjDest').innerText = active.getAttribute('data-dest');
                document.getElementById('lpjPagu').innerText = active.getAttribute('data-pagu');
                document.getElementById('lpjReal').innerText = active.getAttribute('data-real');
                document.getElementById('lpjDiff').innerText = active.getAttribute('data-diff');
                document.getElementById('lpjSummary').innerText = active.getAttribute('data-summary') || '-';
            }
        }
    </script>
</x-layouts.app>
