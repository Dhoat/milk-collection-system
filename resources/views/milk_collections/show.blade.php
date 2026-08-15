<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ __('Collection Record Summary') }}" 
            description="{{ $milkCollection->farmer->name }} • {{ $milkCollection->collection_date->format('d-m-Y') }}">
            <x-slot name="actions">
                @can('update', $milkCollection)
                    <a href="{{ route('milk-collections.edit', $milkCollection) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('Edit Record') }}
                    </a>
                @endcan
                <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                    ← {{ __('Back to List') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <x-admin.card>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-black text-slate-900">{{ $milkCollection->farmer->name }}</h3>
                    <p class="text-xs font-semibold text-[#005BAC] mt-0.5">
                        Farmer Code: <span class="font-extrabold uppercase">{{ $milkCollection->farmer->farmer_code }}</span> • Village: <span class="font-bold">{{ $milkCollection->farmer->village->name }}</span>
                    </p>
                </div>
                <div>
                    @if($milkCollection->shift === 'morning')
                        <span class="px-3 py-1.5 inline-flex items-center gap-1.5 text-xs font-extrabold rounded-xl bg-amber-50 text-amber-800 border border-amber-200 uppercase">
                            ☀️ Morning Shift
                        </span>
                    @else
                        <span class="px-3 py-1.5 inline-flex items-center gap-1.5 text-xs font-extrabold rounded-xl bg-blue-50 text-blue-800 border border-blue-200 uppercase">
                            🌙 Evening Shift
                        </span>
                    @endif
                </div>
            </div>

            <!-- Key Metric Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-6 p-5 bg-slate-50/80 rounded-2xl border border-slate-200/60">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Milk Quantity') }}</span>
                    <span class="text-2xl font-black text-[#005BAC] mt-1 block">
                        {{ number_format($milkCollection->milk_quantity, 2) }} L
                    </span>
                </div>
                @can('view-financials')
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Rate per Liter') }}</span>
                        <span class="text-2xl font-black text-slate-800 mt-1 block">
                            ₹{{ number_format($milkCollection->rate, 2) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Calculated Amount') }}</span>
                        <span class="text-2xl font-black text-emerald-600 mt-1 block">
                            ₹{{ number_format($milkCollection->amount, 2) }}
                        </span>
                    </div>
                @endcan
            </div>

            <!-- Quality & Session Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                <div class="space-y-4">
                    <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-2">{{ __('Session Meta') }}</h4>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">{{ __('Collection Date') }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ $milkCollection->collection_date->format('l, F j, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">{{ __('Recorded Timestamp') }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ $milkCollection->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-2">{{ __('Quality Indicators') }}</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-white border border-slate-200/80 rounded-xl">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">{{ __('Fat %') }}</span>
                            <span class="text-base font-extrabold text-slate-900">{{ $milkCollection->fat ? number_format($milkCollection->fat, 2) . '%' : '-' }}</span>
                        </div>
                        <div class="p-3 bg-white border border-slate-200/80 rounded-xl">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">{{ __('SNF %') }}</span>
                            <span class="text-base font-extrabold text-slate-900">{{ $milkCollection->snf ? number_format($milkCollection->snf, 2) . '%' : '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2 pt-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">{{ __('Quality Remarks / Staff Notes') }}</span>
                    <p class="text-sm font-medium text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-200/60 min-h-[60px] whitespace-pre-line">
                        {{ $milkCollection->notes ?? __('No notes recorded for this collection entry.') }}
                    </p>
                </div>
            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
