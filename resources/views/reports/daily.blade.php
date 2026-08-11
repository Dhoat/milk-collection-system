<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Daily Operations Report') }}" description="{{ __('Comprehensive daily performance summary across procurement, inventory, retail sales, and dispatches.') }}">
            <x-slot name="actions">
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.monthly') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ __('Switch to Monthly Report') }}
                    </a>
                </div>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="space-y-6">
        <!-- Date & Filters Card -->
        <x-admin.card title="{{ __('Report Parameters & Filters') }}">
            <form method="GET" action="{{ route('reports.daily') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <x-input-label for="date" :value="__('Select Date')" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full text-xs font-mono" :value="$date" required />
                </div>

                <div>
                    <x-input-label for="village_id" :value="__('Filter Village')" />
                    <select id="village_id" name="village_id" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Villages') }}</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}" {{ request('village_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="shop_id" :value="__('Filter Shop Outlet')" />
                    <select id="shop_id" name="shop_id" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Shops') }}</option>
                        @foreach($shops as $s)
                            <option value="{{ $s->id }}" {{ request('shop_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="product_id" :value="__('Filter Product')" />
                    <select id="product_id" name="product_id" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Products') }}</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm">
                        {{ __('Generate Report') }}
                    </button>
                    @if(request()->hasAny(['village_id', 'shop_id', 'product_id']))
                        <a href="{{ route('reports.daily', ['date' => $date]) }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- KPI Summary Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Milk Collected -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                    <span>{{ __('Milk Collected') }}</span>
                    <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.182.234l-.454.273L3 17.5V20a2 2 0 002 2h14a2 2 0 002-2v-2.5l-1.572-2.072z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-extrabold text-slate-900">{{ number_format($reportData['collection']['total_litres'], 2) }}</span>
                    <span class="text-xs text-slate-500 font-medium">Ltrs</span>
                </div>
                <div class="text-xxs text-slate-400">
                    From <strong class="text-slate-700">{{ $reportData['collection']['farmers_count'] }}</strong> farmers (Avg {{ number_format($reportData['collection']['avg_per_farmer'], 2) }} L/farmer)
                </div>
            </div>

            <!-- Total Main Center Received -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                    <span>{{ __('Main Center Received') }}</span>
                    <span class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-extrabold text-slate-900">{{ number_format($reportData['center']['total_received'], 2) }}</span>
                    <span class="text-xs text-slate-500 font-medium">Ltrs</span>
                </div>
                <div class="text-xxs text-slate-400">
                    Variance: <span class="font-mono font-bold {{ $reportData['center']['diff_litres'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $reportData['center']['diff_litres'] >= 0 ? '+' : '' }}{{ number_format($reportData['center']['diff_litres'], 2) }} L</span>
                </div>
            </div>

            <!-- Total Order Sales Value -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                    <span>{{ __('Retail Orders Sales') }}</span>
                    <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-extrabold text-indigo-600">₹{{ number_format($reportData['orders']['total_value'], 2) }}</span>
                </div>
                <div class="text-xxs text-slate-400">
                    Across <strong class="text-slate-700">{{ $reportData['orders']['total_count'] }}</strong> shop orders placed
                </div>
            </div>

            <!-- Net Operational Balance -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                    <span>{{ __('Operational Balance') }}</span>
                    <span class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-extrabold {{ $reportData['financial']['net_balance'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        ₹{{ number_format($reportData['financial']['net_balance'], 2) }}
                    </span>
                </div>
                <div class="text-xxs text-slate-400">
                    Sales (₹{{ number_format($reportData['financial']['total_sales'], 0) }}) - Milk Cost (₹{{ number_format($reportData['financial']['milk_expense'], 0) }})
                </div>
            </div>
        </div>

        <!-- Section 1 & 2: Milk Collection & Stock Ledger -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Village-wise Collection Table -->
            <x-admin.card title="{{ __('Village Milk Collection Breakdown') }}" description="{{ __('Milk volume, farmer turnout, and quality metrics.') }}">
                @if($reportData['collection']['village_breakdown']->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs">
                        {{ __('No milk collections recorded on this date.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Village') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Farmers') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Litres') }}</th>
                                    <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($reportData['collection']['village_breakdown'] as $row)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-6 py-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                            {{ $row->village_name ?? __('Unknown') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-600 font-mono">
                                            {{ $row->farmers_count }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs font-mono font-bold text-slate-900">
                                            {{ number_format($row->total_litres, 2) }} L
                                        </td>
                                        <td class="pr-6 py-3 whitespace-nowrap text-right text-xs font-mono font-bold text-indigo-600">
                                            ₹{{ number_format($row->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between text-xxs text-slate-500 font-medium">
                    <span>Avg FAT: <strong class="text-slate-800 font-mono">{{ $reportData['collection']['avg_fat'] }}%</strong></span>
                    <span>Avg SNF: <strong class="text-slate-800 font-mono">{{ $reportData['collection']['avg_snf'] }}%</strong></span>
                </div>
            </x-admin.card>

            <!-- Milk Stock Ledger Balance -->
            <x-admin.card title="{{ __('Milk Stock Ledger Balance') }}" description="{{ __('Raw milk opening, transactions, and closing inventory.') }}">
                <div class="grid grid-cols-2 gap-4 my-2">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xxs font-semibold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Opening Stock') }}</span>
                        <span class="text-xl font-mono font-bold text-slate-800">{{ number_format($reportData['stock']['opening'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="text-xxs font-semibold uppercase tracking-wider text-emerald-600 block mb-1">{{ __('Stock IN (Receivings)') }}</span>
                        <span class="text-xl font-mono font-bold text-emerald-700">+{{ number_format($reportData['stock']['in'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-rose-50 rounded-xl border border-rose-100">
                        <span class="text-xxs font-semibold uppercase tracking-wider text-rose-600 block mb-1">{{ __('Stock OUT (Orders/Sales)') }}</span>
                        <span class="text-xl font-mono font-bold text-rose-700">-{{ number_format($reportData['stock']['out'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <span class="text-xxs font-semibold uppercase tracking-wider text-indigo-600 block mb-1">{{ __('Closing Stock') }}</span>
                        <span class="text-xl font-mono font-bold text-indigo-900">{{ number_format($reportData['stock']['closing'], 2) }} L</span>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl text-xxs text-slate-500 space-y-1">
                    <div class="font-bold text-slate-700">{{ __('Receiving Reconciliation:') }}</div>
                    <div>Main Milk Center Records: <strong class="text-slate-800">{{ $reportData['center']['records_count'] }}</strong> receiving entries.</div>
                    <div>Total Milk Received: <strong class="text-slate-800 font-mono">{{ number_format($reportData['center']['total_received'], 2) }} L</strong></div>
                </div>
            </x-admin.card>
        </div>

        <!-- Section 3 & 4: Orders & Deliveries & Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Shop Orders & Deliveries Status Matrix -->
            <x-admin.card title="{{ __('Orders & Delivery Status Matrix') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Orders Breakdown -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-1.5">{{ __('Shop Orders') }}</h4>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">{{ __('Pending') }}</span>
                                <span class="font-mono font-bold text-amber-600">{{ $reportData['orders']['by_status']['pending'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">{{ __('Confirmed') }}</span>
                                <span class="font-mono font-bold text-blue-600">{{ $reportData['orders']['by_status']['confirmed'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">{{ __('Preparing / Dispatched') }}</span>
                                <span class="font-mono font-bold text-purple-600">{{ $reportData['orders']['by_status']['preparing'] + $reportData['orders']['by_status']['dispatched'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">{{ __('Delivered') }}</span>
                                <span class="font-mono font-bold text-emerald-600">{{ $reportData['orders']['by_status']['delivered'] }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-500">{{ __('Cancelled') }}</span>
                                <span class="font-mono font-bold text-rose-600">{{ $reportData['orders']['by_status']['cancelled'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Deliveries Breakdown -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-1.5">{{ __('Vehicle Dispatches') }}</h4>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">{{ __('Pending Dispatch') }}</span>
                                <span class="font-mono font-bold text-amber-600">{{ $reportData['deliveries']['by_status']['pending'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">{{ __('Assigned Staff') }}</span>
                                <span class="font-mono font-bold text-blue-600">{{ $reportData['deliveries']['by_status']['assigned'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">{{ __('Out for Delivery') }}</span>
                                <span class="font-mono font-bold text-purple-600">{{ $reportData['deliveries']['by_status']['out_for_delivery'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">{{ __('Delivered Receipts') }}</span>
                                <span class="font-mono font-bold text-emerald-600">{{ $reportData['deliveries']['by_status']['delivered'] }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-500">{{ __('Failed / Cancelled') }}</span>
                                <span class="font-mono font-bold text-rose-600">{{ $reportData['deliveries']['by_status']['failed'] + $reportData['deliveries']['by_status']['cancelled'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Best Selling Products Table -->
            <x-admin.card title="{{ __('Daily Dairy Product Sales') }}" description="{{ __('Products sold and sales value for the selected date.') }}">
                @if($reportData['products']['items']->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs">
                        {{ __('No products sold on this date.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Product') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Qty Sold') }}</th>
                                    <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Total Sales') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($reportData['products']['items'] as $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-6 py-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                            {{ $item->product_name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-slate-700">
                                            {{ number_format($item->total_qty, 2) }} {{ $item->unit }}
                                        </td>
                                        <td class="pr-6 py-3 whitespace-nowrap text-right text-xs font-mono font-bold text-indigo-600">
                                            ₹{{ number_format($item->total_sales, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
