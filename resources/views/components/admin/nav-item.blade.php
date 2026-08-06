@props([
    'route' => '#',
    'active' => null,
    'disabled' => false,
    'badge' => null
])

@php
    $isActive = $active && request()->routeIs($active);
    
    $classes = 'flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-all duration-200 group ';
    
    if ($disabled) {
        $classes .= 'text-slate-400 opacity-60 cursor-not-allowed pointer-events-none';
    } elseif ($isActive) {
        $classes .= 'bg-indigo-50/80 text-indigo-600 font-semibold shadow-sm shadow-indigo-100/50';
    } else {
        $classes .= 'text-slate-600 hover:text-slate-900 hover:bg-slate-50/80';
    }
    
    $iconClasses = 'w-5 h-5 transition-colors duration-200 ';
    if ($disabled) {
        $iconClasses .= 'text-slate-300';
    } elseif ($isActive) {
        $iconClasses .= 'text-indigo-600';
    } else {
        $iconClasses .= 'text-slate-400 group-hover:text-slate-500';
    }
@endphp

@if($disabled)
    <div class="{{ $classes }}">
        <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            {{ $icon }}
        </svg>
        <span class="flex-grow truncate">{{ $slot }}</span>
        @if($badge)
            <span class="px-1.5 py-0.5 text-xxs font-medium rounded bg-slate-100 border border-slate-200/50 text-slate-400 uppercase tracking-wider scale-90">{{ $badge }}</span>
        @endif
    </div>
@else
    <a href="{{ $route !== '#' ? route($route) : '#' }}" class="{{ $classes }}">
        <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            {{ $icon }}
        </svg>
        <span class="flex-grow truncate">{{ $slot }}</span>
        @if($badge)
            <span class="px-1.5 py-0.5 text-xxs font-medium rounded bg-slate-100 border border-slate-200/50 text-slate-400 uppercase tracking-wider scale-90">{{ $badge }}</span>
        @endif
    </a>
@endif
