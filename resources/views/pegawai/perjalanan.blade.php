<x-layouts.app title="Perjalanan Saya - Gov.Trip" role="Pegawai" headerTitle="Perjalanan Saya">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Daftar Pengajuan Perjalanan Dinas</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Pantau status persetujuan dan riwayat perjalanan Anda</p>
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
            <button type="button" onclick="filterTrips('Berjalan')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Berjalan">Berjalan</button>
            <button type="button" onclick="filterTrips('Menunggu')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Menunggu">Menunggu</button>
            <button type="button" onclick="filterTrips('Selesai')" class="filter-btn px-3 py-1.5 rounded-lg text-[12px] font-medium bg-[#F1F7FB] text-[#64748B] hover:text-[#123B72] transition-colors cursor-pointer" data-filter="Selesai">Selesai</button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tripsTable">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor / SPPD</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Periode</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Transport</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @php
                        $list = $trips ?? [
                            [
                                'id' => 1,
                                'number' => 'PD-2026/087',
                                'sppd' => '090/087/SPPD/IX/2026',
                                'destination' => 'Yogyakarta',
                                'dates' => '21–23 Sep 2026',
                                'transport' => 'Kereta api',
                                'status' => 'Berjalan',
                                'purpose' => 'Sosialisasi regulasi perjalanan dinas bersama dinas terkait.',
                            ],
                            [
                                'id' => 2,
                                'number' => 'PD-2026/091',
                                'sppd' => null,
                                'destination' => 'Bandung',
                                'dates' => '6–8 Okt 2026',
                                'transport' => 'Kereta api',
                                'status' => 'Menunggu',
                                'purpose' => 'Koordinasi pembaruan standar operasional dinas.',
                            ],
                            [
                                'id' => 3,
                                'number' => 'PD-2026/079',
                                'sppd' => '090/079/SPPD/IX/2026',
                                'destination' => 'Makassar',
                                'dates' => '14–16 Sep 2026',
                                'transport' => 'Pesawat',
                                'status' => 'Selesai',
                                'purpose' => 'Monitoring dan evaluasi pelaksanaan program daerah.',
                            ],
                        ];
                    @endphp
                    @foreach($list as $item)
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors trip-row" data-status="{{ $item['status'] }}">
                            <td class="py-3 px-3">
                                <div class="font-semibold text-[#123B72]">{{ $item['number'] }}</div>
                                @if(!empty($item['sppd']))
                                    <div class="text-[11.5px] text-[#64748B]">{{ $item['sppd'] }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 font-medium text-[#1E293B]">{{ $item['destination'] }}</td>
                            <td class="py-3 px-3 text-[#64748B]">{{ $item['dates'] }}</td>
                            <td class="py-3 px-3 text-[#64748B]">{{ $item['transport'] }}</td>
                            <td class="py-3 px-3"><x-badge :status="$item['status']" /></td>
                            <td class="py-3 px-3 text-right">
                                <button type="button" onclick="showTripDetail('{{ $item['number'] }}', '{{ $item['destination'] }}', '{{ $item['dates'] }}', '{{ $item['purpose'] }}', '{{ $item['status'] }}', '{{ $item['sppd'] ?? '' }}', '{{ $item['id'] }}')"
                                        class="px-2.5 py-1 text-[12px] font-medium text-[#0066B3] bg-[#E3F0FA] hover:bg-[#cde4f7] rounded-md transition-colors cursor-pointer">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Modal Detail Perjalanan -->
    <div id="tripModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-[16px] max-w-[540px] w-full p-6 shadow-2xl flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8EEF3]">
                <div>
                    <h3 id="modalTripCode" class="text-[16px] font-bold text-[#123B72]">Detail Perjalanan</h3>
                    <p id="modalTripDest" class="text-[12.5px] text-[#64748B] mt-0.5"></p>
                </div>
                <button type="button" onclick="closeTripModal()" class="text-[#64748B] hover:text-[#1E293B] p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="flex flex-col gap-3 text-[13px]">
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
            </div>

            <div id="modalSppdSection" class="pt-2 flex justify-end gap-2">
                <a id="modalDownloadSppd" href="#" target="_blank"
                   class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <span>Unduh SPPD Digital</span>
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
                if (status === 'all' || r.getAttribute('data-status') === status) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        function showTripDetail(code, dest, dates, purpose, status, sppd, id) {
            document.getElementById('modalTripCode').innerText = code;
            document.getElementById('modalTripDest').innerText = dest;
            document.getElementById('modalTripDates').innerText = dates;
            document.getElementById('modalTripPurpose').innerText = purpose;
            document.getElementById('modalTripStatusBadge').innerHTML = '<span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#E3F0FA] text-[#0066B3] font-medium">' + status + '</span>';

            const dlBtn = document.getElementById('modalDownloadSppd');
            if (sppd) {
                document.getElementById('modalSppdSection').style.display = 'flex';
                dlBtn.href = '/api/travel-requests/' + id + '/download-sppd';
            } else {
                document.getElementById('modalSppdSection').style.display = 'none';
            }

            document.getElementById('tripModal').classList.remove('hidden');
        }

        function closeTripModal() {
            document.getElementById('tripModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
