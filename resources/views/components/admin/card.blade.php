@props([
    'title' => null,
    'description' => null,
    'headerActions' => null
])

<div {{ $attributes->merge(['class' => 'bg-white border border-slate-200/80 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden']) }}>
    @if($title || $description || $headerActions)
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                @if($title)
                    <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-[#005BAC] rounded-full inline-block"></span>
                        {{ $title }}
                    </h3>
                @endif
                @if($description)
                    <p class="text-xs text-slate-500 mt-0.5 ml-3.5">{{ $description }}</p>
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
