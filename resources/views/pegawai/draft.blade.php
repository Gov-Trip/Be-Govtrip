<x-layouts.app title="Draft Pengajuan - Gov.Trip" role="Pegawai" headerTitle="Draft Pengajuan">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Draft Pengajuan Perjalanan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Pengajuan yang disimpan dan siap dikirimkan ke Atasan Langsung</p>
            </div>

            <x-button variant="primary" size="sm" href="/pegawai/pengajuan">
                + Tambah Pengajuan
            </x-button>
        </div>

        @if(session('success'))
            <div class="mb-5 p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Rencana Tanggal</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Estimasi Anggaran</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Rombongan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @forelse($drafts ?? [] as $draft)
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3">
                                <span class="font-semibold text-[#123B72] block">{{ $draft->destinationCity?->city_name ?? 'Kota Tujuan' }}</span>
                                <span class="text-[11.5px] text-[#64748B] truncate max-w-[200px] block">{{ $draft->purpose }}</span>
                            </td>
                            <td class="py-3 px-3 text-[#64748B]">
                                {{ \Carbon\Carbon::parse($draft->start_date)->translatedFormat('d M') }} &ndash; {{ \Carbon\Carbon::parse($draft->end_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3 px-3 font-semibold text-[#0066B3]">
                                Rp {{ number_format($draft->estimated_total_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-[#64748B]">
                                <span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#E3F0FA] text-[#0066B3] font-medium">
                                    {{ $draft->assignees->count() }} Orang
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('pegawai.pengajuan.edit', $draft->id) }}" class="text-[#0066B3] hover:underline font-medium text-[12.5px]">
                                        Edit
                                    </a>
                                    <form action="{{ route('pegawai.pengajuan.submit', $draft->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 text-[12px] font-semibold text-white bg-[#0066B3] hover:bg-[#005596] rounded-md transition-colors cursor-pointer">
                                            Kirim Sekarang
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-[#64748B]">
                                Tidak ada draft pengajuan. Klik "+ Tambah Pengajuan" untuk membuat baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
