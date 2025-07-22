@props([
    'title' => '',
    'value' => 0,
    'icon' => null,
    'color' => 'gray', // options: gray, green, blue, red, yellow
])

@php
    $gradientMap = [
        'gray' => 'bg-gradient-to-tr from-gray-100 to-gray-300 text-gray-700',
        'green' => 'bg-gradient-to-tr from-emerald-200 to-emerald-400 text-emerald-900',
        'blue' => 'bg-gradient-to-tr from-blue-200 to-blue-400 text-blue-900',
        'red' => 'bg-gradient-to-tr from-red-200 to-red-400 text-red-900',
        'yellow' => 'bg-gradient-to-tr from-yellow-200 to-yellow-400 text-yellow-900',
    ];
    $circleClass = $gradientMap[$color] ?? $gradientMap['gray'];
@endphp

<div class="bg-white shadow-lg rounded-2xl p-6 flex flex-col items-center justify-center w-full h-[170px] transition-transform duration-200 hover:scale-105 hover:shadow-2xl border-2 gradient-border">
    @if($icon)
        <div class="shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-white shadow-md mb-2">
            <svg data-lucide="{{ $icon }}" class="w-10 h-10 gradient-text"></svg>
        </div>
    @endif
    <p class="text-lg text-gray-600 font-semibold mb-2 text-center tracking-wide">{{ $title }}</p>
    @if(trim($slot ?? ''))
        <div class="text-4xl font-extrabold text-gray-900 text-center">{{ $slot }}</div>
    @else
        <p class="text-4xl font-extrabold text-gray-900 text-center" x-data="{ val: 0 }" x-init="let v = {{ $value }}; let step = Math.max(1, Math.floor(v/30)); let i = setInterval(() => { if(val < v) { val += step; if(val > v) val = v; } else { clearInterval(i); } }, 20);" x-text="val.toLocaleString()">{{ $value }}</p>
    @endif
</div>






