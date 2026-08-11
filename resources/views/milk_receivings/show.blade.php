<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Intake Record Details') }}" description="{{ __('Detailed verification, expected vs actual metrics, and farmer breakdown for this received batch.') }}">
            <x-slot name="actions">
                <div class="flex gap-2">
                    @can('update', $milkReceiving)
                        <a href="{{ route('milk-receivings.edit', $milkReceiving) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-yellow-200 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            {{ __('Edit Intake') }}
                        </a>
                    @endcan
                    <a href="{{ route('milk-receivings.index') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-sm transition duration-150 ease-in-out">
                        {{ __('Back to List') }}
                    </a>
                </div>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="space-y-8">
        <!-- Batch Overview & Variance Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Card: Status & Metadata -->
            <x-admin.card title="{{ __('Intake Verification Summary') }}">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('Status') }}</span>
                        @if($milkReceiving->status === 'received')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">
                                {{ __('Received (Verified)') }}
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-100 uppercase tracking-wide">
                                {{ __('Discrepancy Detected') }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-1">
                        <div>
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Village') }}</span>
                            <span class="text-xs font-semibold text-slate-800 mt-0.5 block">{{ $milkReceiving->village->name }}</span>
                            <span class="text-3xs text-slate-400 font-medium block">({{ $milkReceiving->village->code }})</span>
                        </div>
                        <div>
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Shift') }}</span>
                            <span class="text-xs font-semibold text-slate-800 mt-0.5 block capitalize">{{ $milkReceiving->shift }}</span>
                        </div>
                        <div>
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Receiving Date') }}</span>
                            <span class="text-xs font-semibold text-slate-800 mt-0.5 block">{{ $milkReceiving->receiving_date->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Verified By') }}</span>
                            <span class="text-xs font-semibold text-slate-800 mt-0.5 block">{{ $milkReceiving->verifier->name }}</span>
                        </div>
                    </div>

                    @if($milkReceiving->notes)
                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block mb-1">{{ __('Verification Remarks') }}</span>
                            <p class="text-xs text-slate-650 bg-slate-50/70 p-3 rounded-xl border border-slate-100 whitespace-pre-line">
                                {{ $milkReceiving->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Right Card: Metric Breakdown -->
            <div class="lg:col-span-2 space-y-6">
                <x-admin.card title="{{ __('Quantity & Quality Comparison') }}" description="{{ __('Expected vs actual measurements for this center intake batch') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <!-- Expected Volume -->
                        <div class="p-4 bg-slate-50/60 border border-slate-100 rounded-xl">
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Expected Volume') }}</span>
                            <span class="text-lg font-bold text-slate-800 mt-1 block">{{ number_format($milkReceiving->expected_quantity, 2) }} L</span>
                        </div>

                        <!-- Actual Received Volume -->
                        <div class="p-4 bg-indigo-50/40 border border-indigo-100/60 rounded-xl">
                            <span class="text-xxs font-bold text-indigo-500 uppercase tracking-wider block">{{ __('Received Volume') }}</span>
                            <span class="text-lg font-bold text-indigo-900 mt-1 block">{{ number_format($milkReceiving->received_quantity, 2) }} L</span>
                        </div>

                        <!-- Variance -->
                        @php
                            $variance = $milkReceiving->quantity_variance;
                            $variancePercent = $milkReceiving->quantity_variance_percent;
                        @endphp
                        <div class="p-4 {{ $variance > 0.1 ? 'bg-emerald-50/50 border-emerald-100' : ($variance < -0.1 ? 'bg-rose-50/50 border-rose-100' : 'bg-slate-50/60 border-slate-100') }} border rounded-xl">
                            <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Quantity Variance') }}</span>
                            @if($variance > 0.1)
                                <span class="text-lg font-bold text-emerald-600 mt-1 block">+{{ number_format($variance, 2) }} L (+{{ number_format($variancePercent, 1) }}%)</span>
                            @elseif($variance < -0.1)
                                <span class="text-lg font-bold text-rose-600 mt-1 block">{{ number_format($variance, 2) }} L ({{ number_format($variancePercent, 1) }}%)</span>
                            @else
                                <span class="text-lg font-bold text-slate-500 mt-1 block">0.00 L</span>
                            @endif
                        </div>
                    </div>

                    <!-- Quality metrics table -->
                    <div class="border-t border-slate-100 pt-4 grid grid-cols-2 gap-6">
                        <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-xl">
                            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Fat Percentage (%)') }}</h5>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-450">{{ __('Expected:') }}</span>
                                <span class="font-semibold text-slate-700">{{ $milkReceiving->expected_fat !== null ? number_format($milkReceiving->expected_fat, 2) . '%' : '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs mt-1">
                                <span class="text-slate-450">{{ __('Tested:') }}</span>
                                <span class="font-bold text-slate-800">{{ $milkReceiving->received_fat !== null ? number_format($milkReceiving->received_fat, 2) . '%' : '-' }}</span>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-xl">
                            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('SNF Percentage (%)') }}</h5>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-450">{{ __('Expected:') }}</span>
                                <span class="font-semibold text-slate-700">{{ $milkReceiving->expected_snf !== null ? number_format($milkReceiving->expected_snf, 2) . '%' : '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs mt-1">
                                <span class="text-slate-450">{{ __('Tested:') }}</span>
                                <span class="font-bold text-slate-800">{{ $milkReceiving->received_snf !== null ? number_format($milkReceiving->received_snf, 2) . '%' : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Traceability: Associated Farmer Collections -->
        <x-admin.card title="{{ __('Traceability - Farmer Collections') }}" description="{{ __('Individual collections recorded in ') . $milkReceiving->village->name . __(' for this shift') }}">
            @if($farmerCollections->isEmpty())
                <div class="text-center py-8 text-slate-400 text-xs">
                    {{ __('No individual farmer collection records found for this shift.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Farmer') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Code') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Quantity (L)') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Fat %') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('SNF %') }}</th>
                                @can('view-financials')
                                    <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Amount (₹)') }}</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($farmerCollections as $col)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="pl-6 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-800">
                                        {{ $col->farmer->name }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-500 font-mono">
                                        {{ $col->farmer->farmer_code }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-800">
                                        {{ number_format($col->milk_quantity, 2) }} L
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                        {{ $col->fat ? number_format($col->fat, 2) . '%' : '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                        {{ $col->snf ? number_format($col->snf, 2) . '%' : '-' }}
                                    </td>
                                    @can('view-financials')
                                        <td class="pr-6 py-3.5 whitespace-nowrap text-right text-xs font-bold text-indigo-600">
                                            ₹ {{ number_format($col->amount, 2) }}
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-admin.card>
    </div>
</x-admin-layout>
