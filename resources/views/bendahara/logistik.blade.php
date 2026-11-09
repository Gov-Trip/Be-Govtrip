<x-layouts.app title="ACC Logistik & Pencairan - Gov.Trip" role="Bendahara" headerTitle="ACC Logistik & Pencairan Uang Muka">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Daftar Permintaan (col-span-5) -->
        <div class="lg:col-span-5 flex flex-col gap-3">
            <span class="text-[13px] text-[#64748B] font-medium px-1">Daftar Logistik & Pencairan Uang Muka ({{ count($logistics ?? []) }})</span>

            @forelse($logistics ?? [] as $index => $item)
                @php
                    $isFirst = $index === 0;
                    $tripNumber = 'PD-2026/' . strtoupper(substr($item->id, 0, 6));
                    $dateRange = \Carbon\Carbon::parse($item->start_date)->translatedFormat('d M') . '–' . \Carbon\Carbon::parse($item->end_date)->translatedFormat('d M Y');
                    $advance = (float)($item->advance_payment > 0 ? $item->advance_payment : $item->estimated_total_cost);
                @endphp
                <div class="p-4 rounded-[12px] border {{ $isFirst ? 'bg-[#E3F0FA] border-[#0066B3] shadow-xs' : 'bg-white border-[#E8EEF3]' }} transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-[14px] text-[#1E293B]">{{ $item->requester?->name }} &middot; {{ $item->destinationCity?->city_name }}</span>
                        <x-badge :status="$item->status">{{ $item->status }}</x-badge>
                    </div>
                    <span class="text-[12px] text-[#64748B] block">{{ $tripNumber }} &middot; {{ $dateRange }}</span>
                    <div class="flex justify-between items-center mt-2 text-[12px]">
                        <span class="text-[#64748B]">Transport: {{ $item->logistics?->transport_type ?? 'Transportasi Resmi' }}</span>
                        <span class="font-semibold text-[#0066B3]">Rp {{ number_format($advance, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white rounded-[12px] border border-[#E8EEF3] text-[#64748B] text-[13px]">
                    Belum ada data perjalanan dinas yang siap pencairan uang muka.
                </div>
            @endforelse
        </div>

        <!-- Right: Detail ACC (col-span-7) -->
        <div class="lg:col-span-7">
            @if(!empty($logistics) && count($logistics) > 0)
                @php
                    $active = $logistics->first();
                    $activeAdvance = (float)($active->advance_payment > 0 ? $active->advance_payment : $active->estimated_total_cost);
                @endphp
                <x-card padding="lg">
                    <div class="pb-4 border-b border-[#E8EEF3] mb-5">
                        <h3 class="text-[17px] font-bold text-[#123B72]">
                            PD-2026/{{ strtoupper(substr($active->id, 0, 6)) }} &middot; {{ $active->requester?->name }}
                        </h3>
                        <p class="text-[12.5px] text-[#64748B] mt-0.5">Penugasan Dinas ke {{ $active->destinationCity?->city_name }} ({{ $active->destinationCity?->province_name }})</p>
                    </div>

                    <div class="flex flex-col gap-3 text-[13px] mb-5">
                        <div class="flex justify-between py-2 border-b border-[#E8EEF3]">
                            <span class="text-[#64748B]">Nomor SPPD Digital</span>
                            <span class="font-semibold text-[#123B72]">{{ $active->sppd_number ?? 'Dalam Proses' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-[#E8EEF3]">
                            <span class="text-[#64748B]">Transportasi & Tiket</span>
                            <span class="font-semibold text-[#1E293B]">{{ $active->logistics?->transport_type ?? 'Tiket Transportasi Resmi' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-[#E8EEF3]">
                            <span class="text-[#64748B]">Akomodasi Hotel</span>
                            <span class="font-semibold text-[#1E293B]">{{ $active->logistics?->hotel_name ?? 'Hotel Sesuai Standar SBM' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-[#E8EEF3]">
                            <span class="text-[#64748B]">Nominal Uang Muka Pencairan</span>
                            <span class="font-bold text-[#0066B3] text-[16px]">Rp {{ number_format($activeAdvance, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-[10px] bg-[#DFF5F4] text-[#0B7A77] text-[12.5px] mb-5 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        <span>Pagu anggaran SBM telah dikunci dan dana kas instansi siap untuk dicairkan.</span>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-[#E8EEF3]">
                        @if(!empty($active->sppd_number))
                            <a href="{{ route('travel-requests.download-sppd', $active->id) }}" target="_blank"
                               class="px-4 py-2 text-[12.5px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-[10px] transition-all cursor-pointer">
                                Cetak SPPD Digital
                            </a>
                        @endif
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-layouts.app>
