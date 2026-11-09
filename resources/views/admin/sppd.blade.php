<x-layouts.app title="SPPD Digital - Gov.Trip" role="Admin" headerTitle="Manajemen SPPD Digital">
    @if(session('success'))
        <div class="mb-5 p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Penerbitan Surat Perintah Perjalanan Dinas (SPPD)</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Kelola penomoran, tiket transportasi, dan cetak format standar SPPD digital</p>
            </div>
            <a href="{{ route('admin.perjalanan') }}" class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[10px] transition-colors cursor-pointer">
                + Input Tiket & Terbitkan SPPD
            </a>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor SPPD</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai Pelaksana</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @forelse($sppdTrips as $trip)
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 font-semibold text-[#123B72]">{{ $trip->sppd_number }}</td>
                            <td class="py-3 px-3">
                                <span class="font-medium text-[#1E293B] block">{{ $trip->requester?->name }}</span>
                                <span class="text-[11.5px] text-[#64748B]">{{ $trip->requester?->department?->name }}</span>
                            </td>
                            <td class="py-3 px-3 text-[#1E293B]">{{ $trip->destinationCity?->city_name }}</td>
                            <td class="py-3 px-3 text-[#64748B]">
                                {{ \Carbon\Carbon::parse($trip->start_date)->translatedFormat('d M') }}–{{ \Carbon\Carbon::parse($trip->end_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3 px-3"><x-badge :status="$trip->status" /></td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('travel-requests.download-sppd', $trip->id) }}" target="_blank"
                                   class="px-3 py-1.5 text-[12px] font-semibold text-[#0066B3] bg-[#E3F0FA] hover:bg-[#cde4f7] rounded-md transition-colors inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    <span>Unduh PDF</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-[#64748B]">
                                Belum ada SPPD digital yang terbit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
