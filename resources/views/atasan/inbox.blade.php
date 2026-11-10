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
                @if(isset($roleLabel))
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-[#E3F0FA] text-[#0066B3]">
                        Login: {{ $roleLabel }}
                    </span>
                @endif
            </div>

            <!-- Filter Pills Level Approval -->
            <div class="flex flex-wrap items-center gap-1.5 px-1">
                <button type="button" onclick="filterInbox('all')" class="inbox-filter-btn px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#123B72] text-white transition-colors cursor-pointer" data-filter="all">Semua ({{ $pendingRequests->count() }})</button>
                <button type="button" onclick="filterInbox('Atasan')" class="inbox-filter-btn px-2.5 py-1 rounded-md text-[11px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Atasan">Atasan</button>
                <button type="button" onclick="filterInbox('Kabid')" class="inbox-filter-btn px-2.5 py-1 rounded-md text-[11px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Kabid">Kabid</button>
                <button type="button" onclick="filterInbox('Kadis')" class="inbox-filter-btn px-2.5 py-1 rounded-md text-[11px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Kadis">Kadis</button>
            </div>

            @forelse($pendingRequests as $index => $req)
                @php
                    $isFirst = $index === 0;
                    $reqNumber = 'PD-2026/' . strtoupper(substr($req->id, 0, 6));
                    $dateRange = \Carbon\Carbon::parse($req->start_date)->translatedFormat('d M') . '–' . \Carbon\Carbon::parse($req->end_date)->translatedFormat('d M Y');
                    $apprList = $req->approvals->map(fn($a) => [
                        'name' => $a->approver?->name ?? 'Pejabat Berwenang',
                        'pos' => $a->approver?->position ?? '',
                        'decision' => $a->status,
                        'notes' => $a->notes,
                        'time' => $a->created_at ? $a->created_at->translatedFormat('d M H:i') : ''
                    ])->values();
                @endphp
                <div onclick="selectSubmission('{{ $req->id }}')"
                     id="item-{{ $req->id }}"
                     data-subid="{{ $req->id }}"
                     data-status="{{ $req->status }}"
                     data-number="{{ $reqNumber }} &middot; {{ $req->requester?->name }}"
                     data-unit="{{ $req->requester?->position }} &middot; {{ $req->requester?->department?->name }}"
                     data-dest="{{ $req->destinationCity?->city_name }} ({{ $req->destinationCity?->province_name }})"
                     data-dates="{{ $dateRange }}"
                     data-purpose="{{ $req->purpose }}"
                     data-total="Rp {{ number_format($req->estimated_total_cost, 0, ',', '.') }}"
                     data-rombongan="{{ $req->assignees->pluck('employee.name')->join(', ') }}"
                     data-att-url="{{ $req->attachments->first() ? asset('storage/'.$req->attachments->first()->file_path) : '' }}"
                     data-att-name="{{ $req->attachments->first() ? basename($req->attachments->first()->file_path) : '' }}"
                     data-approvals="{{ json_encode($apprList) }}"
                     class="inbox-card p-4 rounded-[12px] border {{ $isFirst ? 'bg-[#E3F0FA] border-[#0066B3] shadow-xs' : 'bg-white border-[#E8EEF3] hover:border-[#C9D8EC]' }} cursor-pointer transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-[14px] text-[#1E293B]">{{ $req->requester?->name }} &middot; {{ $req->destinationCity?->city_name }}</span>
                        <x-badge :status="$req->status">{{ $req->status }}</x-badge>
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
                        <span id="detailStatusBadge"><x-badge :status="$first->status">{{ $first->status }}</x-badge></span>
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
                        <div id="detailAttachmentSection" class="sm:col-span-2 pt-2 border-t border-[#E8EEF3] {{ ($first->attachments && $first->attachments->isNotEmpty()) ? '' : 'hidden' }}">
                            <span class="text-[#64748B] block text-[12px] font-medium mb-1.5">Dokumen Pendukung / Surat Undangan</span>
                            <a id="detailAttachmentLink" href="{{ ($first->attachments && $first->attachments->isNotEmpty()) ? asset('storage/'.$first->attachments->first()->file_path) : '#' }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#E3F0FA] text-[#0066B3] text-[12px] font-semibold hover:bg-[#d0e5f5] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <span id="detailAttachmentName">{{ ($first->attachments && $first->attachments->isNotEmpty()) ? basename($first->attachments->first()->file_path) : 'Unduh Dokumen' }}</span>
                            </a>
                        </div>
                        <div id="detailApprovalsSection" class="sm:col-span-2 pt-2 border-t border-[#E8EEF3] {{ ($first->approvals && $first->approvals->isNotEmpty()) ? '' : 'hidden' }}">
                            <span class="text-[#123B72] block text-[12px] font-semibold mb-1.5 flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span>Jejak Persetujuan Berjenjang:</span>
                            </span>
                            <div id="detailApprovalsList" class="flex flex-col gap-1.5">
                                @if($first->approvals)
                                    @foreach($first->approvals as $appr)
                                        <div class="p-2 rounded-lg bg-slate-50 border border-[#E8EEF3] text-[12px] flex items-start justify-between gap-2">
                                            <div>
                                                <span class="font-semibold text-[#1E293B] block">{{ $appr->approver?->name }} ({{ $appr->approver?->position }})</span>
                                                @if($appr->notes)
                                                    <span class="text-[#64748B] italic">"{{ $appr->notes }}"</span>
                                                @endif
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DFF5F4] text-[#0B7A77] shrink-0">{{ $appr->status }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
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
                            <button type="submit" id="btnApprove" onclick="document.getElementById('formDecision').value = 'Disetujui'"
                                    class="px-5 py-2 text-[12.5px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                                <span id="btnApproveText">
                                    @if(str_contains($first->status, 'Kadis'))
                                        Setujui Pengajuan (Terbitkan SPPD)
                                    @elseif(str_contains($first->status, 'Kabid'))
                                        Setujui & Teruskan ke Kepala Dinas
                                    @else
                                        Setujui & Teruskan ke Kepala Bidang
                                    @endif
                                </span>
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
        function filterInbox(tier) {
            const cards = document.querySelectorAll('.inbox-card');
            const btns = document.querySelectorAll('.inbox-filter-btn');

            btns.forEach(b => {
                if (b.getAttribute('data-filter') === tier) {
                    b.className = 'inbox-filter-btn px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#123B72] text-white transition-colors cursor-pointer';
                } else {
                    b.className = 'inbox-filter-btn px-2.5 py-1 rounded-md text-[11px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer';
                }
            });

            let firstVisible = null;
            cards.forEach(c => {
                const s = c.getAttribute('data-status') || '';
                if (tier === 'all' || s.includes(tier)) {
                    c.style.display = '';
                    if (!firstVisible) firstVisible = c;
                } else {
                    c.style.display = 'none';
                }
            });

            if (firstVisible) {
                selectSubmission(firstVisible.getAttribute('data-subid'));
            }
        }

        function selectSubmission(id) {
            document.querySelectorAll('.inbox-card').forEach(c => {
                c.className = 'inbox-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all';
            });

            const active = document.getElementById('item-' + id);
            if (active) {
                active.className = 'inbox-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all';

                const status = active.getAttribute('data-status') || '';
                document.getElementById('formSubId').value = active.getAttribute('data-subid');
                document.getElementById('detailNumber').innerHTML = active.getAttribute('data-number');
                document.getElementById('detailUnit').innerText = active.getAttribute('data-unit');
                document.getElementById('detailDest').innerText = active.getAttribute('data-dest');
                document.getElementById('detailDates').innerText = active.getAttribute('data-dates');
                document.getElementById('detailPurpose').innerText = active.getAttribute('data-purpose');
                document.getElementById('detailTotal').innerText = active.getAttribute('data-total');
                document.getElementById('detailRombongan').innerText = active.getAttribute('data-rombongan') || '-';

                // Status badge
                document.getElementById('detailStatusBadge').innerHTML = '<span class="inline-flex items-center font-medium rounded-full shrink-0 transition-colors px-2.5 py-0.5 text-[11.5px] bg-[#FFF1D1] text-[#8A5A00]">' + status + '</span>';

                // Attachment
                const attUrl = active.getAttribute('data-att-url');
                const attName = active.getAttribute('data-att-name');
                const attSec = document.getElementById('detailAttachmentSection');
                if (attUrl) {
                    attSec.classList.remove('hidden');
                    document.getElementById('detailAttachmentLink').href = attUrl;
                    document.getElementById('detailAttachmentName').innerText = attName || 'Unduh Dokumen';
                } else {
                    attSec.classList.add('hidden');
                }

                // Approvals Timeline
                const apprData = active.getAttribute('data-approvals');
                const apprSec = document.getElementById('detailApprovalsSection');
                const apprList = document.getElementById('detailApprovalsList');
                try {
                    const parsed = apprData ? JSON.parse(apprData) : [];
                    if (parsed && parsed.length > 0) {
                        apprSec.classList.remove('hidden');
                        apprList.innerHTML = parsed.map(a => `
                            <div class="p-2 rounded-lg bg-slate-50 border border-[#E8EEF3] text-[12px] flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-semibold text-[#1E293B] block">${a.name} ${a.pos ? '(' + a.pos + ')' : ''}</span>
                                    ${a.notes ? '<span class="text-[#64748B] italic">"' + a.notes + '"</span>' : ''}
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-[#DFF5F4] text-[#0B7A77] shrink-0">${a.decision}</span>
                            </div>
                        `).join('');
                    } else {
                        apprSec.classList.add('hidden');
                    }
                } catch(e) {
                    apprSec.classList.add('hidden');
                }

                // Approve button text
                let approveText = 'Setujui & Teruskan ke Kepala Bidang';
                if (status.includes('Kadis')) {
                    approveText = 'Setujui Pengajuan (Terbitkan SPPD)';
                } else if (status.includes('Kabid')) {
                    approveText = 'Setujui & Teruskan ke Kepala Dinas';
                }
                document.getElementById('btnApproveText').innerText = approveText;
            }
        }
    </script>
</x-layouts.app>
