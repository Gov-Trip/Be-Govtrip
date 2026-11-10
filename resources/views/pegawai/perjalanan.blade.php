<x-layouts.app title="Perjalanan Saya - Gov.Trip" role="Pegawai" headerTitle="Perjalanan Saya">
    <div class="flex flex-col gap-6">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-[#FDE8E8] text-[#9B1C1C] border border-[#f8b4b4] text-[13px] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Banner Perlu Perhatian / Catatan Revisi -->
        @php
            $needRevision = collect($trips ?? [])->first(fn($t) => in_array($t['status'], ['Direvisi', 'Revisi', 'Revisi LPJ']));
        @endphp
        @if($needRevision)
            <div class="p-4 rounded-[14px] bg-[#FFF1D1] border border-[#ffe099] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-[13px]">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#8A5A00]/15 text-[#8A5A00] flex items-center justify-center shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div>
                        <span class="font-bold text-[#8A5A00] block text-[13.5px]">Perlu Perbaikan / Revisi: {{ $needRevision['number'] }} &middot; {{ $needRevision['destination'] }}</span>
                        <p class="text-[#64748B] mt-0.5 leading-relaxed">{{ $needRevision['rejection_reason'] ?? 'Terdapat catatan perbaikan dari verifikator.' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    @if($needRevision['status'] === 'Revisi LPJ')
                        <a href="{{ route('pegawai.lpj') }}" class="px-4 py-2 text-[12.5px] font-semibold bg-[#8A5A00] hover:bg-[#724a00] text-white rounded-[10px] transition-colors">
                            Perbaiki LPJ
                        </a>
                    @else
                        <a href="{{ route('pegawai.pengajuan.edit', $needRevision['id']) }}" class="px-4 py-2 text-[12.5px] font-semibold bg-[#8A5A00] hover:bg-[#724a00] text-white rounded-[10px] transition-colors">
                            Perbaiki & Ajukan Ulang
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
                <div>
                    <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Daftar Pengajuan Perjalanan Dinas</h3>
                    <p class="text-[#64748B] text-[12.5px] mt-0.5">Pantau status persetujuan, logistik tiket, hingga verifikasi LPJ</p>
                </div>

                <div class="flex items-center gap-2">
                    <x-button variant="primary" size="sm" href="/pegawai/pengajuan">
                        + Buat Pengajuan
                    </x-button>
                </div>
            </div>

            <!-- Filter Pills -->
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <button type="button" onclick="filterTrips('all')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#123B72] text-white transition-colors cursor-pointer" data-filter="all">Semua</button>
                <button type="button" onclick="filterTrips('Draft')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Draft">Draft</button>
                <button type="button" onclick="filterTrips('Menunggu')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Menunggu">Menunggu</button>
                <button type="button" onclick="filterTrips('Disetujui')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Disetujui">Disetujui</button>
                <button type="button" onclick="filterTrips('Siap Berangkat')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Siap Berangkat">Siap Berangkat</button>
                <button type="button" onclick="filterTrips('Selesai')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Selesai">Selesai</button>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse" id="tripsTable">
                    <thead class="border-b border-[#E8EEF3]">
                        <tr>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor / SPPD</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Periode</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Rombongan</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                        @forelse($trips ?? [] as $item)
                            <tr class="hover:bg-[#F1F7FB]/50 transition-colors trip-row" data-status="{{ $item['status'] }}">
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-[#123B72]">{{ $item['number'] }}</div>
                                    @if(!empty($item['sppd']))
                                        <div class="text-[11.5px] font-medium text-[#0066B3] flex items-center gap-1 mt-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            <span>{{ $item['sppd'] }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-medium text-[#1E293B] block">{{ $item['destination'] }}</span>
                                    <span class="text-[11.5px] text-[#64748B] truncate max-w-[200px] block">{{ Str::limit($item['purpose'], 35) }}</span>
                                </td>
                                <td class="py-3 px-3 text-[#64748B] text-[12.5px]">{{ $item['dates'] }}</td>
                                <td class="py-3 px-3 text-[#64748B]">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[#1E293B] text-[11.5px] font-medium">
                                        {{ $item['assignees_count'] ?? 1 }} Orang
                                    </span>
                                </td>
                                <td class="py-3 px-3"><x-badge :status="$item['status']" /></td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(in_array($item['status'], ['Draft', 'draft']))
                                            <form action="{{ route('pegawai.pengajuan.submit', $item['id']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 text-[12px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-md transition-colors cursor-pointer">
                                                    Kirim
                                                </button>
                                            </form>
                                        @elseif(in_array($item['status'], ['Direvisi', 'Revisi']))
                                            <a href="{{ route('pegawai.pengajuan.edit', $item['id']) }}" class="px-2.5 py-1 text-[12px] font-semibold text-[#8A5A00] bg-[#FFF1D1] hover:bg-[#fee7b3] rounded-md transition-colors">
                                                Perbaiki
                                            </a>
                                        @elseif($item['status'] === 'Siap Berangkat')
                                            <a href="{{ route('pegawai.lpj') }}" class="px-2.5 py-1 text-[12px] font-semibold text-[#0B7A77] bg-[#DFF5F4] hover:bg-[#cbf0ee] rounded-md transition-colors">
                                                Isi LPJ
                                            </a>
                                        @elseif($item['status'] === 'Revisi LPJ')
                                            <a href="{{ route('pegawai.lpj') }}" class="px-2.5 py-1 text-[12px] font-semibold text-[#B02A2A] bg-[#FDE8E8] hover:bg-[#fcd0d0] rounded-md transition-colors">
                                                Revisi LPJ
                                            </a>
                                        @endif

                                        @if(!empty($item['sppd']))
                                            <a href="{{ route('travel-requests.download-sppd', $item['id']) }}" target="_blank" title="Unduh SPPD Digital (PDF)"
                                               class="p-1.5 text-[#0066B3] hover:bg-[#E3F0FA] rounded-md transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                            </a>
                                        @endif

                                        @if($item['status'] === 'Selesai')
                                            <a href="{{ route('travel-requests.download-lpj', $item['id']) }}" target="_blank" title="Unduh LPJ Final & Audit Trail (PDF)"
                                               class="p-1.5 text-[#0B7A77] hover:bg-[#DFF5F4] rounded-md transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                            </a>
                                        @endif

                                        <button type="button" onclick="showTripDetail('{{ $item['number'] }}', '{{ $item['destination'] }}', '{{ $item['dates'] }}', '{{ addslashes($item['purpose']) }}', '{{ $item['status'] }}', '{{ $item['sppd'] ?? '' }}', '{{ $item['id'] }}', '{{ addslashes($item['rejection_reason'] ?? '') }}', '{{ $item['attachment_url'] ?? '' }}', '{{ $item['attachment_name'] ?? '' }}')"
                                                class="px-2.5 py-1 text-[12px] font-medium text-[#0066B3] bg-[#E3F0FA] hover:bg-[#cde4f7] rounded-md transition-colors cursor-pointer">
                                            Detail
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-[#64748B]">
                                    Belum ada data perjalanan dinas yang diajukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <!-- Modal Detail Perjalanan -->
    <div id="tripModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-[16px] max-w-[540px] w-full p-6 shadow-2xl flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8EEF3]">
                <div>
                    <h3 id="modalTripCode" class="text-[16px] font-bold text-[#123B72]">Detail Perjalanan</h3>
                    <p id="modalTripDest" class="text-[12.5px] text-[#64748B] mt-0.5"></p>
                </div>
                <button type="button" onclick="closeTripModal()" class="text-[#64748B] hover:text-[#1E293B] p-1 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="flex flex-col gap-3 text-[13px]">
                <div id="modalReasonBox" class="p-3 rounded-lg bg-[#FFF1D1] border border-[#ffe099] text-[#8A5A00] hidden">
                    <span class="font-bold block text-[12px]">Catatan Koreksi / Penolakan:</span>
                    <p id="modalTripReason" class="mt-0.5 text-[12.5px]"></p>
                </div>
                <div>
                    <span class="text-[#64748B] block text-[11.5px]">Keperluan / Maksud Tugas</span>
                    <p id="modalTripPurpose" class="font-medium text-[#1E293B] mt-0.5 leading-relaxed"></p>
                </div>
                <div class="flex justify-between py-2 border-y border-[#E8EEF3]">
                    <span class="text-[#64748B]">Periode Pelaksanaan</span>
                    <span id="modalTripDates" class="font-medium text-[#1E293B]"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#64748B]">Status Saat Ini</span>
                    <span id="modalTripStatusBadge"></span>
                </div>

                <div id="modalAttSection" class="p-2.5 rounded-lg bg-slate-50 border border-[#E8EEF3] flex items-center justify-between hidden">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="text-[#0066B3]"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span id="modalAttName" class="text-[12px] font-medium text-[#1E293B]"></span>
                    </div>
                    <a id="modalAttLink" href="#" target="_blank" class="text-[11.5px] font-semibold text-[#0066B3] hover:underline">Lihat Dokumen</a>
                </div>
            </div>

            <div id="modalSppdSection" class="pt-2 flex flex-wrap justify-end gap-2">
                <a id="modalEditTrip" href="#"
                   class="px-4 py-2 text-[12.5px] font-semibold bg-[#8A5A00] hover:bg-[#724a00] text-white rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    <span>Perbaiki & Ajukan Ulang</span>
                </a>
                <a id="modalDownloadSppd" href="#" target="_blank"
                   class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <span>Unduh SPPD Digital (PDF)</span>
                </a>
                <a id="modalDownloadLpj" href="#" target="_blank"
                   class="px-4 py-2 text-[12.5px] font-semibold bg-[#0B7A77] hover:bg-[#096360] text-white rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>Unduh LPJ Final & Audit</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        function filterTrips(status) {
            const rows = document.querySelectorAll('.trip-row');
            const btns = document.querySelectorAll('.filter-btn');

            btns.forEach(b => {
                if (b.getAttribute('data-filter') === status) {
                    b.className = 'filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#123B72] text-white transition-colors cursor-pointer';
                } else {
                    b.className = 'filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer';
                }
            });

            rows.forEach(r => {
                const s = r.getAttribute('data-status') || '';
                if (status === 'all' || s.toLowerCase().includes(status.toLowerCase())) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        function showTripDetail(code, dest, dates, purpose, status, sppd, id, reason, attUrl, attName) {
            document.getElementById('modalTripCode').innerText = code;
            document.getElementById('modalTripDest').innerText = dest;
            document.getElementById('modalTripDates').innerText = dates;
            document.getElementById('modalTripPurpose').innerText = purpose;
            document.getElementById('modalTripStatusBadge').innerHTML = '<span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#E3F0FA] text-[#0066B3] font-medium">' + status + '</span>';

            const reasonBox = document.getElementById('modalReasonBox');
            const editBtn = document.getElementById('modalEditTrip');
            if (reason) {
                reasonBox.classList.remove('hidden');
                document.getElementById('modalTripReason').innerText = reason;
            } else {
                reasonBox.classList.add('hidden');
            }

            if (status === 'Direvisi' || status === 'Revisi') {
                editBtn.classList.remove('hidden');
                editBtn.href = '/pegawai/pengajuan/' + id + '/edit';
            } else {
                editBtn.classList.add('hidden');
            }

            // Attachment in modal
            const attSec = document.getElementById('modalAttSection');
            if (attUrl) {
                attSec.classList.remove('hidden');
                document.getElementById('modalAttLink').href = attUrl;
                document.getElementById('modalAttName').innerText = attName || 'Dokumen Pendukung';
            } else {
                attSec.classList.add('hidden');
            }

            const dlBtn = document.getElementById('modalDownloadSppd');
            const lpjBtn = document.getElementById('modalDownloadLpj');

            if (sppd) {
                document.getElementById('modalSppdSection').style.display = 'flex';
                dlBtn.href = '/travel-requests/' + id + '/download-sppd';
                dlBtn.classList.remove('hidden');
            } else {
                dlBtn.classList.add('hidden');
            }

            if (status === 'Selesai') {
                lpjBtn.href = '/travel-requests/' + id + '/download-lpj';
                lpjBtn.classList.remove('hidden');
            } else {
                lpjBtn.classList.add('hidden');
            }

            document.getElementById('tripModal').classList.remove('hidden');
        }
        }

        function closeTripModal() {
            document.getElementById('tripModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
