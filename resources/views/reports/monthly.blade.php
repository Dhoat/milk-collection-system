<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Monthly Operations Report') }}" description="{{ __('Executive monthly performance digest for milk collection, inventory, shop sales, and product revenue.') }}">
            <x-slot name="actions">
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.daily') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ __('Switch to Daily Report') }}
                    </a>
                </div>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="space-y-6">
        <!-- Month / Year & Filters Card -->
        <x-admin.card title="{{ __('Monthly Parameters & Filters') }}">
            <form method="GET" action="{{ route('reports.monthly') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div>
                    <x-input-label for="month" :value="__('Select Month')" />
                    <select id="month" name="month" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs font-medium">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="year" :value="__('Select Year')" />
                    <select id="year" name="year" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs font-mono font-medium">
                        @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
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
                        {{ __('Generate') }}
                    </button>
                    @if(request()->hasAny(['village_id', 'shop_id', 'product_id']))
                        <a href="{{ route('reports.monthly', ['month' => $month, 'year' => $year]) }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- KPI Summary Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Monthly Milk Collected -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                    <span>{{ __('Monthly Milk Collected') }}</span>
                    <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.182.234l-.454.273L3 17.5V20a2 2 0 002 2h14a2 2 0 002-2v-2.5l-1.572-2.072z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-extrabold text-slate-900">{{ number_format($reportData['collection']['total_litres'], 2) }}</span>
                    <span class="text-xs text-slate-500 font-medium">Ltrs</span>
                </div>
                <div class="text-xxs text-slate-400">
                    Avg <strong class="text-slate-700 font-mono">{{ number_format($reportData['collection']['avg_daily'], 2) }} L/day</strong> (Expense ₹{{ number_format($reportData['collection']['total_amount'], 0) }})
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
                    Avg <strong class="text-slate-700 font-mono">{{ number_format($reportData['center']['avg_daily'], 2) }} L/day</strong> received
                </div>
            </div>

            <!-- Total Monthly Retail Sales -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                    <span>{{ __('Monthly Shop Sales') }}</span>
                    <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-extrabold text-indigo-600">₹{{ number_format($reportData['orders']['total_value'], 2) }}</span>
                </div>
                <div class="text-xxs text-slate-400">
                    From <strong class="text-slate-700">{{ $reportData['orders']['total_count'] }}</strong> total orders placed
                </div>
            </div>

            <!-- Net Operational Balance -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                    <span>{{ __('Net Monthly Balance') }}</span>
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
                    Gross Sales - Milk Procurement Cost
                </div>
            </div>
        </div>

        <!-- Section 1 & 2: Village Collection & Monthly Stock Ledger -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Village Monthly Summary Table -->
            <x-admin.card title="{{ __('Monthly Village Collection Summary') }}" description="{{ __('Milk volume and payout summary per village for :month/:year.', ['month' => date('F', mktime(0, 0, 0, $month, 1)), 'year' => $year]) }}">
                @if($reportData['collection']['village_breakdown']->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs">
                        {{ __('No milk collection data recorded for this month.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Village') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Farmers') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Total Litres') }}</th>
                                    <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Total Payout') }}</th>
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
                    <span>Monthly Avg FAT: <strong class="text-slate-800 font-mono">{{ $reportData['collection']['avg_fat'] }}%</strong></span>
                    <span>Monthly Avg SNF: <strong class="text-slate-800 font-mono">{{ $reportData['collection']['avg_snf'] }}%</strong></span>
                </div>
            </x-admin.card>

            <!-- Monthly Stock Ledger Balance -->
            <x-admin.card title="{{ __('Monthly Stock Ledger Balance') }}" description="{{ __('Milk inventory balance for :month/:year.', ['month' => date('F', mktime(0, 0, 0, $month, 1)), 'year' => $year]) }}">
                <div class="grid grid-cols-2 gap-4 my-2">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xxs font-semibold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Month Opening Stock') }}</span>
                        <span class="text-xl font-mono font-bold text-slate-800">{{ number_format($reportData['center']['opening_stock'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="text-xxs font-semibold uppercase tracking-wider text-emerald-600 block mb-1">{{ __('Total Stock IN') }}</span>
                        <span class="text-xl font-mono font-bold text-emerald-700">+{{ number_format($reportData['center']['stock_in'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-rose-50 rounded-xl border border-rose-100">
                        <span class="text-xxs font-semibold uppercase tracking-wider text-rose-600 block mb-1">{{ __('Total Stock OUT') }}</span>
                        <span class="text-xl font-mono font-bold text-rose-700">-{{ number_format($reportData['center']['stock_out'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <span class="text-xxs font-semibold uppercase tracking-wider text-indigo-600 block mb-1">{{ __('Month Closing Stock') }}</span>
                        <span class="text-xl font-mono font-bold text-indigo-900">{{ number_format($reportData['center']['closing_stock'], 2) }} L</span>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl text-xxs text-slate-500 flex justify-between">
                    <span>Days in Month: <strong class="text-slate-800 font-mono">{{ $reportData['days_in_month'] }} days</strong></span>
                    <span>Daily Avg Intake: <strong class="text-slate-800 font-mono">{{ number_format($reportData['center']['avg_daily'], 2) }} L/day</strong></span>
                </div>
            </x-admin.card>
        </div>

        <!-- Section 3 & 4: Top Shops & Top Selling Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Shops by Sales Value -->
            <x-admin.card title="{{ __('Top Retail Shops by Revenue') }}" description="{{ __('Highest performing shops for the selected month.') }}">
                @if($reportData['orders']['top_shops']->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs">
                        {{ __('No shop sales recorded for this month.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Shop Outlet') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Orders') }}</th>
                                    <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Total Sales') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($reportData['orders']['top_shops'] as $ts)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-6 py-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                            {{ $ts->shop->name ?? __('Unknown Shop') }}
                                            <span class="block text-3xs text-slate-400 font-mono">{{ $ts->shop->shop_code ?? '' }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-slate-600">
                                            {{ $ts->total_orders }}
                                        </td>
                                        <td class="pr-6 py-3 whitespace-nowrap text-right text-xs font-mono font-bold text-indigo-600">
                                            ₹{{ number_format($ts->total_sales, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-admin.card>

            <!-- Monthly Top Selling Products Table -->
            <x-admin.card title="{{ __('Monthly Product Sales Breakdown') }}" description="{{ __('Product volume and revenue for the month.') }}">
                @if($reportData['products']['items']->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs">
                        {{ __('No products sold during this month.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Product') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Total Qty') }}</th>
                                    <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Total Revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($reportData['products']['items'] as $pItem)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-6 py-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                            {{ $pItem->product_name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-slate-700">
                                            {{ number_format($pItem->total_qty, 2) }} {{ $pItem->unit }}
                                        </td>
                                        <td class="pr-6 py-3 whitespace-nowrap text-right text-xs font-mono font-bold text-indigo-600">
                                            ₹{{ number_format($pItem->total_sales, 2) }}
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
