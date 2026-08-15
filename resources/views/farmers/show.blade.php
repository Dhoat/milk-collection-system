<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ $farmer->name }}" 
            description="{{ __('Registered Farmer Profile and History') }}">
            <x-slot name="actions">
                @can('update', $farmer)
                    <a href="{{ route('farmers.edit', $farmer) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('Edit Profile') }}
                    </a>
                @endcan
                <a href="{{ route('farmers.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                    ← {{ __('Back to Directory') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Overview Header Card -->
        <x-admin.card>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#005BAC] flex items-center justify-center font-extrabold text-xl border border-blue-100 shadow-sm shrink-0">
                        {{ substr($farmer->name, 0, 2) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-black text-slate-900">{{ $farmer->name }}</h2>
                            @if($farmer->status)
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ● {{ __('Active') }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                    ○ {{ __('Inactive') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs font-bold text-[#005BAC] mt-1 flex items-center gap-2">
                            <span>Code: <strong class="uppercase font-extrabold">{{ $farmer->farmer_code }}</strong></span>
                            <span>•</span>
                            <span>Village: <strong>{{ $farmer->village->name ?? '-' }} ({{ $farmer->village->code ?? '-' }})</strong></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Details Grid (Personal, Contact, Banking) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">
                <!-- Personal Info -->
                <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/60 space-y-3.5">
                    <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-200/80 pb-2">{{ __('Personal Info') }}</h4>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Father / Guardian') }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ $farmer->father_name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Gender') }}</span>
                        <span class="text-sm font-bold text-slate-800 capitalize">{{ $farmer->gender ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Registration Date') }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ $farmer->joining_date ? $farmer->joining_date->format('F j, Y') : '-' }}</span>
                    </div>
                </div>

                <!-- Contact & Address -->
                <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/60 space-y-3.5">
                    <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-200/80 pb-2">{{ __('Contact & Address') }}</h4>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Primary Mobile') }}</span>
                        <span class="text-sm font-bold text-[#005BAC]">{{ $farmer->mobile }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Alternate Phone') }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ $farmer->alternate_mobile ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Address') }}</span>
                        <span class="text-xs font-semibold text-slate-700 whitespace-pre-line block mt-1">{{ $farmer->address ?? '-' }}</span>
                    </div>
                </div>

                <!-- Banking Details -->
                <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/60 space-y-3.5">
                    <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-200/80 pb-2">{{ __('Bank / Payout Details') }}</h4>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Bank Name') }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ $farmer->bank_name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Account Number') }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ $farmer->account_number ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('IFSC Code') }}</span>
                        <span class="text-sm font-bold text-slate-800 uppercase tracking-wide">{{ $farmer->ifsc_code ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </x-admin.card>

        <!-- Collection & Financial Activity Feed Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-admin.card title="{{ __('Milk Collection Record') }}" description="{{ __('Recent daily milk deposits') }}">
                <div class="text-center py-8 text-slate-400">
                    <svg class="mx-auto h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <p class="text-xs font-bold text-slate-700">{{ __('Collection History Active') }}</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">{{ __('Access full daily milk deposits from the Milk Collections menu.') }}</p>
                </div>
            </x-admin.card>

            <x-admin.card title="{{ __('Payout Ledger') }}" description="{{ __('Payment status & statements') }}">
                <div class="text-center py-8 text-slate-400">
                    <svg class="mx-auto h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-xs font-bold text-slate-700">{{ __('Ledger System Ready') }}</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">{{ __('Direct bank settlements are calculated automatically based on FAT/SNF rates.') }}</p>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
