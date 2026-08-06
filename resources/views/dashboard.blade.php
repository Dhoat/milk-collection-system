<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ __('Admin Dashboard') }}" 
            description="{{ __('Welcome back! Here is a summary of the milk collection system metrics for today, ') }}{{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}."
        />
    </x-slot>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Villages -->
        <x-admin.stat-card 
            title="{{ __('Active Villages') }}" 
            value="{{ $kpis['active_villages'] }} / {{ $kpis['total_villages'] }}"
            description="{{ __('Registered collection sectors') }}"
            color="indigo">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </x-slot>
        </x-admin.stat-card>

        <!-- Total Farmers -->
        <x-admin.stat-card 
            title="{{ __('Active Farmers') }}" 
            value="{{ $kpis['active_farmers'] }} / {{ $kpis['total_farmers'] }}"
            description="{{ __('Registered dairy producers') }}"
            color="emerald">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </x-slot>
        </x-admin.stat-card>

        <!-- Today's Litres -->
        <x-admin.stat-card 
            title="{{ __('Today\'s Quantity') }}" 
            value="{{ number_format($kpis['today_quantity'], 2) }} L"
            description="{{ __('Total milk collected today') }}"
            color="amber">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </x-slot>
        </x-admin.stat-card>

        <!-- Today's Amount -->
        <x-admin.stat-card 
            title="{{ __('Today\'s Value') }}" 
            value="₹ {{ number_format($kpis['today_amount'], 2) }}"
            description="{{ __('Total estimated payout value') }}"
            color="rose">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M10 20H4a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"></path>
            </x-slot>
        </x-admin.stat-card>
    </div>

    <!-- Chart & Shift Split Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- 7-Day Collection Trend Chart -->
        <div class="lg:col-span-2">
            <x-admin.card title="{{ __('7-Day Milk Collection Trend') }}" description="{{ __('Milk collection in Liters over the past 7 days') }}">
                <div class="h-64 flex items-end justify-between px-2 pt-6 relative border-b border-slate-100">
                    <!-- Y-Axis helper lines -->
                    <div class="absolute inset-x-0 top-0 border-t border-slate-50 flex justify-between text-xxs text-slate-300 pointer-events-none">
                        <span>{{ number_format($trendMax, 0) }} L</span>
                    </div>
                    <div class="absolute inset-x-0 top-1/2 border-t border-slate-50 flex justify-between text-xxs text-slate-350 pointer-events-none -mt-2">
                        <span>{{ number_format($trendMax / 2, 0) }} L</span>
                    </div>

                    <!-- Trend Bars -->
                    @foreach($collectionTrend as $trend)
                        @php
                            $heightPercent = $trendMax > 0 ? min(100, max(4, ($trend['litres'] / $trendMax) * 100)) : 4;
                            $isToday = $trend['date'] === $today->toDateString();
                        @endphp
                        <div class="flex flex-col items-center flex-grow group">
                            <!-- Tooltip on hover -->
                            <div class="absolute bg-slate-800 text-white text-xxs font-semibold px-2 py-1 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none -mt-10" style="bottom: {{ $heightPercent + 10 }}%;">
                                {{ number_format($trend['litres'], 2) }} L
                            </div>
                            
                            <!-- Bar -->
                            <div class="w-8 sm:w-12 rounded-t-lg transition-all duration-300 {{ $isToday ? 'bg-indigo-600 shadow shadow-indigo-150' : 'bg-indigo-100 hover:bg-indigo-200' }}" 
                                 style="height: {{ $heightPercent }}px; min-height: 16px;">
                            </div>
                            
                            <!-- X-Axis Label -->
                            <span class="text-xxs font-bold text-slate-400 mt-2 tracking-tight">{{ $trend['label'] }}</span>
                            <span class="text-3xs text-slate-300 font-medium tracking-tighter">{{ $trend['full_label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>
        </div>

        <!-- Today's Shift Breakdown -->
        <x-admin.card title="{{ __('Shift Overview (Today)') }}" description="{{ __('Comparison between Morning and Evening milk collection') }}">
            <div class="space-y-6">
                <!-- Morning Shift -->
                <div class="bg-slate-50/50 border border-slate-100 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-amber-50 border border-amber-100 text-amber-500 shadow-sm">
                            <!-- Sun Icon -->
                            <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
                            </svg>
                        </span>
                        <div>
                            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wide">{{ __('Morning Shift') }}</h5>
                            <p class="text-xxs text-slate-400 mt-0.5">{{ __('Farmers participated: ') }} <strong class="text-slate-600 font-semibold">{{ $todayOverview['morning']['farmers'] }}</strong></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-base font-bold text-slate-800 tracking-tight">{{ number_format($todayOverview['morning']['quantity'], 2) }} L</span>
                    </div>
                </div>

                <!-- Evening Shift -->
                <div class="bg-slate-50/50 border border-slate-100 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-500 shadow-sm">
                            <!-- Moon Icon -->
                            <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </span>
                        <div>
                            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wide">{{ __('Evening Shift') }}</h5>
                            <p class="text-xxs text-slate-400 mt-0.5">{{ __('Farmers participated: ') }} <strong class="text-slate-600 font-semibold">{{ $todayOverview['evening']['farmers'] }}</strong></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-base font-bold text-slate-800 tracking-tight">{{ number_format($todayOverview['evening']['quantity'], 2) }} L</span>
                    </div>
                </div>

                <!-- Shift Split Visual Meter Bar -->
                @php
                    $totalQty = $todayOverview['morning']['quantity'] + $todayOverview['evening']['quantity'];
                    $morningPercent = $totalQty > 0 ? ($todayOverview['morning']['quantity'] / $totalQty) * 100 : 50;
                @endphp
                <div class="pt-2">
                    <div class="flex justify-between text-xxs text-slate-450 font-bold mb-1.5 px-0.5">
                        <span>Morning ({{ number_format($morningPercent, 0) }}%)</span>
                        <span>Evening ({{ number_format(100 - $morningPercent, 0) }}%)</span>
                    </div>
                    <div class="w-full h-2.5 bg-indigo-100 rounded-full overflow-hidden flex">
                        <div class="h-full bg-amber-400 rounded-l-full transition-all duration-550" style="width: {{ $morningPercent }}%;"></div>
                        <div class="h-full bg-indigo-500 rounded-r-full flex-grow transition-all duration-550"></div>
                    </div>
                </div>
            </div>
        </x-admin.card>
    </div>

    <!-- Village Rankings & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Village Ranking Table -->
        <div class="lg:col-span-2">
            <x-admin.card title="{{ __('Village Performance (Today)') }}" description="{{ __('Milk quantities received across active villages today') }}">
                @if($villagePerformance->isEmpty())
                    <div class="text-center py-6 text-slate-400 text-xs">
                        {{ __('No village collection data recorded for today.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -mb-6">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th scope="col" class="pl-6 py-2.5 text-left text-xxs font-bold text-slate-400 uppercase tracking-wider">{{ __('Village') }}</th>
                                    <th scope="col" class="px-4 py-2.5 text-left text-xxs font-bold text-slate-400 uppercase tracking-wider">{{ __('Farmers') }}</th>
                                    <th scope="col" class="px-4 py-2.5 text-left text-xxs font-bold text-slate-400 uppercase tracking-wider">{{ __('Qty (Liters)') }}</th>
                                    <th scope="col" class="pr-6 py-2.5 text-right text-xxs font-bold text-slate-400 uppercase tracking-wider">{{ __('Today Value') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($villagePerformance as $perf)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="pl-6 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $perf->status ? 'bg-emerald-500' : 'bg-slate-350' }}"></span>
                                                <span class="text-xs font-semibold text-slate-800">{{ $perf->name }}</span>
                                                <span class="text-3xs text-slate-400 uppercase tracking-wide">({{ $perf->code }})</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-500">
                                            {{ $perf->farmers_count }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-700">
                                            {{ number_format($perf->today_quantity, 2) }} L
                                        </td>
                                        <td class="pr-6 py-3.5 whitespace-nowrap text-right text-xs font-bold text-indigo-650">
                                            ₹ {{ number_format($perf->today_amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-admin.card>
        </div>

        <!-- Recent Activities Feed -->
        <x-admin.card title="{{ __('Recent Activity') }}" description="{{ __('Log of transactions recorded in the system') }}">
            @if($recentActivity->isEmpty())
                <div class="text-center py-6 text-slate-400 text-xs">
                    {{ __('No recent activities found.') }}
                </div>
            @else
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($recentActivity as $index => $act)
                            @php
                                $isLast = $index === count($recentActivity) - 1;
                                $colorClasses = 'bg-slate-100 text-slate-500';
                                if($act['icon'] === 'collection') $colorClasses = 'bg-amber-50 border-amber-100 text-amber-550 border';
                                elseif($act['icon'] === 'farmer') $colorClasses = 'bg-emerald-50 border-emerald-100 text-emerald-550 border';
                                elseif($act['icon'] === 'village') $colorClasses = 'bg-indigo-50 border-indigo-100 text-indigo-550 border';
                            @endphp
                            <li>
                                <div class="relative pb-6">
                                    @if(!$isLast)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-100" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-lg flex items-center justify-center ring-4 ring-white {{ $colorClasses }}">
                                                @if($act['icon'] === 'collection')
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                    </svg>
                                                @elseif($act['icon'] === 'farmer')
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex-grow min-w-0 pt-0.5">
                                            <p class="text-xs font-semibold text-slate-800">{{ $act['message'] }}</p>
                                            <div class="flex justify-between items-center gap-2 mt-1">
                                                <span class="text-3xs text-slate-400 uppercase font-bold tracking-wide">{{ $act['detail'] }}</span>
                                                <span class="text-3xs text-slate-400 font-medium whitespace-nowrap">{{ $act['timestamp']->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-admin.card>
    </div>
</x-admin-layout>
