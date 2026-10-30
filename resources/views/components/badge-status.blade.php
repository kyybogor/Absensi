@props(['status'])

@php
    $statusEnum = $status instanceof \App\Enums\StatusAbsensi
        ? $status
        : \App\Enums\StatusAbsensi::tryFrom((string) $status);
    $label = $statusEnum?->label() ?? ucfirst(str_replace('_', ' ', (string) $status));
@endphp

@if ($statusEnum?->isIzin())
    <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-medium tracking-wide uppercase border border-[#2B527E] text-[#2B527E] bg-[#EAF0F8] rounded-[2px]">
        {{ $label }}
    </span>
@elseif ($statusEnum?->isTelat())
    <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-medium tracking-wide uppercase border border-[#C2542D] text-[#C2542D] bg-[#FBECE7] rounded-[2px]">
        {{ $label }}
    </span>
@else
    <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-medium tracking-wide uppercase border border-[#1F3D2B] text-[#1F3D2B] bg-[#E5EEE8] rounded-[2px]">
        {{ $label }}
    </span>
@endif
