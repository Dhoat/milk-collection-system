@props([
    'title',
    'description' => null
])

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-800 sm:text-2xl">{{ $title }}</h1>
        @if($description)
            <p class="text-xs text-slate-400 mt-1 sm:text-sm">{{ $description }}</p>
        @endif
    </div>
    
    @if(isset($actions))
        <div class="flex items-center gap-3 self-start md:self-auto">
            {{ $actions }}
        </div>
    @endif
</div>
