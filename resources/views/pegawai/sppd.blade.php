<x-layouts.app title="SPPD Digital - Gov.Trip" role="Pegawai" headerTitle="SPPD Digital">
    <div class="flex flex-col gap-6">
        @php
            $activeSppd = $sppdList->first();
        @endphp

        @if($activeSppd)
            <!-- Active SPPD Showcase Card -->
            <x-card padding="lg" class="border-l-4 border-l-[#0066B3]">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E8EEF3] gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#E3F0FA] text-[#0066B3] flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[#123B72] text-[17px] font-bold">SPPD Resmi: {{ $activeSppd->sppd_number }}</h3>
                            <span class="text-[#64748B] text-[12.5px]">
                                Tujuan: {{ $activeSppd->destinationCity?->city_name }} &middot;
                                {{ \Carbon\Carbon::parse($activeSppd->start_date)->translatedFormat('d M') }}–{{ \Carbon\Carbon::parse($activeSppd->end_date)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge status="Terbit">Terbit Resmi</x-badge>
                        <a href="{{ route('travel-requests.download-sppd', $activeSppd->id) }}" target="_blank"
                           class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[10px] transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Cetak / Unduh PDF</span>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 pt-5 text-[13px]">
                    <div>
                        <span class="text-[#64748B] block text-[12px]">Pelaksana Dinas</span>
                        <span class="font-semibold text-[#1E293B] block mt-0.5">{{ $activeSppd->requester?->name }}</span>
                        <span class="text-[12px] text-[#64748B]">NIP: {{ $activeSppd->requester?->nip ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[#64748B] block text-[12px]">Unit Kerja / SKPD</span>
                        <span class="font-semibold text-[#1E293B] block mt-0.5">{{ $activeSppd->requester?->department?->name ?? 'Dinas' }}</span>
                        <span class="text-[12px] text-[#64748B]">Instansi Pemerintah Daerah</span>
                    </div>
                    <div>
                        <span class="text-[#64748B] block text-[12px]">Status Penugasan</span>
                        <span class="font-semibold text-[#0066B3] block mt-0.5">{{ $activeSppd->status }}</span>
                        <span class="text-[12px] text-[#64748B]">Nomor SPPD Valid</span>
                    </div>
                    <div class="sm:col-span-2 md:col-span-3 pt-2">
                        <span class="text-[#64748B] block text-[12px]">Keperluan / Dasar Pelaksanaan</span>
                        <p class="font-medium text-[#1E293B] mt-0.5 leading-relaxed">
                            {{ $activeSppd->purpose }}
                        </p>
                    </div>
                </div>
            </x-card>
        @endif

        <!-- Riwayat SPPD Card -->
        <x-card>
            <div class="pb-4 border-b border-[#E8EEF3] mb-4">
                <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Arsip SPPD Digital</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Daftar Surat Perintah Perjalanan Dinas yang telah diterbitkan</p>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="border-b border-[#E8EEF3]">
                        <tr>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor SPPD</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Periode Tugas</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status Dokumen</th>
                            <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                        @forelse($sppdList as $sppd)
                            <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                                <td class="py-3 px-3 font-semibold text-[#123B72]">{{ $sppd->sppd_number }}</td>
                                <td class="py-3 px-3 text-[#1E293B]">{{ $sppd->destinationCity?->city_name }}</td>
                                <td class="py-3 px-3 text-[#64748B]">
                                    {{ \Carbon\Carbon::parse($sppd->start_date)->translatedFormat('d M') }}–{{ \Carbon\Carbon::parse($sppd->end_date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3 px-3"><x-badge :status="$sppd->status" /></td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('travel-requests.download-sppd', $sppd->id) }}" target="_blank"
                                       class="px-3 py-1.5 text-[12px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-md transition-colors inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        <span>Unduh PDF</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-[#64748B]">
                                    Belum ada arsip SPPD digital. SPPD diterbitkan otomatis setelah pengajuan disetujui.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.app>
