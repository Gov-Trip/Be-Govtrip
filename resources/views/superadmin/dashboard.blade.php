<x-layouts.app title="Dashboard Super Admin - Gov.Trip" role="Super Admin" headerTitle="Dashboard Super Administrator">
    <!-- Hero Banner -->
    <x-hero-banner
        title="Halo, {{ $userName ?? 'Pak Super Admin' }}!"
        subtitle="Pusat kendali master data, hak akses role pengguna, dan konfigurasi sistem Gov.Trip."
        actionText="Kelola Pengguna"
        actionHref="/superadmin/pengguna"
    />

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Total Pengguna"
            :value="$userCount ?? 0"
            sublabel="Aktif di seluruh SKPD"
            iconBg="bg-[#E3F0FA]"
            iconColor="text-[#0066B3]"
            href="/superadmin/pengguna"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Hak Akses Role"
            value="7 Role"
            sublabel="RBAC terkonfigurasi"
            iconBg="bg-[#DFF5F4]"
            iconColor="text-[#0B7A77]"
            href="/superadmin/akses"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Unit Kerja / SKPD"
            :value="($deptCount ?? 0) . ' Unit'"
            sublabel="Bidang & Divisi"
            iconBg="bg-[#FFF1D1]"
            iconColor="text-[#8A5A00]"
            href="/superadmin/unit"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4M5 21V10.85M19 21V10.85M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/></svg>
        </x-stat-card>

        <x-stat-card
            title="Audit Log Sistem"
            :value="($auditCount ?? 0) . ' Event'"
            sublabel="Semua aksi tercatat aman"
            iconBg="bg-[#E3F0FA]"
            iconColor="text-[#0066B3]"
            href="/superadmin/audit"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </x-stat-card>
    </div>

    <!-- 2 Columns: Role Distribution & System Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Role Distribution (col-span-7) -->
        <div class="lg:col-span-7">
            <x-card padding="lg">
                <div class="flex items-center justify-between pb-3 border-b border-[#E8EEF3] mb-4">
                    <div>
                        <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Distribusi Peran Pengguna</h3>
                        <p class="text-[#64748B] text-[12px] mt-0.5">Proporsi akun aktif berdasarkan level hak akses dari basis data</p>
                    </div>
                    <a href="/superadmin/pengguna" class="text-[12px] text-[#0066B3] font-medium hover:underline">Kelola &rarr;</a>
                </div>

                <div class="flex flex-col gap-3.5 text-[13px]">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-[#1E293B] font-medium">Pegawai Pelaksana</span>
                            <span class="text-[#64748B]">{{ $roleStats['staff']['count'] ?? 0 }} orang ({{ $roleStats['staff']['pct'] ?? 0 }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#0066B3] h-full rounded-full" style="width: {{ $roleStats['staff']['pct'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-[#1E293B] font-medium">Atasan Langsung & Kepala Bidang</span>
                            <span class="text-[#64748B]">{{ $roleStats['manager']['count'] ?? 0 }} orang ({{ $roleStats['manager']['pct'] ?? 0 }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#1684C4] h-full rounded-full" style="width: {{ $roleStats['manager']['pct'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-[#1E293B] font-medium">Bendahara & Keuangan</span>
                            <span class="text-[#64748B]">{{ $roleStats['finance']['count'] ?? 0 }} orang ({{ $roleStats['finance']['pct'] ?? 0 }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#0B7A77] h-full rounded-full" style="width: {{ $roleStats['finance']['pct'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-[#1E293B] font-medium">Admin Travel & Super Admin</span>
                            <span class="text-[#64748B]">{{ $roleStats['admin']['count'] ?? 0 }} orang ({{ $roleStats['admin']['pct'] ?? 0 }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#F5A000] h-full rounded-full" style="width: {{ $roleStats['admin']['pct'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right: Recent Activity Log (col-span-5) -->
        <div class="lg:col-span-5">
            <x-card padding="lg">
                <div class="flex items-center justify-between pb-3 border-b border-[#E8EEF3] mb-4">
                    <div>
                        <h3 class="text-[#123B72] text-[15px] font-semibold leading-tight">Aktivitas Sistem Terbaru</h3>
                        <p class="text-[#64748B] text-[12px] mt-0.5">Audit log perubahan konfigurasi & master data real-time</p>
                    </div>
                    <a href="/superadmin/audit" class="text-[12px] text-[#0066B3] font-medium hover:underline">Semua &rarr;</a>
                </div>

                <div class="divide-y divide-[#E8EEF3] text-[12.5px]">
                    @forelse($recentAudits as $log)
                        <div class="py-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-[#1E293B] block truncate max-w-[200px]">
                                    {{ $log->action }} &middot; {{ $log->module }}
                                </span>
                                <span class="text-[11px] text-[#64748B] shrink-0">
                                    {{ $log->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <span class="text-[#64748B] text-[11.5px]">
                                Oleh: {{ $log->user?->employee?->name ?? $log->user?->email ?? 'Sistem' }}
                            </span>
                            <p class="text-[11.5px] text-[#64748B] mt-0.5 truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </p>
                        </div>
                    @empty
                        <div class="py-6 text-center text-gray-500 text-xs">
                            Belum ada aktivitas audit log.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
