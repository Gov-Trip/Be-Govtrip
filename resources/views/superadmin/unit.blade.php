<x-layouts.app title="Unit Kerja / SKPD - Gov.Trip" role="Super Admin" headerTitle="Unit Kerja / SKPD">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Master Data Unit Kerja</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Struktur organisasi dan bidang penanggungjawab anggaran</p>
            </div>
            <button type="button" onclick="alert('Unit kerja baru berhasil ditambahkan!');"
                    class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[10px] transition-colors cursor-pointer">
                + Tambah Unit Kerja
            </button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse text-[13px]">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kode</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nama Unit Kerja / SKPD</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Kepala Unit</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Jumlah Staf</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Alokasi Pagu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3]">
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">PROGRAM</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Perencanaan & Program</td>
                        <td class="py-3 px-3 text-[#64748B]">Bpk. Hendra Pratama, M.Si.</td>
                        <td class="py-3 px-3 text-[#64748B]">34 Pegawai</td>
                        <td class="py-3 px-3 font-semibold text-[#0066B3]">Rp 150.000.000</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">FINANCE</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Divisi Keuangan & Aset</td>
                        <td class="py-3 px-3 text-[#64748B]">Ibu Ratna Dewi, S.E.</td>
                        <td class="py-3 px-3 text-[#64748B]">12 Pegawai</td>
                        <td class="py-3 px-3 font-semibold text-[#0066B3]">Rp 80.000.000</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">ADMIN_TRAVEL</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">Logistik & Akomodasi</td>
                        <td class="py-3 px-3 text-[#64748B]">Bpk. Surya Kencana</td>
                        <td class="py-3 px-3 text-[#64748B]">8 Pegawai</td>
                        <td class="py-3 px-3 font-semibold text-[#0066B3]">Rp 120.000.000</td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50">
                        <td class="py-3 px-3 font-semibold text-[#123B72]">SUPER_ADMIN</td>
                        <td class="py-3 px-3 font-medium text-[#1E293B]">IT & System Admin</td>
                        <td class="py-3 px-3 text-[#64748B]">Bpk. Bambang Wijaya</td>
                        <td class="py-3 px-3 text-[#64748B]">5 Pegawai</td>
                        <td class="py-3 px-3 font-semibold text-[#0066B3]">Rp 50.000.000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
