@props([
    'title',
    'description' => null
])

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 pb-2 border-b border-slate-200/60">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2.5">
            {{ $title }}
        </h1>
        @if($description)
            <p class="text-xs text-slate-500 mt-1 font-medium sm:text-sm">{{ $description }}</p>
        @endif
    </div>
    
    @if(isset($actions))
        <div class="flex items-center gap-3 self-start md:self-auto shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
