<x-layouts.app title="Role & Hak Akses - Gov.Trip" role="Super Admin" headerTitle="Role & Hak Akses (RBAC)">
    @if(session('success'))
        <div class="mb-5 p-4 rounded-xl bg-[#DFF5F4] text-[#0B7A77] border border-[#a2dedc] text-[13px] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('superadmin.akses.update') }}" method="POST">
        @csrf
        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-[#E8EEF3] gap-3 mb-6">
                <div>
                    <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Matriks Otorisasi Hak Akses Sistem</h3>
                    <p class="text-[#64748B] text-[12.5px] mt-0.5">Konfigurasi izin operasional & bypass persetujuan untuk setiap tingkatan jabatan</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" formaction="{{ route('superadmin.akses.reset') }}" onclick="return confirm('Apakah Anda yakin ingin mengembalikan matriks ke pengaturan default standar?')"
                            class="px-3.5 py-2 text-[12px] font-medium text-[#64748B] hover:text-[#1E293B] border border-[#E8EEF3] rounded-[10px] hover:bg-slate-50 transition-colors cursor-pointer">
                        Reset ke Default
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-[12.5px] font-semibold bg-[#0066B3] hover:bg-[#005596] text-white rounded-[10px] transition-colors shadow-sm cursor-pointer">
                        Simpan Perubahan Matriks
                    </button>
                </div>
            </div>

            <!-- Highlight Fast-Track Bypass Explanation -->
            <div class="mb-5 p-3.5 rounded-[10px] bg-[#E3F0FA] border border-[#C9D8EC] text-[12.5px] text-[#0066B3] flex items-start gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mt-0.5 shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <div class="leading-relaxed">
                    <strong>Fitur Fast-Track Bypass Persetujuan:</strong> Jika peran <em>Pegawai</em> dicentang pada baris <strong>"Setujui pengajuan"</strong>, maka seluruh pengajuan dinas yang dikirim pegawai tersebut akan melewati pemeriksaan Atasan dan <strong>langsung diterbitkan nomor SPPD secara otomatis (Status: Disetujui)</strong>.
                </div>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse text-[13px]">
                    <thead class="border-b border-[#E8EEF3] bg-slate-50">
                        <tr>
                            <th class="py-3 px-4 text-[#123B72] font-semibold">Fitur / Hak Akses Operasional</th>
                            @foreach($roles as $role)
                                <th class="py-3 px-3 text-center text-[#123B72] font-medium">{{ $role }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8EEF3]">
                        @foreach($matrix as $permKey => $row)
                            <tr class="hover:bg-[#F1F7FB]/50 transition-colors">
                                <td class="py-3.5 px-4 font-medium text-[#1E293B]">
                                    <span class="block">{{ $row['label'] ?? $permKey }}</span>
                                    <span class="text-[11px] text-[#64748B] font-mono">{{ $permKey }}</span>
                                </td>
                                @foreach($roles as $role)
                                    @php
                                        $isChecked = !empty($row[$role]);
                                    @endphp
                                    <td class="py-3.5 px-3 text-center">
                                        <input type="hidden" name="matrix[{{ $permKey }}][{{ $role }}]" value="0" />
                                        <input type="checkbox"
                                               name="matrix[{{ $permKey }}][{{ $role }}]"
                                               value="1"
                                               {{ $isChecked ? 'checked' : '' }}
                                               class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3] cursor-pointer" />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </form>
</x-layouts.app>
