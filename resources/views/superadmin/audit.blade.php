<x-layouts.app title="Audit Log - Gov.Trip" role="Super Admin" headerTitle="Audit Log & Rekam Jejak Sistem">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Rekam Jejak Operasional Sistem</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Catatan seluruh aksi login, perubahan data, approval, dan transaksi anggaran</p>
            </div>
            <span class="text-[12px] font-semibold text-[#0066B3] bg-[#E3F0FA] px-3 py-1 rounded-full">
                Audit Trail Aktif
            </span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse text-[13px]">
                <thead class="border-b border-[#E8EEF3] bg-slate-50">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Waktu</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Pengguna</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Modul</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Aksi</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Keterangan / Jejak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3]">
                    @forelse($logs ?? [] as $log)
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 text-[#64748B] text-[12px] whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="py-3 px-3 font-semibold text-[#1E293B]">
                                {{ $log->user?->employee?->name ?? ($log->user?->email ?? 'Sistem') }}
                            </td>
                            <td class="py-3 px-3 text-[#123B72] font-medium">
                                {{ $log->module ?? 'SISTEM' }}
                            </td>
                            <td class="py-3 px-3 font-mono text-[11.5px] font-bold text-[#0066B3]">
                                {{ $log->action }}
                            </td>
                            <td class="py-3 px-3 text-[#1E293B] text-[12.5px] leading-relaxed">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-[#64748B]">
                                Belum ada catatan rekam jejak audit sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($logs) && method_exists($logs, 'links'))
            <div class="mt-4 pt-3 border-t border-[#E8EEF3]">
                {{ $logs->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
