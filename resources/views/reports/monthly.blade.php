<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Monthly Operations Report') }}" description="{{ __('Executive monthly performance digest for milk collection, inventory, shop sales, and product revenue.') }}">
            <x-slot name="actions">
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.daily') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
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
                    <label for="month" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Select Month') }}</label>
                    <select id="month" name="month" class="w-full py-2.5 text-sm font-semibold border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Select Year') }}</label>
                    <select id="year" name="year" class="w-full py-2.5 text-sm font-mono font-semibold border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Filter Village') }}</label>
                    <select id="village_id" name="village_id" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Villages') }}</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}" {{ request('village_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="shop_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Filter Shop Outlet') }}</label>
                    <select id="shop_id" name="shop_id" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Shops') }}</option>
                        @foreach($shops as $s)
                            <option value="{{ $s->id }}" {{ request('shop_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="product_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Filter Product') }}</label>
                    <select id="product_id" name="product_id" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Products') }}</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <x-primary-button type="submit" class="w-full justify-center">
                        {{ __('Generate') }}
                    </x-primary-button>
                    @if(request()->hasAny(['village_id', 'shop_id', 'product_id']))
                        <a href="{{ route('reports.monthly', ['month' => $month, 'year' => $year]) }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- KPI Summary Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Monthly Milk Collected -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <span>{{ __('Monthly Milk Collected') }}</span>
                    <span class="p-2 bg-blue-50 text-[#005BAC] rounded-xl border border-blue-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.182.234l-.454.273L3 17.5V20a2 2 0 002 2h14a2 2 0 002-2v-2.5l-1.572-2.072z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-black text-slate-900">{{ number_format($reportData['collection']['total_litres'], 2) }}</span>
                    <span class="text-xs text-slate-500 font-bold">Ltrs</span>
                </div>
                <div class="text-xs text-slate-400 font-semibold">
                    Avg <strong class="text-slate-800 font-mono">{{ number_format($reportData['collection']['avg_daily'], 2) }} L/day</strong> (Expense ₹{{ number_format($reportData['collection']['total_amount'], 0) }})
                </div>
            </div>

            <!-- Total Main Center Received -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <span>{{ __('Main Center Received') }}</span>
                    <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-black text-slate-900">{{ number_format($reportData['center']['total_received'], 2) }}</span>
                    <span class="text-xs text-slate-500 font-bold">Ltrs</span>
                </div>
                <div class="text-xs text-slate-400 font-semibold">
                    Avg <strong class="text-slate-800 font-mono">{{ number_format($reportData['center']['avg_daily'], 2) }} L/day</strong> received
                </div>
            </div>

            <!-- Total Monthly Retail Sales -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <span>{{ __('Monthly Shop Sales') }}</span>
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-black text-[#005BAC]">₹{{ number_format($reportData['orders']['total_value'], 2) }}</span>
                </div>
                <div class="text-xs text-slate-400 font-semibold">
                    From <strong class="text-slate-800">{{ $reportData['orders']['total_count'] }}</strong> total orders placed
                </div>
            </div>

            <!-- Net Operational Balance -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                <div class="flex items-center justify-between text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <span>{{ __('Net Monthly Balance') }}</span>
                    <span class="p-2 bg-purple-50 text-purple-600 rounded-xl border border-purple-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-mono font-black {{ $reportData['financial']['net_balance'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        ₹{{ number_format($reportData['financial']['net_balance'], 2) }}
                    </span>
                </div>
                <div class="text-xs text-slate-400 font-semibold">
                    Gross Sales - Milk Procurement Cost
                </div>
            </div>
        </div>

        <!-- Section 1 & 2: Village Collection & Monthly Stock Ledger -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Village Monthly Summary Table -->
            <x-admin.card title="{{ __('Monthly Village Collection Summary') }}" description="{{ __('Milk volume and payout summary per village for :month/:year.', ['month' => date('F', mktime(0, 0, 0, $month, 1)), 'year' => $year]) }}">
                @if($reportData['collection']['village_breakdown']->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs font-semibold">
                        {{ __('No milk collection data recorded for this month.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full divide-y divide-slate-200/80">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Village') }}</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Farmers') }}</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Total Litres') }}</th>
                                    <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Total Payout') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($reportData['collection']['village_breakdown'] as $row)
                                    <tr class="hover:bg-blue-50/20 transition-colors">
                                        <td class="pl-6 py-4 whitespace-nowrap text-xs font-bold text-slate-900">
                                            {{ $row->village_name ?? __('Unknown') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-600 font-mono font-semibold">
                                            {{ $row->farmers_count }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs font-mono font-extrabold text-slate-900">
                                            {{ number_format($row->total_litres, 2) }} L
                                        </td>
                                        <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-mono font-black text-[#005BAC]">
                                            ₹{{ number_format($row->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="mt-6 pt-3 border-t border-slate-100 flex justify-between text-xs text-slate-500 font-bold">
                    <span>Monthly Avg FAT: <strong class="text-slate-900 font-mono">{{ $reportData['collection']['avg_fat'] }}%</strong></span>
                    <span>Monthly Avg SNF: <strong class="text-slate-900 font-mono">{{ $reportData['collection']['avg_snf'] }}%</strong></span>
                </div>
            </x-admin.card>

            <!-- Monthly Stock Ledger Balance -->
            <x-admin.card title="{{ __('Monthly Stock Ledger Balance') }}" description="{{ __('Milk inventory balance for :month/:year.', ['month' => date('F', mktime(0, 0, 0, $month, 1)), 'year' => $year]) }}">
                <div class="grid grid-cols-2 gap-4 my-2">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Month Opening Stock') }}</span>
                        <span class="text-xl font-mono font-black text-slate-800">{{ number_format($reportData['center']['opening_stock'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-emerald-50/70 rounded-2xl border border-emerald-200">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-1">{{ __('Total Stock IN') }}</span>
                        <span class="text-xl font-mono font-black text-emerald-800">+{{ number_format($reportData['center']['stock_in'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-rose-50/70 rounded-2xl border border-rose-200">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-700 block mb-1">{{ __('Total Stock OUT') }}</span>
                        <span class="text-xl font-mono font-black text-rose-800">-{{ number_format($reportData['center']['stock_out'], 2) }} L</span>
                    </div>
                    <div class="p-4 bg-blue-50/70 rounded-2xl border border-blue-200">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#005BAC] block mb-1">{{ __('Month Closing Stock') }}</span>
                        <span class="text-xl font-mono font-black text-[#003B73]">{{ number_format($reportData['center']['closing_stock'], 2) }} L</span>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 font-medium flex justify-between">
                    <span>Days in Month: <strong class="text-slate-900 font-mono font-bold">{{ $reportData['days_in_month'] }} days</strong></span>
                    <span>Daily Avg Intake: <strong class="text-slate-900 font-mono font-bold">{{ number_format($reportData['center']['avg_daily'], 2) }} L/day</strong></span>
                </div>
            </x-admin.card>
        </div>

        <!-- Section 3 & 4: Top Shops & Top Selling Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Shops by Sales Value -->
            <x-admin.card title="{{ __('Top Retail Shops by Revenue') }}" description="{{ __('Highest performing shops for the selected month.') }}">
                @if($reportData['orders']['top_shops']->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs font-semibold">
                        {{ __('No shop sales recorded for this month.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full divide-y divide-slate-200/80">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Shop Outlet') }}</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Orders') }}</th>
                                    <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Total Sales') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($reportData['orders']['top_shops'] as $ts)
                                    <tr class="hover:bg-blue-50/20 transition-colors">
                                        <td class="pl-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-bold text-slate-900">{{ $ts->shop->name ?? __('Unknown Shop') }}</div>
                                            <div class="text-[11px] font-semibold text-slate-400 font-mono">{{ $ts->shop->shop_code ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs font-mono font-bold text-slate-700">
                                            {{ $ts->total_orders }}
                                        </td>
                                        <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-mono font-black text-[#005BAC]">
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
                    <div class="text-center py-8 text-slate-400 text-xs font-semibold">
                        {{ __('No products sold during this month.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full divide-y divide-slate-200/80">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Total Qty') }}</th>
                                    <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Total Revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($reportData['products']['items'] as $pItem)
                                    <tr class="hover:bg-blue-50/20 transition-colors">
                                        <td class="pl-6 py-4 whitespace-nowrap text-xs font-bold text-slate-900">
                                            {{ $pItem->product_name }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs font-mono text-slate-700 font-bold">
                                            {{ number_format($pItem->total_qty, 2) }} {{ $pItem->unit }}
                                        </td>
                                        <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-mono font-black text-[#005BAC]">
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
