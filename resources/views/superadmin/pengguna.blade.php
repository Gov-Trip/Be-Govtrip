<x-layouts.app title="Manajemen Pengguna - Gov.Trip" role="Super Admin" headerTitle="Manajemen Pengguna & Akun">
    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Master Akun Pengguna</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Kelola akun pegawai, atasan, bendahara, dan administrator sistem</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="text" id="userSearch" placeholder="Cari nama pengguna..."
                       onkeyup="GovTrip.filterTable('userSearch', 'userTable')"
                       class="px-3 py-1.5 text-[12.5px] bg-[#F1F7FB] border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                <button type="button" onclick="document.getElementById('addUserModal').classList.remove('hidden')"
                        class="px-3.5 py-1.5 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[8px] transition-colors cursor-pointer">
                    + Tambah Pengguna
                </button>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse" id="userTable">
                <thead class="border-b border-[#E8EEF3]">
                    <tr>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nama Pengguna</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Peran / Role</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Unit Kerja</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status Akun</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#1E293B]">Liyana Agustin, S.AP</td>
                        <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#E3F0FA] text-[#0066B3] font-medium">Pegawai</span></td>
                        <td class="py-3 px-3 text-[#64748B]">Bidang Perencanaan</td>
                        <td class="py-3 px-3"><x-badge status="Aktif">Aktif</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <button type="button" onclick="alert('Ubah status')" class="text-[#0066B3] hover:underline font-medium text-[12px] mr-2">Edit</button>
                            <button type="button" onclick="this.closest('tr').remove()" class="text-[#B02A2A] hover:underline font-medium text-[12px]">Hapus</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#1E293B]">Bapak Kepala Bidang</td>
                        <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#E3F0FA] text-[#0066B3] font-medium">Atasan</span></td>
                        <td class="py-3 px-3 text-[#64748B]">Bidang Perencanaan</td>
                        <td class="py-3 px-3"><x-badge status="Aktif">Aktif</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <button type="button" onclick="alert('Ubah status')" class="text-[#0066B3] hover:underline font-medium text-[12px] mr-2">Edit</button>
                            <button type="button" onclick="this.closest('tr').remove()" class="text-[#B02A2A] hover:underline font-medium text-[12px]">Hapus</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#1E293B]">Ibu Bendahara</td>
                        <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#FFF1D1] text-[#8A5A00] font-medium">Bendahara</span></td>
                        <td class="py-3 px-3 text-[#64748B]">Divisi Keuangan</td>
                        <td class="py-3 px-3"><x-badge status="Aktif">Aktif</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <button type="button" onclick="alert('Ubah status')" class="text-[#0066B3] hover:underline font-medium text-[12px] mr-2">Edit</button>
                            <button type="button" onclick="this.closest('tr').remove()" class="text-[#B02A2A] hover:underline font-medium text-[12px]">Hapus</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#1E293B]">Kang Tiket</td>
                        <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#E3F0FA] text-[#0066B3] font-medium">Admin Travel</span></td>
                        <td class="py-3 px-3 text-[#64748B]">Logistik & Akomodasi</td>
                        <td class="py-3 px-3"><x-badge status="Aktif">Aktif</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <button type="button" onclick="alert('Ubah status')" class="text-[#0066B3] hover:underline font-medium text-[12px] mr-2">Edit</button>
                            <button type="button" onclick="this.closest('tr').remove()" class="text-[#B02A2A] hover:underline font-medium text-[12px]">Hapus</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                        <td class="py-3 px-3 font-semibold text-[#1E293B]">Pak Super Admin</td>
                        <td class="py-3 px-3"><span class="px-2.5 py-0.5 rounded-full text-[11.5px] bg-[#123B72] text-white font-medium">Super Admin</span></td>
                        <td class="py-3 px-3 text-[#64748B]">IT & System Admin</td>
                        <td class="py-3 px-3"><x-badge status="Aktif">Aktif</x-badge></td>
                        <td class="py-3 px-3 text-right">
                            <button type="button" onclick="alert('Ubah status')" class="text-[#0066B3] hover:underline font-medium text-[12px] mr-2">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Modal Tambah Pengguna -->
    <div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-[16px] max-w-[460px] w-full p-6 shadow-2xl flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8EEF3]">
                <h3 class="text-[16px] font-bold text-[#123B72]">Tambah Akun Pengguna</h3>
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-[#64748B] hover:text-[#1E293B]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form onsubmit="event.preventDefault(); alert('Pengguna baru berhasil ditambahkan!'); document.getElementById('addUserModal').classList.add('hidden');" class="flex flex-col gap-3 text-[13px]">
                <div class="flex flex-col">
                    <label class="text-[#64748B] text-[12px] font-medium mb-1">Nama Lengkap</label>
                    <input type="text" required placeholder="Contoh: Budi Santoso" class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                </div>
                <div class="flex flex-col">
                    <label class="text-[#64748B] text-[12px] font-medium mb-1">Email</label>
                    <input type="email" required placeholder="email@gov.trip" class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                </div>
                <div class="flex flex-col">
                    <label class="text-[#64748B] text-[12px] font-medium mb-1">Role / Peran</label>
                    <select class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]">
                        <option>Pegawai</option>
                        <option>Atasan</option>
                        <option>Bendahara</option>
                        <option>Admin Travel</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="text-[#64748B] text-[12px] font-medium mb-1">Unit Kerja</label>
                    <select class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]">
                        <option>Bidang Perencanaan & Program</option>
                        <option>Divisi Keuangan</option>
                        <option>Logistik & Akomodasi</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-[#E8EEF3]">
                    <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-3 py-1.5 text-[12.5px] border border-[#E8EEF3] rounded-[8px]">Batal</button>
                    <button type="submit" class="px-4 py-1.5 text-[12.5px] font-semibold bg-[#0066B3] text-white rounded-[8px]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
