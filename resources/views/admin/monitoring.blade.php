<x-layouts.app title="Monitoring Perjalanan - Gov.Trip" role="Admin" headerTitle="Monitoring Perjalanan Real-Time">
    <div class="flex flex-col gap-6">
        @php
            $activeList = $allTrips->filter(fn($t) => in_array($t->status, ['Siap Berangkat', 'Disetujui', 'Menunggu Verifikasi LPJ']))->take(3);
            if ($activeList->isEmpty()) {
                $activeList = $allTrips->take(3);
            }
        @endphp

        <!-- Live Active Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($activeList as $trip)
                <x-card class="border-t-4 border-t-[#0066B3]">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#E8EEF3]">
                        <span class="font-bold text-[14px] text-[#123B72] truncate max-w-[180px]">
                            {{ $trip->requester?->name ?? 'Pegawai' }}
                        </span>
                        <x-badge :status="$trip->status">{{ $trip->status }}</x-badge>
                    </div>
                    <div class="flex flex-col gap-1 text-[12.5px] text-[#64748B]">
                        <span>Lokasi: <strong class="text-[#1E293B]">{{ $trip->destinationCity?->city_name ?? '-' }}</strong></span>
                        <span>Penginapan: <strong class="text-[#1E293B]">{{ $trip->logistics?->hotel_name ?? 'Hotel Standar SBM' }}</strong></span>
                        <span>Transportasi: <strong class="text-[#0B7A77]">{{ $trip->logistics?->transport_type ?? 'Tiket Resmi' }}</strong></span>
                        <span>Kode / SPPD: <strong class="text-[#1E293B]">{{ $trip->sppd_number ?? $trip->logistics?->booking_code_or_ticket ?? '-' }}</strong></span>
                    </div>
                </x-card>
            @empty
                <div class="col-span-3 py-8 text-center text-[#64748B] text-[13px] bg-white rounded-[12px] border border-[#E8EEF3]">
                    Belum ada penugasan perjalanan dinas aktif.
                </div>
            @endforelse
        </div>

        <!-- Jadwal Keberangkatan Mendatang -->
        <x-card>
            <div class="pb-4 border-b border-[#E8EEF3] mb-4">
                <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">Jadwal Keberangkatan & Pemantauan Perjalanan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Pegawai dengan SPPD terbit yang akan segera berangkat atau sedang bertugas</p>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="border-b border-[#E8EEF3]">
                        <tr>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal Berangkat</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Transportasi</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status Tiket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                        @forelse($allTrips as $trip)
                            <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                                <td class="py-3 px-3">
                                    <span class="font-semibold text-[#1E293B] block">{{ $trip->requester?->name ?? 'Pegawai' }}</span>
                                    <span class="text-[11.5px] text-[#64748B]">{{ $trip->requester?->department?->name ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3 text-[#123B72] font-medium">{{ $trip->destinationCity?->city_name ?? '-' }}</td>
                                <td class="py-3 px-3 text-[#64748B]">
                                    {{ \Carbon\Carbon::parse($trip->start_date)->translatedFormat('d M Y') }}
                                    @if($trip->logistics?->departure_datetime)
                                        &middot; {{ \Carbon\Carbon::parse($trip->logistics->departure_datetime)->format('H.i') }} WIB
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-[#64748B]">
                                    {{ $trip->logistics?->transport_type ?? 'Menunggu Pengadaan' }}
                                    @if($trip->logistics?->booking_code_or_ticket)
                                        <span class="text-xs text-slate-400">({{ $trip->logistics->booking_code_or_ticket }})</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <x-badge :status="$trip->status">{{ $trip->status }}</x-badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-[#64748B] text-[13px]">
                                    Belum ada data perjalanan dinas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($allTrips, 'hasPages') && $allTrips->hasPages())
                <div class="mt-4 pt-3 border-t border-[#E8EEF3]">
                    {{ $allTrips->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.app>
