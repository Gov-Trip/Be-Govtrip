<x-layouts.app title="Data Perjalanan & Logistik - Gov.Trip" role="Admin" headerTitle="Data Perjalanan & Tiket">
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
        <!-- Left: List Perjalanan (col-span-5) -->
        <div class="lg:col-span-5 flex flex-col gap-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-[13px] text-[#64748B] font-medium">Perjalanan Disetujui (Perlu Input Tiket / SPPD)</span>
                <span class="text-[12px] font-bold text-[#0066B3] bg-[#E3F0FA] px-2 py-0.5 rounded-full">{{ $approvedTrips->count() }} Antrean</span>
            </div>

            @forelse($approvedTrips as $index => $trip)
                @php
                    $isFirst = $index === 0;
                    $tripNumber = 'PD-2026/' . strtoupper(substr($trip->id, 0, 6));
                    $dateRange = \Carbon\Carbon::parse($trip->start_date)->translatedFormat('d M') . '–' . \Carbon\Carbon::parse($trip->end_date)->translatedFormat('d M Y');
                    $sbmTotal = $trip->sbm_budget['standardized_total'] ?? (float)$trip->estimated_total_cost;
                @endphp
                <div onclick="selectAdminTrip('{{ $trip->id }}')"
                     id="admin-card-{{ $trip->id }}"
                     data-tripid="{{ $trip->id }}"
                     data-title="{{ $tripNumber }} &middot; {{ $trip->requester?->name }} ({{ $trip->destinationCity?->city_name }})"
                     data-requester="{{ $trip->requester?->name }} &middot; {{ $trip->requester?->department?->name }}"
                     data-dest="{{ $trip->destinationCity?->city_name }} ({{ $trip->destinationCity?->province_name }})"
                     data-dates="{{ $dateRange }}"
                     data-purpose="{{ $trip->purpose }}"
                     data-sbm="Rp {{ number_format($sbmTotal, 0, ',', '.') }}"
                     data-rawcost="{{ (int)$sbmTotal }}"
                     data-sppd="{{ $trip->sppd_number ?? 'Otomatis digenerate' }}"
                     data-members="{{ $trip->assignees->pluck('employee.name')->join(', ') }}"
                     class="admin-trip-card p-4 rounded-[12px] border {{ $isFirst ? 'bg-[#E3F0FA] border-[#0066B3] shadow-xs' : 'bg-white border-[#E8EEF3] hover:border-[#C9D8EC]' }} cursor-pointer transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-[14px] text-[#1E293B]">{{ $trip->requester?->name }} &middot; {{ $trip->destinationCity?->city_name }}</span>
                        <x-badge status="Disetujui">Siap Logistik</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">{{ $tripNumber }} &middot; {{ $dateRange }}</span>
                    <div class="flex justify-between items-center mt-2 text-[12px]">
                        <span class="text-[#64748B]">{{ $trip->assignees->count() }} Orang Rombongan</span>
                        <span class="font-semibold text-[#0066B3]">Plafon: Rp {{ number_format($sbmTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white rounded-[12px] border border-[#E8EEF3] text-[#64748B] text-[13px]">
                    Tidak ada perjalanan dinas yang menunggu pemrosesan tiket/logistik saat ini.
                </div>
            @endforelse
        </div>

        <!-- Right: Input Tiket & Terbitkan SPPD (col-span-7) -->
        <div class="lg:col-span-7">
            @if($approvedTrips->isNotEmpty())
                @php
                    $activeTrip = $approvedTrips->first();
                    $activeSbm = $activeTrip->sbm_budget['standardized_total'] ?? (float)$activeTrip->estimated_total_cost;
                @endphp
                <x-card padding="lg">
                    <div class="pb-4 border-b border-[#E8EEF3] mb-5">
                        <h3 id="adminTripTitle" class="text-[17px] font-bold text-[#123B72]">
                            PD-2026/{{ strtoupper(substr($activeTrip->id, 0, 6)) }} &middot; {{ $activeTrip->requester?->name }} ({{ $activeTrip->destinationCity?->city_name }})
                        </h3>
                        <p id="adminTripSub" class="text-[12.5px] text-[#64748B] mt-0.5">
                            Input tiket transportasi resmi dan hotel penginapan, lalu kunci anggaran standar SBM
                        </p>
                    </div>

                    <!-- Ringkasan Perjalanan & Plafon SBM -->
                    <div class="p-4 rounded-[12px] bg-slate-50 border border-[#E8EEF3] mb-5 text-[12.5px] flex flex-col gap-2">
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Pemohon & Departemen:</span>
                            <span id="displayRequester" class="font-semibold text-[#1E293B]">{{ $activeTrip->requester?->name }} ({{ $activeTrip->requester?->department?->name }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Kota Tujuan:</span>
                            <span id="displayDest" class="font-semibold text-[#1E293B]">{{ $activeTrip->destinationCity?->city_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Anggota Rombongan:</span>
                            <span id="displayMembers" class="font-medium text-[#1E293B]">{{ $activeTrip->assignees->pluck('employee.name')->join(', ') ?: $activeTrip->requester?->name }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-[#E8EEF3]">
                            <span class="text-[#123B72] font-bold">Plafon Terstandarisasi Master SBM:</span>
                            <span id="displaySbm" class="text-[15px] font-extrabold text-[#0066B3]">Rp {{ number_format($activeSbm, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Form Logistik & Tiket -->
                    <form action="{{ route('admin.logistics.store') }}" method="POST" class="flex flex-col gap-4">
                        @csrf
                        <input type="hidden" name="trip_id" id="adminTripId" value="{{ $activeTrip->id }}" />
                        <input type="hidden" name="estimated_total_cost" id="adminCostInput" value="{{ (int)$activeSbm }}" />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Jenis Tiket / Maskapai / Armada</label>
                                <input type="text" name="transport_type" value="Garuda Indonesia (GA-512) PP" required
                                       class="px-3.5 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Kode Booking / Nomor Tiket</label>
                                <input type="text" name="booking_code" value="GA-7890214-{{ strtoupper(substr($activeTrip->id, 0, 4)) }}" required
                                       class="px-3.5 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Akomodasi Hotel Resmi</label>
                                <input type="text" name="hotel_name" value="Hotel Grand Santika {{ $activeTrip->destinationCity?->city_name }}" required
                                       class="px-3.5 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[#64748B] text-[12.5px] font-medium mb-1">Jadwal Keberangkatan</label>
                                <input type="datetime-local" name="departure_datetime" value="{{ \Carbon\Carbon::parse($activeTrip->start_date)->format('Y-m-d') }}T08:00" required
                                       class="px-3.5 py-2 text-[13px] bg-white border border-[#E8EEF3] rounded-[10px] focus:outline-none focus:border-[#0066B3]" />
                            </div>
                        </div>

                        <div class="p-3.5 rounded-[10px] bg-[#E3F0FA] text-[#0066B3] text-[12px] flex items-center justify-between">
                            <span>Status perjalanan akan otomatis menjadi <strong>SIAP BERANGKAT</strong> dan nomor SPPD digital siap diunduh.</span>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-[#E8EEF3]">
                            <!-- Tombol Tolak / Kembalikan jika kendala kuota -->
                            <button type="button" onclick="openRejectLogisticsModal()"
                                    class="px-4 py-2.5 text-[12.5px] font-semibold text-[#B02A2A] border border-[#B02A2A] hover:bg-[#FBE8E8] rounded-[10px] transition-colors cursor-pointer">
                                Kendala Kuota / Kembalikan
                            </button>

                            <!-- Tombol Input Tiket & Terbitkan SPPD -->
                            <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] shadow-sm transition-all cursor-pointer">
                                Simpan Tiket & Kunci Budget (Siap Berangkat)
                            </button>
                        </div>
                    </form>
                </x-card>
            @else
                <x-card padding="lg">
                    <div class="py-16 text-center text-[#64748B]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" class="mx-auto text-slate-300 mb-3"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <h4 class="font-bold text-[#1E293B] text-[15px]">Tidak Ada Antrean Tiket</h4>
                        <p class="text-[12.5px] mt-1">Seluruh pengajuan yang disetujui telah selesai diproses logistiknya.</p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>

    <!-- Modal Kendala Kuota / Reject Logistik -->
    <div id="rejectLogisticsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-[16px] max-w-[460px] w-full p-6 shadow-2xl flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8EEF3]">
                <h3 class="text-[16px] font-bold text-[#B02A2A]">Kembalikan ke Pegawai (Kendala Kuota / Hotel Penuh)</h3>
                <button type="button" onclick="closeRejectLogisticsModal()" class="text-[#64748B] hover:text-[#1E293B]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.logistics.reject') }}" method="POST" class="flex flex-col gap-3 text-[13px]">
                @csrf
                <input type="hidden" name="trip_id" id="modalRejectTripId" value="{{ $approvedTrips->first()?->id }}" />

                <div>
                    <label class="text-[#64748B] text-[12px] font-medium block mb-1">Alasan Pengembalian / Penolakan Logistik</label>
                    <textarea name="reason" rows="3" required placeholder="Jelaskan alasan (misal: tiket kereta habis pada tanggal tersebut, mohon geser jadwal)..."
                              class="w-full px-3 py-2 text-[13px] border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#B02A2A]">Penerbangan atau kuota tiket pada tanggal yang dipilih telah penuh. Mohon sesuaikan tanggal keberangkatan.</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-[#E8EEF3]">
                    <button type="button" onclick="closeRejectLogisticsModal()" class="px-4 py-2 text-[12.5px] rounded-[8px] border border-[#E8EEF3] text-[#64748B]">Batal</button>
                    <button type="submit" class="px-4 py-2 text-[12.5px] font-semibold bg-[#B02A2A] hover:bg-[#8e1d1d] text-white rounded-[8px]">
                        Kembalikan (Status: Direvisi)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectAdminTrip(id) {
            document.querySelectorAll('.admin-trip-card').forEach(c => {
                c.className = 'admin-trip-card p-4 rounded-[12px] border bg-white border-[#E8EEF3] hover:border-[#C9D8EC] cursor-pointer transition-all';
            });
            const active = document.getElementById('admin-card-' + id);
            if (active) {
                active.className = 'admin-trip-card p-4 rounded-[12px] border bg-[#E3F0FA] border-[#0066B3] shadow-xs cursor-pointer transition-all';

                const tripid = active.getAttribute('data-tripid');
                document.getElementById('adminTripId').value = tripid;
                document.getElementById('modalRejectTripId').value = tripid;

                document.getElementById('adminTripTitle').innerHTML = active.getAttribute('data-title');
                document.getElementById('displayRequester').innerText = active.getAttribute('data-requester');
                document.getElementById('displayDest').innerText = active.getAttribute('data-dest');
                document.getElementById('displayMembers').innerText = active.getAttribute('data-members') || '-';
                document.getElementById('displaySbm').innerText = active.getAttribute('data-sbm');
                document.getElementById('adminCostInput').value = active.getAttribute('data-rawcost');
            }
        }

        function openRejectLogisticsModal() {
            document.getElementById('rejectLogisticsModal').classList.remove('hidden');
        }

        function closeRejectLogisticsModal() {
            document.getElementById('rejectLogisticsModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
