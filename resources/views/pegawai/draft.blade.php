<x-layouts.app title="Draft Pengajuan - Gov.Trip" role="Pegawai" headerTitle="Draft Pengajuan">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Draft Pengajuan Perjalanan</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Pengajuan yang disimpan namun belum dikirim ke atasan</p>
            </div>

            <x-button variant="primary" size="sm" href="/pegawai/pengajuan">
                + Tambah Pengajuan
            </x-button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kota Tujuan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Rencana Tanggal</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Terakhir Diubah</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kelengkapan</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">Medan</td>
                        <td class="py-3 px-3 text-[#64748B]">3–5 Nov 2026</td>
                        <td class="py-3 px-3 text-[#64748B]">Kemarin</td>
                        <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#FFF1D1] text-[#8A5A00] font-medium">70% terisi</span></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/pegawai/pengajuan" class="text-[#0066B3] hover:underline font-medium text-[12.5px] mr-3">Lanjutkan</a>
                            <button type="button" onclick="this.closest('tr').remove()" class="text-[#B02A2A] hover:underline font-medium text-[12.5px]">Hapus</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">Denpasar</td>
                        <td class="py-3 px-3 text-[#64748B]">Belum diisi</td>
                        <td class="py-3 px-3 text-[#64748B]">5 hari lalu</td>
                        <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-slate-100 text-[#64748B] font-medium">30% terisi</span></td>
                        <td class="py-3 px-3 text-right">
                            <a href="/pegawai/pengajuan" class="text-[#0066B3] hover:underline font-medium text-[12.5px] mr-3">Lanjutkan</a>
                            <button type="button" onclick="this.closest('tr').remove()" class="text-[#B02A2A] hover:underline font-medium text-[12.5px]">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
