@props([
    'disabled' => false,
    'error' => null,
])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'w-full bg-[#FAF7F2] border ' . ($error ? 'border-[#C2542D] focus:border-[#C2542D]' : 'border-[#DDD5C7] focus:border-[#1F3D2B]') . ' text-[#1B1B18] text-sm px-3 py-2 rounded-[2px] transition duration-150 focus:outline-none focus:ring-1 ' . ($error ? 'focus:ring-[#C2542D]' : 'focus:ring-[#1F3D2B]') . ' disabled:opacity-60 disabled:cursor-not-allowed placeholder-[#8E8B82]'
]) !!}>
