<x-layouts.app title="Role & Hak Akses - Gov.Trip" role="Super Admin" headerTitle="Role & Hak Akses (RBAC)">
    <x-card>
        <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Matriks Otorisasi Hak Akses Sistem</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Konfigurasi izin operasional untuk setiap tingkatan jabatan</p>
            </div>
            <button type="button" onclick="alert('Matriks hak akses RBAC berhasil disimpan!');"
                    class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[10px] transition-colors cursor-pointer">
                Simpan Perubahan
            </button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse text-[13px]">
                <thead class="border-b border-[#E8EEF3] bg-slate-50">
                    <tr>
                        <th class="py-3 px-4 text-[#123B72] font-semibold">Fitur / Hak Akses</th>
                        <th class="py-3 px-3 text-center text-[#64748B] font-medium">Pegawai</th>
                        <th class="py-3 px-3 text-center text-[#64748B] font-medium">Atasan</th>
                        <th class="py-3 px-3 text-center text-[#64748B] font-medium">Kabid / Kadis</th>
                        <th class="py-3 px-3 text-center text-[#64748B] font-medium">Bendahara</th>
                        <th class="py-3 px-3 text-center text-[#64748B] font-medium">Admin Travel</th>
                        <th class="py-3 px-3 text-center text-[#123B72] font-semibold">Super Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3]">
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-4 font-medium text-[#1E293B]">Ajukan perjalanan dinas</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-4 font-medium text-[#1E293B]">Persetujuan dinas bawahan</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-4 font-medium text-[#1E293B]">Penerbitan nomor & cetak SPPD</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-4 font-medium text-[#1E293B]">Input tiket & akomodasi travel</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-4 font-medium text-[#1E293B]">Verifikasi kuitansi riil LPJ</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-4 font-medium text-[#1E293B]">Kelola master data & pengguna</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#64748B]">&mdash;</td>
                        <td class="py-3 px-3 text-center text-[#0B7A77] font-bold">&check;</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
