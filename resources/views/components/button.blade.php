@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'leftIcon' => null,
    'rightIcon' => null,
])

@php
    $variantClasses = [
        'primary' => 'bg-[#0066B3] hover:bg-[#005596] active:bg-[#00447a] text-white shadow-sm border border-transparent',
        'warning' => 'bg-[#F5A000] hover:bg-[#e09200] active:bg-[#c98300] text-[#1E293B] font-semibold shadow-sm border border-transparent',
        'outline' => 'bg-white hover:bg-[#F1F7FB] active:bg-[#E3F0FA] text-[#1E293B] border border-[#E8EEF3]',
        'secondary' => 'bg-[#E3F0FA] hover:bg-[#cde4f7] active:bg-[#b8d9f4] text-[#0066B3] font-medium border border-transparent',
        'danger' => 'bg-[#B02A2A] hover:bg-[#992222] active:bg-[#7e1c1c] text-white shadow-sm border border-transparent',
        'danger-outline' => 'bg-white hover:bg-[#FBE8E8] active:bg-[#f6d0d0] text-[#B02A2A] border border-[#B02A2A]',
        'ghost' => 'bg-transparent hover:bg-slate-100 active:bg-slate-200 text-[#64748B] hover:text-[#123B72]',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-[12px] rounded-[8px] gap-1.5',
        'md' => 'px-4 py-2 text-[13px] rounded-[10px] gap-2',
        'lg' => 'px-5 py-2.5 text-[14px] rounded-[10px] gap-2.5',
    ];

    $baseClasses = 'inline-flex items-center justify-center font-medium transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if($leftIcon)
            <span class="shrink-0">{!! $leftIcon !!}</span>
        @endif
        <span>{{ $slot }}</span>
        @if($rightIcon)
            <span class="shrink-0">{!! $rightIcon !!}</span>
        @endif
    </a>
@else
    <button {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if($leftIcon)
            <span class="shrink-0">{!! $leftIcon !!}</span>
        @endif
        <span>{{ $slot }}</span>
        @if($rightIcon)
            <span class="shrink-0">{!! $rightIcon !!}</span>
        @endif
    </button>
@endif
