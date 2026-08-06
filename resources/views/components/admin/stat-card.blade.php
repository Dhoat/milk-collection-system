@props([
    'title',
    'value',
    'description' => null,
    'color' => 'indigo'
])

@php
    $colors = [
        'indigo' => [
            'bg' => 'bg-indigo-50 text-indigo-600',
            'border' => 'border-indigo-100',
            'glow' => 'shadow-indigo-100/30'
        ],
        'emerald' => [
            'bg' => 'bg-emerald-50 text-emerald-600',
            'border' => 'border-emerald-100',
            'glow' => 'shadow-emerald-100/30'
        ],
        'amber' => [
            'bg' => 'bg-amber-50 text-amber-600',
            'border' => 'border-amber-100',
            'glow' => 'shadow-amber-100/30'
        ],
        'rose' => [
            'bg' => 'bg-rose-50 text-rose-600',
            'border' => 'border-rose-100',
            'glow' => 'shadow-rose-100/30'
        ],
    ];

    $selectedColor = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm shadow-slate-100/50 hover:shadow-md hover:shadow-slate-150/30 transition-all duration-200 flex items-start gap-4">
    @if(isset($icon))
        <div class="p-3 rounded-xl {{ $selectedColor['bg'] }} border {{ $selectedColor['border'] }} shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                {{ $icon }}
            </svg>
        </div>
    @endif
    
    <div class="flex-grow min-w-0">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider truncate">{{ $title }}</p>
        <h4 class="text-2xl font-bold tracking-tight text-slate-800 mt-1 truncate">{{ $value }}</h4>
        @if($description)
            <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                {{ $description }}
            </p>
        @endif
    </div>
</div>
