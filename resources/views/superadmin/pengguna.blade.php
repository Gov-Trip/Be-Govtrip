<x-layouts.app title="Manajemen Pengguna - Gov.Trip" role="Super Admin" headerTitle="Manajemen Pengguna & Akun">
    @if(session('success'))
        <div class="mb-5 p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 p-4 rounded-xl bg-[#FDE8E8] text-[#9B1C1C] border border-[#f8b4b4] text-[13px] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E8EEF3] mb-6">
            <div>
                <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Master Akun Pengguna & Pegawai</h3>
                <p class="text-[#64748B] text-[12.5px] mt-0.5">Kelola akun pegawai, atasan, bendahara, dan administrator sistem</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="text" id="userSearch" placeholder="Cari nama pengguna..."
                       onkeyup="filterUsers()"
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
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Nama Pengguna / Pegawai</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Email / Akun</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Jabatan & Grade</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Unit Kerja</th>
                        <th class="py-2.5 px-3 text-[#64748B] text-[12px] font-medium">Status Akun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8EEF3] text-[13px]">
                    @forelse($users ?? [] as $usr)
                        @php
                            $emp = $usr->employee;
                            $dept = $emp?->department;
                            $deptCode = $dept?->code ?? '';
                            $roleLabel = match($deptCode) {
                                'SUPER_ADMIN' => 'Super Admin',
                                'FINANCE' => 'Bendahara',
                                'ADMIN_TRAVEL' => 'Admin Travel',
                                default => ($emp?->grade === 'Manager' ? 'Atasan' : 'Pegawai')
                            };
                        @endphp
                        <tr class="hover:bg-[#F1F7FB]/50 transition-colors user-row">
                            <td class="py-3 px-3">
                                <span class="font-semibold text-[#1E293B] block user-name">{{ $emp?->name ?? 'User '.$usr->id }}</span>
                                <span class="text-[11.5px] text-[#64748B]">NIP: {{ $emp?->nip ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-3 text-[#64748B]">
                                {{ $usr->email }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-medium text-[#1E293B] block">{{ $emp?->position ?? '-' }}</span>
                                <span class="text-[11px] px-2 py-0.5 rounded-full font-medium inline-block mt-0.5 {{ $emp?->grade === 'Manager' ? 'bg-[#FFF1D1] text-[#8A5A00]' : 'bg-[#E3F0FA] text-[#0066B3]' }}">
                                    {{ $emp?->grade ?? 'Staff' }} ({{ $roleLabel }})
                                </span>
                            </td>
                            <td class="py-3 px-3 text-[#64748B]">
                                {{ $dept?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-3">
                                <x-badge status="{{ $usr->is_active ? 'Aktif' : 'Nonaktif' }}">{{ $usr->is_active ? 'Aktif' : 'Nonaktif' }}</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-[#64748B]">Belum ada data pengguna yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Modal Tambah Pengguna -->
    <div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-[16px] max-w-[480px] w-full p-6 shadow-2xl flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E8EEF3]">
                <h3 class="text-[16px] font-bold text-[#123B72]">Tambah Akun Pengguna & Pegawai</h3>
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-[#64748B] hover:text-[#1E293B] cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form action="{{ route('superadmin.pengguna.store') }}" method="POST" class="flex flex-col gap-3 text-[13px]">
                @csrf
                <div class="flex flex-col">
                    <label class="text-[#64748B] text-[12px] font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Contoh: Rahmat Hidayat, S.Kom" class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12px] font-medium mb-1">NIP</label>
                        <input type="text" name="nip" required placeholder="19870101..." class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12px] font-medium mb-1">Tingkat Grade</label>
                        <select name="grade" class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]">
                            <option value="Staff">Staff</option>
                            <option value="Manager">Manager / Eselon III</option>
                            <option value="Director">Kepala Dinas / Eselon II</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label class="text-[#64748B] text-[12px] font-medium mb-1">Jabatan Resmi</label>
                    <input type="text" name="position" required placeholder="Contoh: Staf Perencanaan / Kepala Seksi" class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                </div>

                <div class="flex flex-col">
                    <label class="text-[#64748B] text-[12px] font-medium mb-1">Unit Kerja / Departemen</label>
                    <select name="department_id" class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]">
                        @foreach($departments ?? [] as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12px] font-medium mb-1">Alamat Email</label>
                        <input type="email" name="email" required placeholder="user@gov.trip" class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-[#64748B] text-[12px] font-medium mb-1">Kata Sandi</label>
                        <input type="password" name="password" required value="password123" class="px-3 py-2 border border-[#E8EEF3] rounded-[8px] focus:outline-none focus:border-[#0066B3]" />
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-[#E8EEF3] mt-2">
                    <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-4 py-2 text-[12.5px] rounded-[8px] border border-[#E8EEF3] text-[#64748B] cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[8px] shadow-sm cursor-pointer">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterUsers() {
            const query = (document.getElementById('userSearch').value || '').toLowerCase();
            const rows = document.querySelectorAll('.user-row');
            rows.forEach(r => {
                const text = r.innerText.toLowerCase();
                r.style.display = text.includes(query) ? '' : 'none';
            });
        }
    </script>
</x-layouts.app>
