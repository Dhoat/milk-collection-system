@props([
    'title',
    'value',
    'description' => null,
    'color' => 'blue'
])

@php
    $colors = [
        'blue' => [
            'bg' => 'bg-blue-50 text-[#005BAC]',
            'border' => 'border-blue-100',
            'topBorder' => 'border-t-[#005BAC]'
        ],
        'emerald' => [
            'bg' => 'bg-emerald-50 text-emerald-600',
            'border' => 'border-emerald-100',
            'topBorder' => 'border-t-emerald-500'
        ],
        'amber' => [
            'bg' => 'bg-amber-50 text-amber-600',
            'border' => 'border-amber-100',
            'topBorder' => 'border-t-amber-500'
        ],
        'rose' => [
            'bg' => 'bg-rose-50 text-rose-600',
            'border' => 'border-rose-100',
            'topBorder' => 'border-t-rose-500'
        ],
        'indigo' => [
            'bg' => 'bg-indigo-50 text-indigo-600',
            'border' => 'border-indigo-100',
            'topBorder' => 'border-t-indigo-500'
        ],
    ];

    $selectedColor = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white border border-slate-200/80 border-t-4 {{ $selectedColor['topBorder'] }} rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-start gap-4">
    @if(isset($icon))
        <div class="p-3 rounded-xl {{ $selectedColor['bg'] }} border {{ $selectedColor['border'] }} shadow-sm shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                {{ $icon }}
            </svg>
        </div>
    @endif
    
    <div class="flex-grow min-w-0">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider truncate">{{ $title }}</p>
        <h4 class="text-2xl font-extrabold tracking-tight text-slate-900 mt-1 truncate">{{ $value }}</h4>
        @if($description)
            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1 font-medium">
                {{ $description }}
            </p>
        @endif
    </div>
</div>
