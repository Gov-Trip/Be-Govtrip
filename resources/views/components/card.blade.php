@props([
    'padding' => 'md',
])

@php
    $paddingStyles = [
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-5 sm:p-6',
        'lg' => 'p-6 sm:p-8',
    ];

    $paddingClass = $paddingStyles[$padding] ?? $paddingStyles['md'];
@endphp

<div {{ $attributes->merge(['class' => "bg-white border border-[#E8EEF3] rounded-[14px] shadow-[0px_4px_14px_0px_rgba(18,59,114,0.05)] transition-all {$paddingClass}"]) }}>
    {{ $slot }}
</div>
