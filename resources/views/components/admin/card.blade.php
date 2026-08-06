@props([
    'title' => null,
    'description' => null,
    'headerActions' => null
])

<div {{ $attributes->merge(['class' => 'bg-white border border-slate-200/80 rounded-2xl shadow-sm shadow-slate-100/50']) }}>
    @if($title || $description || $headerActions)
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                @if($title)
                    <h3 class="text-sm font-bold text-slate-800 tracking-tight">{{ $title }}</h3>
                @endif
                @if($description)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $description }}</p>
                @endif
            </div>
            @if($headerActions)
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
