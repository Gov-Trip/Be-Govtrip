@props([
    'title',
    'subtitle',
    'actionText' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'bg-[#123B72] rounded-[16px] p-6 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm']) }}>
    <div class="flex flex-col">
        <h2 class="text-white text-[20px] sm:text-[22px] font-bold leading-tight">
            {{ $title }}
        </h2>
        <p class="text-[#C9D8EC] text-[13.5px] mt-1">{{ $subtitle }}</p>
    </div>

    @if($actionText)
        <a href="{{ $actionHref ?? '#' }}"
           class="self-start md:self-auto shrink-0 inline-flex items-center justify-center font-semibold text-[13px] px-4 py-2 rounded-[10px] gap-2 bg-[#F5A000] hover:bg-[#e09200] active:bg-[#c98300] text-[#1E293B] shadow-sm transition-all cursor-pointer">
            <span>{{ $actionText }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
            </svg>
        </a>
    @endif
</div>
