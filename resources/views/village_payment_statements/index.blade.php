<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ __('Village Payment Statement') }}" 
            description="{{ __('Village-wise milk receiving summary, rate calculations, and ledger statement') }}">
            <x-slot name="actions">
                @if($statement && count($statement['entries']) > 0)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('village-payment-statement.print', ['village_id' => $villageId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            {{ __('Print Statement') }}
                        </a>
                        <a href="{{ route('village-payment-statement.pdf', ['village_id' => $villageId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('Download PDF') }}
                        </a>
                    </div>
                @endif
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Filter Card Header -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 lg:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Filter Criteria') }}</h3>
                    <p class="text-xs text-slate-500 font-medium">{{ __('Select village and inclusive date range to generate statement') }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('village-payment-statement.index') }}" x-data="{ loading: false }" @submit="loading = true" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Village Dropdown -->
                    <div>
                        <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            {{ __('Village') }} <span class="text-rose-500">*</span>
                        </label>
                        <select id="village_id" name="village_id" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" required>
                            <option value="" disabled {{ empty($villageId) ? 'selected' : '' }}>{{ __('Select Village...') }}</option>
                            @foreach($villages as $village)
                                <option value="{{ $village->id }}" {{ (string)$villageId === (string)$village->id ? 'selected' : '' }}>
                                    {{ $village->name }} ({{ $village->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-1.5" :messages="$errors->get('village_id')" />
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            {{ __('Start Date') }} <span class="text-rose-500">*</span>
                        </label>
                        <x-text-input id="start_date" name="start_date" type="date" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('start_date', $startDate ?? date('Y-m-01'))" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('start_date')" />
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            {{ __('End Date') }} <span class="text-rose-500">*</span>
                        </label>
                        <x-text-input id="end_date" name="end_date" type="date" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('end_date', $endDate ?? date('Y-m-d'))" required />
                        <x-input-error class="mt-1.5" :messages="$errors->get('end_date')" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" :disabled="loading" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all disabled:opacity-50">
                        <template x-if="!loading">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </template>
                        <template x-if="loading">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span>{{ __('Generate Statement') }}</span>
                    </button>
                    <a href="{{ route('village-payment-statement.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        @if($statement)
            <!-- Statement Results Container -->
            <div class="space-y-6">
                <!-- Summary Metrics Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Total Milk -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">{{ __('Total Milk Quantity') }}</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-black text-slate-900">{{ number_format($statement['summary']['total_milk'], 2) }}</span>
                            <span class="text-xs font-bold text-slate-500">L</span>
                        </div>
                    </div>

                    <!-- Average Fat -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">{{ __('Average Fat') }}</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-black text-slate-900">{{ number_format($statement['summary']['average_fat'], 2) }}</span>
                            <span class="text-xs font-bold text-slate-500">%</span>
                        </div>
                    </div>

                    <!-- Average SNF -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">{{ __('Average SNF') }}</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-black text-slate-900">{{ number_format($statement['summary']['average_snf'], 2) }}</span>
                            <span class="text-xs font-bold text-slate-500">%</span>
                        </div>
                    </div>

                    <!-- Total Payment -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
                        <span class="text-[11px] font-extrabold text-[#005BAC] uppercase tracking-wider block mb-1">{{ __('Total Payment') }}</span>
                        <span class="text-2xl font-black text-[#005BAC]">₹ {{ number_format($statement['summary']['total_payment'], 2) }}</span>
                    </div>

                    <!-- Closing Balance -->
                    <div class="bg-blue-900 border border-blue-800 text-white rounded-2xl p-5 shadow-xs">
                        <span class="text-[11px] font-extrabold text-sky-200 uppercase tracking-wider block mb-1">{{ __('Closing Balance') }}</span>
                        <span class="text-2xl font-black text-white">₹ {{ number_format($statement['summary']['closing_balance'], 2) }}</span>
                    </div>
                </div>

                <!-- Ledger Table Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">
                                {{ __('Village Statement:') }} <span class="text-[#005BAC]">{{ $statement['village']['name'] }}</span>
                            </h3>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                {{ __('Period:') }} <span class="font-mono text-slate-700 font-bold">{{ $statement['period']['start_date_formatted'] }}</span> {{ __('to') }} <span class="font-mono text-slate-700 font-bold">{{ $statement['period']['end_date_formatted'] }}</span>
                            </p>
                        </div>
                    </div>

                    @if(count($statement['entries']) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider">
                                        <th scope="col" class="py-3.5 px-4 font-mono">{{ __('Date') }}</th>
                                        <th scope="col" class="py-3.5 px-4">{{ __('Particular') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-right">{{ __('Milk Qty (L)') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-right">{{ __('Fat (%)') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-right">{{ __('SNF (%)') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-right">{{ __('Price (₹)') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-right">{{ __('Payment (₹)') }}</th>
                                        <th scope="col" class="py-3.5 px-4 text-right">{{ __('Balance (₹)') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                                    @foreach($statement['entries'] as $entry)
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                                                {{ $entry['date_formatted'] }}
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 font-bold rounded-lg text-[11px]">
                                                    {{ $entry['particular'] }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                                {{ number_format($entry['milk_quantity'], 2) }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono text-slate-600">
                                                {{ number_format($entry['fat'], 2) }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono text-slate-600">
                                                {{ number_format($entry['snf'], 2) }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono text-slate-700">
                                                ₹ {{ number_format($entry['price'], 2) }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-bold text-[#005BAC]">
                                                ₹ {{ number_format($entry['payment'], 2) }}
                                            </td>
                                            <td class="py-3.5 px-4 text-right font-mono font-black text-slate-900">
                                                ₹ {{ number_format($entry['running_balance'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-100/80 font-black text-slate-900 border-t-2 border-slate-300">
                                        <td colspan="2" class="py-4 px-4 uppercase tracking-wider text-slate-700">
                                            {{ __('Total / Summary') }}
                                        </td>
                                        <td class="py-4 px-4 text-right font-mono text-sm text-slate-900">
                                            {{ number_format($statement['summary']['total_milk'], 2) }} L
                                        </td>
                                        <td class="py-4 px-4 text-right font-mono text-slate-700">
                                            {{ number_format($statement['summary']['average_fat'], 2) }}%
                                        </td>
                                        <td class="py-4 px-4 text-right font-mono text-slate-700">
                                            {{ number_format($statement['summary']['average_snf'], 2) }}%
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            -
                                        </td>
                                        <td class="py-4 px-4 text-right font-mono text-sm text-[#005BAC]">
                                            ₹ {{ number_format($statement['summary']['total_payment'], 2) }}
                                        </td>
                                        <td class="py-4 px-4 text-right font-mono text-sm text-slate-900">
                                            ₹ {{ number_format($statement['summary']['closing_balance'], 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="p-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center mx-auto shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">{{ __('No Records Found') }}</h4>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">
                                {{ __('No milk receiving records found for the selected village and date range.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
