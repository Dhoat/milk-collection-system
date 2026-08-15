@props([
    'route' => '#',
    'active' => null,
    'disabled' => false,
    'badge' => null
])

@php
    $isActive = $active && request()->routeIs($active);
    
    $classes = 'flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group ';
    
    if ($disabled) {
        $classes .= 'text-slate-400 opacity-50 cursor-not-allowed pointer-events-none';
    } elseif ($isActive) {
        $classes .= 'bg-[#005BAC] text-white font-semibold shadow-md shadow-sky-900/30';
    } else {
        $classes .= 'text-slate-300 hover:text-white hover:bg-white/10';
    }
    
    $iconClasses = 'w-5 h-5 transition-colors duration-200 shrink-0 ';
    if ($disabled) {
        $iconClasses .= 'text-slate-500';
    } elseif ($isActive) {
        $iconClasses .= 'text-white';
    } else {
        $iconClasses .= 'text-sky-200/70 group-hover:text-white';
    }
@endphp

@if($disabled)
    <div class="{{ $classes }}">
        <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            {{ $icon }}
        </svg>
        <span class="flex-grow truncate">{{ $slot }}</span>
        @if($badge)
            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-slate-700 text-slate-300 uppercase tracking-wider">{{ $badge }}</span>
        @endif
    </div>
@else
    <a href="{{ $route !== '#' ? route($route) : '#' }}" class="{{ $classes }}">
        <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            {{ $icon }}
        </svg>
        <span class="flex-grow truncate">{{ $slot }}</span>
        @if($badge)
            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-sky-500/20 text-sky-200 uppercase tracking-wider">{{ $badge }}</span>
        @endif
    </a>
@endif
