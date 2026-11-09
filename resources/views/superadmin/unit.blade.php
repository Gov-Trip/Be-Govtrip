<x-layouts.app title="Unit Kerja / SKPD - Gov.Trip" role="Super Admin" headerTitle="Unit Kerja / SKPD">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Master Data Unit Kerja / SKPD</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Struktur organisasi dan departemen penanggung jawab anggaran</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse text-[13px]">
                <thead class="border-b border-[#E8EEF3] bg-slate-50">
                    <tr>
                        <th class="py-2.5 px-3 text-[#123B72] font-semibold">Kode SKPD</th>
                        <th class="py-2.5 px-3 text-[#123B72] font-semibold">Nama Unit Kerja / Divisi</th>
                        <th class="py-2.5 px-3 text-[#64748B] font-medium text-center">Jumlah Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] font-medium text-right">Dibuat Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3]">
                    @forelse($units ?? [] as $unit)
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 font-mono font-bold text-[#123B72]">{{ $unit->code }}</td>
                            <td class="py-3 px-3 font-semibold text-[#1E293B]">{{ $unit->name }}</td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#E3F0FA] text-[#0066B3] font-medium">
                                    {{ $unit->employees_count ?? $unit->employees()->count() }} Pegawai
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right text-[#64748B] text-[12px]">
                                {{ \Carbon\Carbon::parse($unit->created_at)->translatedFormat('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-[#64748B]">Belum ada unit kerja yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
