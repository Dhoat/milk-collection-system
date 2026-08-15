<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ $village->name }}" 
            description="{{ __('Village Sector Overview and Details') }}">
            <x-slot name="actions">
                @can('update', $village)
                    <a href="{{ route('villages.edit', $village) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('Edit Village') }}
                    </a>
                @endcan
                <a href="{{ route('villages.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition-all">
                    ← {{ __('Back to List') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Main Form Container Card (Full Screen / Full Width) -->
        <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs p-6 lg:p-8 space-y-8">
            <!-- Header Card Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-black text-xl shadow-2xs">
                        {{ strtoupper(substr($village->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $village->name }}</h2>
                            @if($village->status)
                                <span class="px-3 py-1 text-xs font-extrabold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    {{ __('Active') }}
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-extrabold rounded-full bg-slate-100 text-slate-600 border border-slate-200 inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    {{ __('Inactive') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs font-extrabold text-[#005BAC] mt-1">{{ __('Sector Code') }}: <span class="font-mono tracking-wider">{{ $village->code }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Details Grid Section -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Sector Specifications') }}</h3>
                        <p class="text-xs text-slate-400 font-medium">{{ __('Registration parameters and geographical details') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">{{ __('SECTOR CODE') }}</span>
                        <span class="text-sm font-mono font-extrabold text-slate-900">{{ $village->code }}</span>
                    </div>

                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">{{ __('COLLECTION STATUS') }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ $village->status ? __('Active for Operations') : __('Disabled') }}</span>
                    </div>

                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">{{ __('CREATED DATE') }}</span>
                        <span class="text-xs font-mono font-bold text-slate-800">{{ $village->created_at->format('F j, Y h:i A') }}</span>
                    </div>

                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">{{ __('LAST UPDATED') }}</span>
                        <span class="text-xs font-mono font-bold text-slate-800">{{ $village->updated_at->format('F j, Y h:i A') }}</span>
                    </div>
                </div>

                <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 space-y-2">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">{{ __('PHYSICAL ADDRESS & SECTOR DETAILS') }}</span>
                    <p class="text-xs text-slate-800 font-semibold whitespace-pre-line leading-relaxed">
                        {{ $village->address ?? __('No physical address specified for this sector.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
