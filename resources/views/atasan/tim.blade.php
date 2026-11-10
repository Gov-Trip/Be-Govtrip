<x-layouts.app title="Tim Saya - Gov.Trip" role="Atasan" headerTitle="Anggota Tim & Status">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Status Penugasan Anggota Tim</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Monitoring keberadaan dan aktivitas perjalanan dinas bawahan langsung</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nama Anggota</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Periode Tanggal</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Moda Transport</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Keperluan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @forelse($team as $member)
                        @php
                            $latestTrip = $member->travelRequests->first();
                        @endphp
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3">
                                <span class="font-semibold text-[#1E293B] block">{{ $member->name }}</span>
                                <span class="text-[11.5px] text-[#64748B]">NIP: {{ $member->nip }} &middot; {{ $member->position }}</span>
                            </td>
                            <td class="py-3 px-3 font-medium text-[#123B72]">
                                {{ $latestTrip?->destinationCity?->city_name ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-[#64748B]">
                                @if($latestTrip)
                                    {{ \Carbon\Carbon::parse($latestTrip->start_date)->translatedFormat('d M') }}–{{ \Carbon\Carbon::parse($latestTrip->end_date)->translatedFormat('d M Y') }}
                                @else
                                    <span class="text-slate-400">Tidak ada jadwal dinas</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-[#64748B]">
                                {{ $latestTrip?->logistics?->transport_type ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-[#64748B] max-w-[200px] truncate" title="{{ $latestTrip?->purpose ?? 'Tersedia di instansi' }}">
                                {{ $latestTrip?->purpose ?? 'Standby di Kantor' }}
                            </td>
                            <td class="py-3 px-3">
                                @if($latestTrip)
                                    <x-badge :status="$latestTrip->status">{{ $latestTrip->status }}</x-badge>
                                @else
                                    <span class="text-[11.5px] font-medium text-[#64748B] bg-slate-100 px-2.5 py-1 rounded-md">Standby di Kantor</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-[#64748B] text-[13px]">
                                Belum ada anggota tim terdaftar di unit kerja ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
