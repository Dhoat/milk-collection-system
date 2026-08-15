<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Milk Stock Inventory') }}" description="{{ __('Real-time raw milk inventory ledger, intake tracking, and stock issuance.') }}">
            <x-slot name="actions">
                @can('createOut', App\Models\MilkStock::class)
                    <button type="button" @click="stockOutModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4m8-8v16"></path>
                        </svg>
                        {{ __('Record Stock OUT') }}
                    </button>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ stockOutModalOpen: false, availableStock: {{ $availableStock }} }" class="space-y-6">
        <!-- Flash & Error Alert Messages -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-xl shadow-sm flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-bold">{{ session('success') }}</span>
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Opening Stock -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Opening Stock') }}</span>
                    <span class="text-2xl font-black text-slate-800 mt-1 block">{{ number_format($openingStock, 2) }} <span class="text-xs text-slate-400 font-bold">L</span></span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Today Received (Stock IN) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Today Received (IN)') }}</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">+{{ number_format($todayReceived, 2) }} <span class="text-xs text-emerald-500 font-bold">L</span></span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                </div>
            </div>

            <!-- Today Stock OUT -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Today Issued (OUT)') }}</span>
                    <span class="text-2xl font-black text-rose-600 mt-1 block">-{{ number_format($todayStockOut, 2) }} <span class="text-xs text-rose-500 font-bold">L</span></span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                </div>
            </div>

            <!-- Current Available Stock -->
            <div class="bg-gradient-to-br from-[#005BAC] to-[#003B73] text-white p-5 rounded-2xl shadow-md flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-blue-200 uppercase tracking-wider block">{{ __('Current Available Stock') }}</span>
                    <span class="text-2xl font-black text-white mt-1 block">{{ number_format($availableStock, 2) }} <span class="text-xs text-blue-200 font-bold">L</span></span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-white/10 text-white flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar Card -->
        <x-admin.card title="{{ __('Filter Stock Ledger') }}">
            <form method="GET" action="{{ route('milk-stocks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Transaction Date') }}</label>
                    <input id="date" name="date" type="date" value="{{ $filterDate }}" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                </div>

                <div>
                    <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Transaction Type') }}</label>
                    <select id="type" name="type" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Types (IN & OUT)') }}</option>
                        <option value="in" {{ $filterType === 'in' ? 'selected' : '' }}>{{ __('Stock IN (+)') }}</option>
                        <option value="out" {{ $filterType === 'out' ? 'selected' : '' }}>{{ __('Stock OUT (-)') }}</option>
                    </select>
                </div>

                <div>
                    <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Search Reason / Reference') }}</label>
                    <input id="search" name="search" type="text" value="{{ $search }}" placeholder="Search village, shop, reason..." class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                </div>

                <div class="flex gap-2">
                    <x-primary-button type="submit" class="w-full justify-center">
                        {{ __('Filter') }}
                    </x-primary-button>
                    @if($filterDate || $filterType || $search)
                        <a href="{{ route('milk-stocks.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Stock Transaction Ledger Table -->
        <x-admin.card title="{{ __('Stock Ledger History') }}" description="{{ __('Comprehensive log of raw milk intake and issuance transactions.') }}">
            @if($transactions->isEmpty())
                <div class="text-center py-12 text-slate-400 text-xs font-semibold">
                    {{ __('No stock transactions found for the selected criteria.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-200/80">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Quantity (L)') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Fat / SNF %') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Source / Reason') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Reference') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Logged By') }}</th>
                                <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Running Balance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($transactions as $txn)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap text-xs font-bold text-slate-800">
                                        {{ $txn->transaction_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs">
                                        @if($txn->type === 'in')
                                            <span class="px-2.5 py-1 inline-flex text-[10px] font-extrabold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                                ↓ Stock IN
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-[10px] font-extrabold rounded-full bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider">
                                                ↑ Stock OUT
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-extrabold">
                                        @if($txn->type === 'in')
                                            <span class="text-emerald-600">+{{ number_format($txn->quantity, 2) }} L</span>
                                        @else
                                            <span class="text-rose-600">-{{ number_format($txn->quantity, 2) }} L</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-600 font-semibold">
                                        @if($txn->fat || $txn->snf)
                                            <span class="font-bold text-slate-800">{{ $txn->fat ? number_format($txn->fat, 1) . '%' : '-' }}</span> / 
                                            <span class="font-bold text-slate-800">{{ $txn->snf ? number_format($txn->snf, 1) . '%' : '-' }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-xs font-semibold text-slate-700 max-w-xs truncate">
                                        {{ $txn->source_or_reason }}
                                        @if($txn->notes)
                                            <span class="block text-[11px] text-slate-400 italic truncate">{{ $txn->notes }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs">
                                        @if($txn->milk_receiving_id)
                                            <a href="{{ route('milk-receivings.show', $txn->milk_receiving_id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#005BAC] bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                {{ __('Receiving #') }}{{ $txn->milk_receiving_id }}
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-[10px] uppercase tracking-wider font-bold">{{ __('Manual / Direct') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-600 font-semibold">
                                        {{ $txn->creator->name ?? __('System') }}
                                    </td>
                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-black text-slate-900 font-mono">
                                        {{ number_format($balanceMap[$txn->id] ?? 0, 2) }} L
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Record Stock OUT Modal -->
        @can('createOut', App\Models\MilkStock::class)
            <div x-show="stockOutModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <div x-show="stockOutModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                         @click="stockOutModalOpen = false">
                    </div>

                    <div x-show="stockOutModalOpen"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="relative inline-block bg-white rounded-2xl p-6 text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900">{{ __('Record Stock OUT') }}</h3>
                                    <p class="text-xs text-slate-500">{{ __('Issue raw milk from available center stock') }}</p>
                                </div>
                            </div>
                            <button type="button" @click="stockOutModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                        </div>

                        <!-- Available Stock Banner -->
                        <div class="mb-5 p-3.5 bg-blue-50/80 border border-blue-100 rounded-xl flex justify-between items-center text-xs">
                            <span class="font-bold text-[#005BAC]">{{ __('Available Stock Limit:') }}</span>
                            <span class="font-black text-[#005BAC] text-sm">{{ number_format($availableStock, 2) }} Liters</span>
                        </div>

                        <form method="POST" action="{{ route('milk-stocks.store-out') }}" class="space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="transaction_date" :value="__('Transaction Date')" class="font-bold text-slate-700" />
                                <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1.5 block w-full" :value="old('transaction_date', date('Y-m-d'))" required />
                            </div>

                            <div>
                                <x-input-label for="quantity" :value="__('Stock OUT Quantity (Liters)')" class="font-bold text-slate-700" />
                                <x-text-input id="quantity" name="quantity" type="number" step="0.01" min="0.01" max="{{ $availableStock }}" class="mt-1.5 block w-full text-base font-bold text-slate-900" :value="old('quantity')" placeholder="e.g. 150.00" required />
                            </div>

                            <div>
                                <x-input-label for="source_or_reason" :value="__('Issuance Reason / Destination')" class="font-bold text-slate-700" />
                                <x-text-input id="source_or_reason" name="source_or_reason" type="text" class="mt-1.5 block w-full" :value="old('source_or_reason')" placeholder="e.g. Shop Order #104 / Dairy Processing" required />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="fat" :value="__('Fat % (Optional)')" class="font-bold text-slate-700" />
                                    <x-text-input id="fat" name="fat" type="number" step="0.01" min="0" max="100" class="mt-1.5 block w-full" :value="old('fat')" placeholder="e.g. 4.2" />
                                </div>
                                <div>
                                    <x-input-label for="snf" :value="__('SNF % (Optional)')" class="font-bold text-slate-700" />
                                    <x-text-input id="snf" name="snf" type="number" step="0.01" min="0" max="100" class="mt-1.5 block w-full" :value="old('snf')" placeholder="e.g. 8.5" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notes / Remarks')" class="font-bold text-slate-700" />
                                <textarea id="notes" name="notes" rows="2" class="mt-1.5 block w-full border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-sm" placeholder="Additional context or batch dispatch info...">{{ old('notes') }}</textarea>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                <x-secondary-button @click="stockOutModalOpen = false">
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                                <x-danger-button type="submit">
                                    {{ __('Issue Stock OUT') }}
                                </x-danger-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</x-admin-layout>
