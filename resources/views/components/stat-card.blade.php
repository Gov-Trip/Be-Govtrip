@props([
    'title',
    'value',
    'sublabel' => null,
    'iconBg' => 'bg-[#E3F0FA]',
    'iconColor' => 'text-[#0066B3]',
    'href' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-[#E8EEF3] rounded-[14px] p-4 flex items-center gap-3.5 shadow-[0px_4px_14px_0px_rgba(18,59,114,0.05)] transition-all hover:border-[#C9D8EC]' . ($href ? ' cursor-pointer hover:shadow-md' : '')]) }}
     @if($href) onclick="window.location.href='{{ $href }}'" @endif>
    <div class="w-[42px] h-[42px] rounded-[10px] {{ $iconBg }} {{ $iconColor }} flex items-center justify-center shrink-0">
        {{ $slot }}
    </div>

    <div class="flex flex-col min-w-0">
        <span class="text-[#64748B] text-[12.5px] leading-tight font-medium truncate">
            {{ $title }}
        </span>
        <span class="text-[#123B72] text-[24px] font-semibold leading-tight mt-0.5">
            {{ $value }}
        </span>
        @if($sublabel)
            <span class="text-[#64748B] text-[11.5px] leading-none mt-0.5 truncate">
                {{ $sublabel }}
            </span>
        @endif
    </div>
</div>
