@php
    $routeName = request()->route()?->getName();
    $paths = [];
    
    // Default prefix
    $paths[] = ['label' => __('Home'), 'url' => route('dashboard')];
    
    if ($routeName) {
        if ($routeName === 'dashboard') {
            $paths[] = ['label' => __('Dashboard'), 'url' => null];
        } elseif (str_starts_with($routeName, 'villages.')) {
            $paths[] = ['label' => __('Villages'), 'url' => route('villages.index')];
            if ($routeName === 'villages.create') {
                $paths[] = ['label' => __('Add Village'), 'url' => null];
            } elseif ($routeName === 'villages.edit') {
                $paths[] = ['label' => __('Edit'), 'url' => null];
            } elseif ($routeName === 'villages.show') {
                $paths[] = ['label' => __('Details'), 'url' => null];
            } else {
                $paths[] = ['label' => __('List'), 'url' => null];
            }
        } elseif (str_starts_with($routeName, 'farmers.')) {
            $paths[] = ['label' => __('Farmers'), 'url' => route('farmers.index')];
            if ($routeName === 'farmers.create') {
                $paths[] = ['label' => __('Register'), 'url' => null];
            } elseif ($routeName === 'farmers.edit') {
                $paths[] = ['label' => __('Edit'), 'url' => null];
            } elseif ($routeName === 'farmers.show') {
                $paths[] = ['label' => __('Profile'), 'url' => null];
            } else {
                $paths[] = ['label' => __('List'), 'url' => null];
            }
        } elseif (str_starts_with($routeName, 'milk-collections.')) {
            $paths[] = ['label' => __('Milk Collections'), 'url' => route('milk-collections.index')];
            if ($routeName === 'milk-collections.create') {
                $paths[] = ['label' => __('Record Entry'), 'url' => null];
            } elseif ($routeName === 'milk-collections.edit') {
                $paths[] = ['label' => __('Edit Entry'), 'url' => null];
            } elseif ($routeName === 'milk-collections.show') {
                $paths[] = ['label' => __('View Entry'), 'url' => null];
            } else {
                $paths[] = ['label' => __('List'), 'url' => null];
            }
        } elseif (str_starts_with($routeName, 'profile.')) {
            $paths[] = ['label' => __('Profile'), 'url' => null];
        }
    }
@endphp

<nav class="flex text-xs font-medium text-slate-500 space-x-1.5 items-center">
    @foreach ($paths as $index => $path)
        @if ($index > 0)
            <svg class="w-3.5 h-3.5 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
            </svg>
        @endif
        
        @if ($path['url'] && $index < count($paths) - 1)
            <a href="{{ $path['url'] }}" class="hover:text-indigo-650 hover:underline transition-colors">{{ $path['label'] }}</a>
        @else
            <span class="{{ $index === count($paths) - 1 ? 'text-slate-800 font-semibold' : '' }}">{{ $path['label'] }}</span>
        @endif
    @endforeach
</nav>
