<x-layouts.app title="Riwayat Persetujuan - Gov.Trip" role="Atasan" headerTitle="Riwayat Persetujuan">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Log Keputusan Persetujuan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Daftar keputusan yang telah Anda ambil terkait pengajuan perjalanan dinas bawahan</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor Pengajuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nama Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal Diputuskan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Keputusan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @forelse($history as $item)
                        @php
                            $approval = $item->approvals->first();
                            $dateDecided = $approval?->created_at ? $approval->created_at->translatedFormat('d M Y') : $item->updated_at->translatedFormat('d M Y');
                            $note = $approval?->notes ?? $item->rejection_reason ?? 'Disetujui untuk diproses tiket/hotel.';
                        @endphp
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 font-semibold text-[#123B72]">
                                {{ $item->sppd_number ?? ('PD-'.strtoupper(substr($item->id, 0, 8))) }}
                            </td>
                            <td class="py-3 px-3 font-medium text-[#1E293B]">
                                {{ $item->requester?->name ?? 'Pegawai' }}
                            </td>
                            <td class="py-3 px-3 text-[#1E293B]">
                                {{ $item->destinationCity?->city_name ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-[#64748B]">
                                {{ $dateDecided }}
                            </td>
                            <td class="py-3 px-3">
                                <x-badge :status="$item->status">{{ $item->status }}</x-badge>
                            </td>
                            <td class="py-3 px-3 text-[#64748B] max-w-[240px] truncate" title="{{ $note }}">
                                {{ $note }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-[#64748B] text-[13px]">
                                Belum ada riwayat persetujuan keputusan perjalanan dinas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($history, 'hasPages') && $history->hasPages())
            <div class="mt-4 pt-3 border-t border-[#E8EEF3]">
                {{ $history->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
