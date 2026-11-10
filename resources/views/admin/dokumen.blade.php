<x-layouts.app title="Dokumen Perjalanan - Gov.Trip" role="Admin" headerTitle="Dokumen Perjalanan">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Kelengkapan Dokumen Perjalanan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Pemantauan surat tugas, tiket, dan nota dinas antar instansi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($documents as $doc)
                @php
                    $hasLogistics = !empty($doc->logistics);
                    $tripNum = $doc->sppd_number ?? ('PD-'.strtoupper(substr($doc->id, 0, 8)));
                @endphp
                <div class="p-4 rounded-[12px] border border-[#E8EEF3] bg-white flex flex-col justify-between hover:border-[#0066B3] transition-all">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-[14px] text-[#123B72] truncate max-w-[220px]">
                                {{ $tripNum }} &middot; {{ $doc->destinationCity?->city_name ?? 'Kota Tujuan' }}
                            </span>
                            <x-badge :status="$hasLogistics ? 'Disetujui' : 'Menunggu'">
                                {{ $hasLogistics ? 'Lengkap' : 'Menunggu Tiket' }}
                            </x-badge>
                        </div>
                        <span class="text-[12px] text-[#64748B] block">
                            Pegawai: <strong>{{ $doc->requester?->name ?? 'Pegawai' }}</strong> ({{ $doc->requester?->department?->name ?? 'Instansi' }})
                        </span>
                        <div class="mt-3 divide-y divide-[#E8EEF3] text-[12.5px]">
                            <div class="py-1.5 flex justify-between">
                                <span class="text-[#1E293B]">SPPD Digital Sah (TTE)</span>
                                <span class="text-[#0B7A77] font-medium">&check; {{ $doc->sppd_number ?? 'Diterbitkan' }}</span>
                            </div>
                            <div class="py-1.5 flex justify-between">
                                <span class="text-[#1E293B]">Tiket Transportasi</span>
                                @if($hasLogistics)
                                    <span class="text-[#0B7A77] font-medium">&check; {{ $doc->logistics->transport_type }} ({{ $doc->logistics->booking_code_or_ticket }})</span>
                                @else
                                    <span class="text-[#B02A2A] font-medium">&cross; Belum diinput tiket</span>
                                @endif
                            </div>
                            <div class="py-1.5 flex justify-between">
                                <span class="text-[#1E293B]">Akomodasi Hotel</span>
                                @if($hasLogistics)
                                    <span class="text-[#0B7A77] font-medium">&check; {{ $doc->logistics->hotel_name }}</span>
                                @else
                                    <span class="text-[#8A5A00] font-medium">Menunggu Akomodasi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#E8EEF3] flex justify-between items-center">
                        <span class="text-[11.5px] text-[#64748B]">
                            {{ \Carbon\Carbon::parse($doc->start_date)->translatedFormat('d M') }}–{{ \Carbon\Carbon::parse($doc->end_date)->translatedFormat('d M Y') }}
                        </span>
                        <a href="{{ route('travel-requests.download-sppd', $doc->id) }}" target="_blank"
                           class="text-[12px] font-semibold text-[#0066B3] hover:underline flex items-center gap-1">
                            Unduh SPPD (PDF) &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 py-12 text-center text-[#64748B] text-[13px] bg-slate-50/50 rounded-[12px]">
                    Belum ada dokumen perjalanan dinas yang terbit.
                </div>
            @endforelse
        </div>

        @if(method_exists($documents, 'hasPages') && $documents->hasPages())
            <div class="mt-4 pt-3 border-t border-[#E8EEF3]">
                {{ $documents->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
