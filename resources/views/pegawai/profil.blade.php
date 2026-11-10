<x-layouts.app title="Profil Pegawai - Gov.Trip" role="Pegawai" headerTitle="Profil Pegawai">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Profil Pegawai (col-span-7) -->
        <div class="lg:col-span-7">
            <x-card padding="lg">
                <div class="border-b border-[#E8EEF3] pb-3 mb-6">
                    <h3 class="text-[#123B72] text-[16px] font-semibold leading-tight">Data Profil Pegawai</h3>
                </div>

                <!-- Avatar & Identity -->
                <div class="flex items-center gap-4 pb-6 border-b border-[#E8EEF3]">
                    <div class="w-16 h-16 rounded-full bg-[#1684C4] text-white flex items-center justify-center font-semibold text-[24px] shrink-0">
                        {{ strtoupper(substr($employee?->name ?? $user->email ?? 'AP', 0, 2)) }}
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-[#1E293B] font-bold text-[18px]">
                            {{ $employee?->name ?? 'Andi Pratama' }}
                        </h3>
                        <span class="text-[#64748B] text-[13px] mt-0.5">
                            {{ $employee?->position ?? 'Staf Pegawai' }}
                        </span>
                        <span class="text-[12px] text-[#0066B3] mt-0.5 font-medium">NIP: {{ $employee?->nip ?? '-' }}</span>
                    </div>
                </div>

                <!-- Information Grid -->
                <div class="pt-6 divide-y divide-[#E8EEF3] text-[13.5px]">
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-3 first:pt-0">
                        <span class="text-[#64748B]">Unit Kerja / SKPD</span>
                        <span class="sm:col-span-2 font-medium text-[#1E293B]">{{ $employee?->department?->name ?? 'Bidang Perencanaan & Program' }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-3">
                        <span class="text-[#64748B]">Email Resmi</span>
                        <span class="sm:col-span-2 font-medium text-[#1E293B]">{{ $user->email ?? 'karyawan@gov.trip' }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-3">
                        <span class="text-[#64748B]">Atasan Langsung</span>
                        <span class="sm:col-span-2 font-medium text-[#1E293B]">{{ $employee?->supervisor?->name ?? 'Bapak Kepala Bidang' }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-3 last:pb-0">
                        <span class="text-[#64748B]">Pangkat / Golongan</span>
                        <span class="sm:col-span-2 font-medium text-[#1E293B]">{{ $employee?->grade ?? 'Penata Muda (III/a)' }}</span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right: Pengaturan Notifikasi & Akun (col-span-5) -->
        <div class="lg:col-span-5 flex flex-col gap-6">
            <x-card padding="lg">
                <div class="border-b border-[#E8EEF3] pb-3 mb-4">
                    <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Pengaturan Notifikasi</h3>
                    <p class="text-[#64748B] text-[12px] mt-0.5">Preferensi pemberitahuan akun</p>
                </div>

                <div class="flex flex-col gap-4 text-[13px]">
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-[#1E293B]">Notifikasi Email</span>
                        <input type="checkbox" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-[#1E293B]">Pemberitahuan Status SPPD</span>
                        <input type="checkbox" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-[#1E293B]">Pengingat Batas LPJ</span>
                        <input type="checkbox" checked class="w-4 h-4 rounded text-[#0066B3] accent-[#0066B3]" />
                    </label>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
