<x-layouts.app title="Data Pegawai - Gov.Trip" role="Admin" headerTitle="Data Pegawai">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Daftar Pegawai Instansi</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Master data aparatur dan pegawai pelaksana perjalanan dinas</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="text" id="pegawaiSearch" placeholder="Cari nama atau NIP..."
                       onkeyup="GovTrip.filterTable('pegawaiSearch', 'pegawaiTable')"
                       class="px-3 py-1.5 text-[12.5px] bg-[#F1F7FB] border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                <button type="button" onclick="alert('Fitur tambah pegawai dibuka oleh Super Admin di menu Pengguna.');"
                        class="px-3.5 py-1.5 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[8px] transition-colors cursor-pointer">
                    + Tambah Pegawai
                </button>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse" id="pegawaiTable">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">NIP</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nama Lengkap</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Jabatan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Unit Kerja</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @forelse($employees ?? [] as $emp)
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                            <td class="py-3 px-3 text-[#64748B]">{{ $emp->nip }}</td>
                            <td class="py-3 px-3 font-semibold text-[#1E293B]">{{ $emp->name }}</td>
                            <td class="py-3 px-3 text-[#1E293B]">{{ $emp->position }}</td>
                            <td class="py-3 px-3 text-[#64748B]">{{ $emp->department->name ?? '-' }}</td>
                            <td class="py-3 px-3"><x-badge status="Aktif">Aktif</x-badge></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-[#64748B] text-[13px]">
                                Belum ada data pegawai yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
