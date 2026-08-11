<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Milk Stock Management') }}" description="{{ __('Real-time raw milk inventory ledger, intake tracking, and stock issuance.') }}">
            <x-slot name="actions">
                @can('createOut', App\Models\MilkStock::class)
                    <button type="button" @click="stockOutModalOpen = true" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m8-8v16"></path>
                        </svg>
                        {{ __('Record Stock OUT') }}
                    </button>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ stockOutModalOpen: false, availableStock: {{ $availableStock }} }" class="space-y-8">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-xl shadow-sm flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-medium">{{ session('success') }}</span>
                </div>
                <button class="text-emerald-500 hover:text-emerald-700" onclick="this.parentElement.remove();">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-xl shadow-sm" role="alert">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-bold">{{ __('Action Failed') }}</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-7">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Opening Stock -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Opening Stock') }}</span>
                    <span class="text-xl font-bold text-slate-800 mt-1 block">{{ number_format($openingStock, 2) }} <span class="text-xs text-slate-400 font-normal">L</span></span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Today Received (Stock IN) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Today Received (IN)') }}</span>
                    <span class="text-xl font-bold text-emerald-600 mt-1 block">+{{ number_format($todayReceived, 2) }} <span class="text-xs text-emerald-400 font-normal">L</span></span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                </div>
            </div>

            <!-- Today Stock OUT -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Today Issued (OUT)') }}</span>
                    <span class="text-xl font-bold text-rose-600 mt-1 block">-{{ number_format($todayStockOut, 2) }} <span class="text-xs text-rose-400 font-normal">L</span></span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                </div>
            </div>

            <!-- Current Available Stock -->
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 text-white p-5 rounded-2xl shadow-md shadow-indigo-200 flex items-center justify-between">
                <div>
                    <span class="text-xxs font-bold text-indigo-200 uppercase tracking-wider block">{{ __('Current Available Stock') }}</span>
                    <span class="text-2xl font-extrabold text-white mt-1 block">{{ number_format($availableStock, 2) }} <span class="text-xs text-indigo-200 font-normal">L</span></span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center backdrop-blur-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <x-admin.card title="{{ __('Filter Stock Ledger') }}">
            <form method="GET" action="{{ route('milk-stocks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="date" :value="__('Transaction Date')" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full text-xs" :value="$filterDate" />
                </div>

                <div>
                    <x-input-label for="type" :value="__('Transaction Type')" />
                    <select id="type" name="type" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Types (IN & OUT)') }}</option>
                        <option value="in" {{ $filterType === 'in' ? 'selected' : '' }}>{{ __('Stock IN (+)') }}</option>
                        <option value="out" {{ $filterType === 'out' ? 'selected' : '' }}>{{ __('Stock OUT (-)') }}</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="search" :value="__('Search Reason / Reference')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full text-xs" :value="$search" placeholder="{{ __('Search by village, shop, reason...') }}" />
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="w-full py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition">
                        {{ __('Filter') }}
                    </button>
                    @if($filterDate || $filterType || $search)
                        <a href="{{ route('milk-stocks.index') }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Stock Transaction Ledger Table -->
        <x-admin.card title="{{ __('Stock Ledger History') }}" description="{{ __('Comprehensive log of raw milk intake and issuance transactions.') }}">
            @if($transactions->isEmpty())
                <div class="text-center py-10 text-slate-400 text-xs">
                    {{ __('No stock transactions found for the selected criteria.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Quantity (L)') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Fat / SNF %') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Source / Reason') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Reference') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Logged By') }}</th>
                                <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Running Balance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($transactions as $txn)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="pl-6 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-800">
                                        {{ $txn->transaction_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs">
                                        @if($txn->type === 'in')
                                            <span class="px-2.5 py-1 inline-flex text-xxs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wider">
                                                {{ __('Stock IN') }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-xxs font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-100 uppercase tracking-wider">
                                                {{ __('Stock OUT') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs font-bold">
                                        @if($txn->type === 'in')
                                            <span class="text-emerald-600">+{{ number_format($txn->quantity, 2) }} L</span>
                                        @else
                                            <span class="text-rose-600">-{{ number_format($txn->quantity, 2) }} L</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-500">
                                        @if($txn->fat || $txn->snf)
                                            <span class="font-medium text-slate-700">{{ $txn->fat ? number_format($txn->fat, 1) . '%' : '-' }}</span> / 
                                            <span class="font-medium text-slate-700">{{ $txn->snf ? number_format($txn->snf, 1) . '%' : '-' }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-700 max-w-xs truncate">
                                        {{ $txn->source_or_reason }}
                                        @if($txn->notes)
                                            <span class="block text-3xs text-slate-400 italic truncate">{{ $txn->notes }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs">
                                        @if($txn->milk_receiving_id)
                                            <a href="{{ route('milk-receivings.show', $txn->milk_receiving_id) }}" class="inline-flex items-center gap-1 text-xxs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded-md transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                {{ __('Receiving #') }}{{ $txn->milk_receiving_id }}
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-xxs uppercase tracking-wide">{{ __('Manual / Direct') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600 font-medium">
                                        {{ $txn->creator->name ?? __('System') }}
                                    </td>
                                    <td class="pr-6 py-3.5 whitespace-nowrap text-right text-xs font-bold text-slate-800 font-mono">
                                        {{ number_format($balanceMap[$txn->id] ?? 0, 2) }} L
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 border-t border-slate-100 pt-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Record Stock OUT Modal -->
        @can('createOut', App\Models\MilkStock::class)
            <div x-show="stockOutModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="stockOutModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 transition-opacity" 
                         aria-hidden="true"
                         @click="stockOutModalOpen = false">
                        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div x-show="stockOutModalOpen"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-slate-200/80">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-800">{{ __('Record Stock OUT') }}</h3>
                                    <p class="text-xxs text-slate-400">{{ __('Issue raw milk from available center stock') }}</p>
                                </div>
                            </div>
                            <button type="button" @click="stockOutModalOpen = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>

                        <!-- Available Stock Banner -->
                        <div class="mb-4 p-3 bg-indigo-50/60 border border-indigo-100 rounded-xl flex justify-between items-center text-xs">
                            <span class="font-medium text-indigo-700">{{ __('Available Stock Limit:') }}</span>
                            <span class="font-bold text-indigo-900">{{ number_format($availableStock, 2) }} Liters</span>
                        </div>

                        <form method="POST" action="{{ route('milk-stocks.store-out') }}" class="space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="transaction_date" :value="__('Transaction Date')" />
                                <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1 block w-full text-xs" :value="old('transaction_date', date('Y-m-d'))" required />
                            </div>

                            <div>
                                <x-input-label for="quantity" :value="__('Stock OUT Quantity (Liters)')" />
                                <x-text-input id="quantity" name="quantity" type="number" step="0.01" min="0.01" max="{{ $availableStock }}" class="mt-1 block w-full text-xs" :value="old('quantity')" placeholder="{{ __('e.g. 150.00') }}" required />
                            </div>

                            <div>
                                <x-input-label for="source_or_reason" :value="__('Issuance Reason / Destination')" />
                                <x-text-input id="source_or_reason" name="source_or_reason" type="text" class="mt-1 block w-full text-xs" :value="old('source_or_reason')" placeholder="{{ __('e.g. Shop Order #104 / Dairy Processing / Spoilage Dump') }}" required />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="fat" :value="__('Fat % (Optional)')" />
                                    <x-text-input id="fat" name="fat" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full text-xs" :value="old('fat')" placeholder="e.g. 4.2" />
                                </div>
                                <div>
                                    <x-input-label for="snf" :value="__('SNF % (Optional)')" />
                                    <x-text-input id="snf" name="snf" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full text-xs" :value="old('snf')" placeholder="e.g. 8.5" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notes / Remarks (Optional)')" />
                                <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" placeholder="{{ __('Additional context or batch dispatch info...') }}">{{ old('notes') }}</textarea>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                                <button type="button" @click="stockOutModalOpen = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition">
                                    {{ __('Cancel') }}
                                </button>
                                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-rose-200 transition">
                                    {{ __('Issue Stock OUT') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</x-admin-layout>
