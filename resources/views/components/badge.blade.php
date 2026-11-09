@props([
    'variant' => null,
    'status' => null,
    'size' => 'md',
])

@php
    $text = (string) ($slot->isNotEmpty() ? $slot : $status);
    $s = strtolower($text);

    if ($variant) {
        $resolved = $variant;
    } else {
        if (str_contains($s, 'berjalan') || str_contains($s, 'aktif') || str_contains($s, 'proses')) {
            $resolved = 'info';
        } elseif (str_contains($s, 'setuju') || str_contains($s, 'selesai') || str_contains($s, 'terbit') || (str_contains($s, 'lengkap') && !str_contains($s, 'belum'))) {
            $resolved = 'success';
        } elseif (str_contains($s, 'tunggu') || str_contains($s, 'verifikasi') || str_contains($s, 'pending') || str_contains($s, 'belum') || str_contains($s, 'perlu')) {
            $resolved = 'warning';
        } elseif (str_contains($s, 'tolak') || str_contains($s, 'batal') || str_contains($s, 'gagal') || str_contains($s, 'darurat')) {
            $resolved = 'danger';
        } elseif (str_contains($s, 'draft') || str_contains($s, 'arsip')) {
            $resolved = 'neutral';
        } else {
            $resolved = 'info';
        }
    }

    $styles = [
        'primary' => 'bg-[#E3F0FA] text-[#0066B3]',
        'info' => 'bg-[#E3F0FA] text-[#0066B3]',
        'success' => 'bg-[#DFF5F4] text-[#0B7A77]',
        'warning' => 'bg-[#FFF1D1] text-[#8A5A00]',
        'danger' => 'bg-[#FBE8E8] text-[#B02A2A]',
        'neutral' => 'bg-slate-100 text-[#64748B]',
    ];

    $style = $styles[$resolved] ?? $styles['info'];
    $sizeClass = $size === 'sm' ? 'px-2 py-0.5 text-[11px]' : 'px-2.5 py-0.5 text-[11.5px]';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full shrink-0 transition-colors {$sizeClass} {$style}"]) }}>
    {{ $slot->isNotEmpty() ? $slot : $status }}
</span>
