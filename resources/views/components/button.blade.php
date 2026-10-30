@props([
    'variant' => 'primary', // primary, secondary, accent
    'type' => 'submit',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-mono text-xs uppercase tracking-wider font-semibold px-4 py-2.5 rounded-[2px] transition duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-[#1F3D2B] hover:bg-[#162E20] text-[#FAF7F2] border border-[#1F3D2B]',
        'secondary' => 'bg-transparent hover:bg-[#ECE5DA] text-[#1B1B18] border border-[#DDD5C7]',
        'accent' => 'bg-[#C2542D] hover:bg-[#9F4120] text-[#FAF7F2] border border-[#C2542D]',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
