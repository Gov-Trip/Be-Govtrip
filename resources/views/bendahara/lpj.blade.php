<x-layouts.app title="Laporan Pertanggungjawaban - Gov.Trip" role="Bendahara" headerTitle="Laporan Pertanggungjawaban (LPJ)">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Seluruh Berkas LPJ Masuk</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Daftar laporan pertanggungjawaban dari seluruh perjalanan dinas</p>
            </div>
            <x-button variant="primary" size="sm" href="{{ route('bendahara.verifikasi') }}">
                Periksa Antrean &rarr;
            </x-button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor / SPPD</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai / Pelaksana</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Uang Muka</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Realisasi Riil</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-center">Status Audit</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @forelse($lpjList as $rep)
                        @php
                            $travel = $rep->travelRequest;
                            $lpjCode = 'LPJ-2026/' . strtoupper(substr($rep->travel_request_id, 0, 6));
                        @endphp
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3">
                                <span class="font-semibold text-[#123B72] block">{{ $lpjCode }}</span>
                                <span class="text-[11.5px] text-[#64748B]">{{ $travel?->sppd_number ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-3 font-medium text-[#1E293B]">
                                {{ $rep->employee?->name ?? 'Pegawai' }}
                            </td>
                            <td class="py-3 px-3 text-[#1E293B]">
                                {{ $travel?->destinationCity?->city_name ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-right text-[#64748B]">
                                Rp {{ number_format($rep->total_advance, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-[#0066B3]">
                                Rp {{ number_format($rep->total_real_expense, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $rep->status === 'VERIFIED' ? 'bg-[#DFF5F4] text-[#0B7A77]' : ($rep->status === 'REJECTED' ? 'bg-[#FDE8E8] text-[#9B1C1C]' : 'bg-[#FFF1D1] text-[#8A5A00]') }}">
                                    {{ $rep->status === 'VERIFIED' ? 'Terverifikasi' : ($rep->status === 'REJECTED' ? 'Ditolak/Revisi' : 'Menunggu Audit') }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                @if($rep->status === 'VERIFIED')
                                    <a href="{{ route('travel-requests.download-lpj', $rep->travel_request_id) }}" target="_blank"
                                       class="px-2.5 py-1 text-[12px] font-semibold text-[#0B7A77] bg-[#DFF5F4] hover:bg-[#cbebe9] rounded-md transition-colors inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        <span>PDF Final</span>
                                    </a>
                                @else
                                    <a href="{{ route('bendahara.verifikasi') }}" class="text-[#0066B3] hover:underline font-semibold text-[12.5px]">
                                        Audit Sekarang
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-[#64748B]">
                                Belum ada berkas LPJ yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
