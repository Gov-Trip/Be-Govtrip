@props([
    'notifications' => [],
    'title' => 'Notifikasi',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-[#E8EEF3] rounded-[14px] shadow-[0px_4px_14px_0px_rgba(18,59,114,0.05)] p-5 sm:p-6']) }}>
    <div class="flex items-center justify-between pb-4 border-b border-[#E8EEF3] mb-4">
        <div>
            <h3 class="text-[#123B72] text-[15px] sm:text-[16px] font-semibold leading-tight">{{ $title }}</h3>
            @if($description)
                <p class="text-[#64748B] text-[12.5px] mt-0.5">{{ $description }}</p>
            @endif
        </div>

        <button type="button" onclick="document.querySelectorAll('.unread-dot').forEach(d => d.remove())"
                class="inline-flex items-center justify-center font-medium transition-all px-3 py-1.5 text-[12px] rounded-[8px] bg-white hover:bg-[#F1F7FB] text-[#1E293B] border border-[#E8EEF3] cursor-pointer">
            Tandai semua dibaca
        </button>
    </div>

    <div class="divide-y divide-[#E8EEF3]">
        @forelse($notifications as $item)
            @php
                $cat = $item['category'] ?? 'info';
                $catStyles = [
                    'warning' => 'bg-[#FFF1D1] text-[#8A5A00]',
                    'danger' => 'bg-[#FBE8E8] text-[#B02A2A]',
                    'success' => 'bg-[#DFF5F4] text-[#0B7A77]',
                    'neutral' => 'bg-[#E8EEF3] text-[#64748B]',
                    'info' => 'bg-[#E3F0FA] text-[#0066B3]',
                ];
                $bgClass = $catStyles[$cat] ?? $catStyles['info'];
                $isUnread = !empty($item['unread']);
            @endphp
            <div class="py-3.5 px-3 flex items-center justify-between gap-4 rounded-[10px] transition-colors {{ $isUnread ? 'bg-[#F1F7FB]/40' : 'hover:bg-slate-50' }}">
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 rounded-[10px] flex items-center justify-center shrink-0 {{ $bgClass }}">
                        @if($cat === 'success')
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                        @elseif($cat === 'warning' || $cat === 'danger')
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10" /><line x1="12" y1="16" x2="12" y2="12" /><line x1="12" y1="8" x2="12.01" y2="8" /></svg>
                        @endif
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[13.5px] font-semibold text-[#1E293B]">{{ $item['title'] }}</span>
                        <span class="text-[12px] text-[#64748B] mt-0.5">
                            {{ $item['description'] }}
                            @if(!empty($item['time']))
                                &middot; {{ $item['time'] }}
                            @endif
                        </span>
                    </div>
                </div>

                @if($isUnread)
                    <span class="unread-dot w-2.5 h-2.5 rounded-full bg-[#F5A000] shrink-0"></span>
                @endif
            </div>
        @empty
            <div class="py-8 text-center text-[#64748B] text-[13px]">
                Tidak ada notifikasi baru.
            </div>
        @endforelse
    </div>
</div>
