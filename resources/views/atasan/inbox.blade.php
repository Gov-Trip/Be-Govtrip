<x-layouts.app title="Kotak Masuk Persetujuan - Gov.Trip" role="Atasan" headerTitle="Kotak Masuk Persetujuan">
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
        <!-- Left: List Antrean (col-span-4) -->
        <div class="lg:col-span-4 flex flex-col gap-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-[13px] text-[#64748B] font-medium">Antrean Verifikasi ({{ $pendingRequests->count() }})</span>
            </div>

            @forelse($pendingRequests as $index => $req)
                @php
                    $isFirst = $index === 0;
                    $reqNumber = 'PD-2026/' . strtoupper(substr($req->id, 0, 6));
                    $dateRange = \Carbon\Carbon::parse($req->start_date)->translatedFormat('d M') . '–' . \Carbon\Carbon::parse($req->end_date)->translatedFormat('d M Y');
                @endphp
                <div onclick="selectSubmission('{{ $req->id }}')"
                     id="item-{{ $req->id }}"
                     data-subid="{{ $req->id }}"
                     data-number="{{ $reqNumber }} &middot; {{ $req->requester?->name }}"
                     data-unit="{{ $req->requester?->position }} &middot; {{ $req->requester?->department?->name }}"
                     data-dest="{{ $req->destinationCity?->city_name }} ({{ $req->destinationCity?->province_name }})"
                     data-dates="{{ $dateRange }}"
                     data-purpose="{{ $req->purpose }}"
                     data-total="Rp {{ number_format($req->estimated_total_cost, 0, ',', '.') }}"
                     data-rombongan="{{ $req->assignees->pluck('employee.name')->join(', ') }}"
                     class="inbox-card p-4 rounded-[12px] border {{ $isFirst ? 'bg-[#E3F0FA] border-[#0066B3] shadow-xs' : 'bg-white border-[#E8EEF3] hover:border-[#C9D8EC]' }} cursor-pointer transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-[14px] text-[#1E293B]">{{ $req->requester?->name }} &middot; {{ $req->destinationCity?->city_name }}</span>
                        <x-badge status="Menunggu">Menunggu</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">{{ $reqNumber }} &middot; {{ $dateRange }}</span>
                    <div class="flex justify-between items-center mt-2 text-[12px]">
                        <span class="text-[#64748B]">{{ $req->assignees->count() }} Anggota</span>
                        <span class="font-semibold text-[#0066B3]">Rp {{ number_format($req->estimated_total_cost, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white rounded-[12px] border border-[#E8EEF3] text-[#64748B] text-[13px]">
                    Tidak ada antrean persetujuan perjalanan dinas saat ini.
                </div>
            @endforelse
        </div>

        <!-- Right: Detail Panel (col-span-8) -->
        <div class="lg:col-span-8">
            @if($pendingRequests->isNotEmpty())
                @php $first = $pendingRequests->first(); @endphp
                <x-card padding="lg">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E8EEF3] gap-2 mb-5">
                        <div>
                            <h3 id="detailNumber" class="text-[17px] font-bold text-[#123B72]">
                                PD-2026/{{ strtoupper(substr($first->id, 0, 6)) }} &middot; {{ $first->requester?->name }}
                            </h3>
                            <p id="detailUnit" class="text-[12.5px] text-[#64748B]">
                                {{ $first->requester?->position }} &middot; {{ $first->requester?->department?->name }}
                            </p>
                        </div>
                        <x-badge status="Menunggu">Menunggu Persetujuan Atasan</x-badge>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 text-[13px]">
                        <div>
                            <span class="text-[#64748B] block text-[12px]">Kota Tujuan</span>
                            <span id="detailDest" class="font-semibold text-[#1E293B]">
                                {{ $first->destinationCity?->city_name }} ({{ $first->destinationCity?->province_name }})
                            </span>
                        </div>
                        <div>
                            <span class="text-[#64748B] block text-[12px]">Tanggal Pelaksanaan</span>
                            <span id="detailDates" class="font-semibold text-[#1E293B]">
                                {{ \Carbon\Carbon::parse($first->start_date)->translatedFormat('d M') }} &ndash; {{ \Carbon\Carbon::parse($first->end_date)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-[#64748B] block text-[12px]">Anggota Rombongan</span>
                            <span id="detailRombongan" class="font-medium text-[#1E293B]">
                                {{ $first->assignees->pluck('employee.name')->join(', ') ?: $first->requester?->name }}
                            </span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-[#64748B] block text-[12px]">Maksud / Keperluan Tugas</span>
                            <p id="detailPurpose" class="font-medium text-[#1E293B] mt-0.5 leading-relaxed">
                                {{ $first->purpose }}
                            </p>
                        </div>
                    </div>

                    <!-- Cost Breakdown Table -->
                    <div class="py-4 border-t border-[#E8EEF3]">
                        <span class="text-[13px] font-semibold text-[#123B72] block mb-2">Total Estimasi Plafon SBM</span>
                        <div class="w-full overflow-x-auto">
                            <table class="w-full text-[13px] text-left border-collapse">
                                <tfoot class="border border-[#E8EEF3] bg-slate-50 font-bold">
                                    <tr>
                                        <td class="py-2.5 px-3 text-[#123B72]">Total Estimasi Biaya Rombongan</td>
                                        <td id="detailTotal" class="py-2.5 px-3 text-right text-[#0066B3] text-[15px]">
                                            Rp {{ number_format($first->estimated_total_cost, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Decision Form -->
                    <form action="{{ route('atasan.decision.post') }}" method="POST" class="pt-4 border-t border-[#E8EEF3] flex flex-col gap-3">
                        @csrf
                        <input type="hidden" name="submission_id" id="formSubId" value="{{ $first->id }}" />
                        <input type="hidden" name="decision" id="formDecision" value="Disetujui" />

                        <div>
                            <label class="text-[#64748B] text-[12px] font-medium block mb-1">Catatan Keputusan (Wajib bila Menolak/Minta Revisi)</label>
                            <textarea name="notes" id="formNotes" rows="2" placeholder="Tulis catatan persetujuan atau alasan revisi..."
                                      class="w-full px-3 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]">Disetujui. Laksanakan tugas kedinasan dengan penuh tanggung jawab sesuai agenda yang direncanakan.</textarea>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2">
                            <button type="submit" onclick="document.getElementById('formDecision').value = 'Ditolak'"
                                    class="px-3.5 py-2 text-[12.5px] font-semibold text-[#B02A2A] border border-[#B02A2A] hover:bg-[#FBE8E8] rounded-[10px] transition-colors cursor-pointer">
                                Tolak Pengajuan
                            </button>
                            <button type="submit" onclick="document.getElementById('formDecision').value = 'Perlu Revisi'"
                                    class="px-3.5 py-2 text-[12.5px] font-semibold text-[#8A5A00] bg-[#FFF1D1] hover:bg-[#fee7b3] rounded-[10px] transition-colors cursor-pointer">
                                Minta Revisi
                            </button>
                            <button type="submit" onclick="document.getElementById('formDecision').value = 'Disetujui'"
                                    class="px-5 py-2 text-[12.5px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-colors cursor-pointer">
                                Setujui Pengajuan (Terbitkan SPPD)
                            </button>
                        </div>
                    </form>
                </x-card>
            @else
                <x-card padding="lg">
                    <div class="py-16 text-center text-[#64748B]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" class="mx-auto text-slate-300 mb-3"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        <h4 class="font-bold text-[#1E293B] text-[15px]">Semua Pengajuan Telah Diproses</h4>
                        <p class="text-[12.5px] mt-1">Tidak ada dokumen perjalanan dinas bawahan yang menunggu keputusan Anda saat ini.</p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>

    <script>
        function selectSubmission(id) {
            document.querySelectorAll('.inbox-card').forEach(c => {
                c.className = 'inbox-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all';
            });

            const active = document.getElementById('item-' + id);
            if (active) {
                active.className = 'inbox-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all';

                document.getElementById('formSubId').value = active.getAttribute('data-subid');
                document.getElementById('detailNumber').innerHTML = active.getAttribute('data-number');
                document.getElementById('detailUnit').innerText = active.getAttribute('data-unit');
                document.getElementById('detailDest').innerText = active.getAttribute('data-dest');
                document.getElementById('detailDates').innerText = active.getAttribute('data-dates');
                document.getElementById('detailPurpose').innerText = active.getAttribute('data-purpose');
                document.getElementById('detailTotal').innerText = active.getAttribute('data-total');
                document.getElementById('detailRombongan').innerText = active.getAttribute('data-rombongan') || '-';
            }
        }
    </script>
</x-layouts.app>
