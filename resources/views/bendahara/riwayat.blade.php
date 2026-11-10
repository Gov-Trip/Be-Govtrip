<x-layouts.app title="Riwayat Verifikasi - Gov.Trip" role="Bendahara" headerTitle="Riwayat Verifikasi Keuangan">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Log Audit & Verifikasi Keuangan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Daftar laporan yang telah selesai diverifikasi oleh bendahara</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nomor LPJ</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Tanggal Audit</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nominal Sah</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @forelse($history as $item)
                        @php
                            $realCost = (float) ($item->expenseReport?->total_real_expense ?? $item->advance_payment ?? $item->estimated_total_cost);
                            $auditDate = $item->expenseReport?->updated_at ? $item->expenseReport->updated_at->translatedFormat('d M Y') : $item->updated_at->translatedFormat('d M Y');
                        @endphp
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 font-semibold text-[#123B72]">LPJ-{{ strtoupper(substr($item->id, 0, 6)) }}</td>
                            <td class="py-3 px-3 font-medium text-[#1E293B]">{{ $item->requester?->name ?? 'Pegawai' }}</td>
                            <td class="py-3 px-3 text-[#1E293B]">{{ $item->destinationCity?->city_name ?? '-' }}</td>
                            <td class="py-3 px-3 text-[#64748B]">{{ $auditDate }}</td>
                            <td class="py-3 px-3 font-semibold text-[#0B7A77]">Rp {{ number_format($realCost, 0, ',', '.') }}</td>
                            <td class="py-3 px-3">
                                <x-badge :status="$item->status">{{ $item->status }}</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-[#64748B] text-[13px]">
                                Belum ada riwayat verifikasi LPJ yang tersimpan.
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
