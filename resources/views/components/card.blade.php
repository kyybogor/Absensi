@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-[#FAF7F2] border border-[#DDD5C7] rounded-[2px] p-5 md:p-6 shadow-[0_1px_2px_rgba(27,27,24,0.04)]']) }}>
    @if ($title || $subtitle)
        <div class="mb-4 pb-3 border-b border-[#DDD5C7]">
            @if ($title)
                <h2 class="text-lg font-bold text-[#1B1B18] tracking-tight">{{ $title }}</h2>
            @endif
            @if ($subtitle)
                <p class="text-xs text-[#57574E] mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
