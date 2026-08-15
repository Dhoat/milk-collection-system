<x-admin-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-[#003B73] via-[#005BAC] to-[#0072E5] rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden mb-6">
            <!-- Background Decorative Elements -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
            <div class="absolute right-32 top-0 w-32 h-32 rounded-full bg-sky-400/10 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white text-xl font-black shadow-inner shrink-0">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div>
                        @php
                            $hour = now()->hour;
                            $greeting = $hour < 12 ? __('Good Morning') : ($hour < 17 ? __('Good Afternoon') : __('Good Evening'));
                            $roleName = Auth::user()->role ?? 'user';
                            $roleLabel = ucwords(str_replace('_', ' ', $roleName));
                        @endphp
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                                {{ $greeting }}, {{ Auth::user()->name }}! 👋
                            </h1>
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-white/20 text-white backdrop-blur-md border border-white/30 tracking-wide uppercase">
                                {{ $roleLabel }}
                            </span>
                        </div>
                        <p class="text-sky-100 text-xs sm:text-sm mt-1.5 font-medium flex items-center gap-2">
                            <span>Dairy Operations Command Center</span>
                            <span>•</span>
                            <span>{{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2.5 rounded-xl text-right">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-sky-200 block">{{ __('Active Shift') }}</span>
                        <span class="text-sm font-extrabold text-white flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ $hour < 14 ? __('Morning Shift') : __('Evening Shift') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Today's Milk Collection -->
        <x-admin.stat-card 
            title="{{ __('Today\'s Collection') }}" 
            value="{{ number_format($kpis['today_quantity'], 2) }} L"
            description="{{ __('Total milk collected today') }}"
            color="blue">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </x-slot>
        </x-admin.stat-card>

        <!-- Active Farmers -->
        <x-admin.stat-card 
            title="{{ __('Total Farmers') }}" 
            value="{{ $kpis['active_farmers'] }} / {{ $kpis['total_farmers'] }}"
            description="{{ __('Active / Total registered producers') }}"
            color="emerald">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </x-slot>
        </x-admin.stat-card>

        <!-- Active Villages -->
        <x-admin.stat-card 
            title="{{ __('Active Villages') }}" 
            value="{{ $kpis['active_villages'] }} / {{ $kpis['total_villages'] }}"
            description="{{ __('Operational collection centers') }}"
            color="indigo">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </x-slot>
        </x-admin.stat-card>

        <!-- Today's Payout Amount -->
        @can('view-financials')
            <x-admin.stat-card 
                title="{{ __('Today\'s Value') }}" 
                value="₹ {{ number_format($kpis['today_amount'], 2) }}"
                description="{{ __('Total collection payout value') }}"
                color="rose">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M10 20H4a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"></path>
                </x-slot>
            </x-admin.stat-card>
        @endcan
    </div>

    <!-- Chart & Shift Split Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- 7-Day Collection Trend Chart -->
        <div class="lg:col-span-2">
            <x-admin.card title="{{ __('7-Day Milk Collection Trend') }}" description="{{ __('Milk collection in Litres over the past 7 days') }}">
                <div class="h-64 flex items-end justify-between px-4 pt-8 pb-2 relative border-b border-slate-100 bg-slate-50/40 rounded-xl">
                    <!-- Y-Axis helper lines -->
                    <div class="absolute inset-x-4 top-2 border-t border-slate-200/50 flex justify-between text-[10px] text-slate-400 pointer-events-none">
                        <span>{{ number_format($trendMax, 0) }} L</span>
                    </div>
                    <div class="absolute inset-x-4 top-1/2 border-t border-dashed border-slate-200/60 flex justify-between text-[10px] text-slate-400 pointer-events-none -mt-2">
                        <span>{{ number_format($trendMax / 2, 0) }} L</span>
                    </div>

                    <!-- Trend Bars -->
                    @foreach($collectionTrend as $trend)
                        @php
                            $heightPercent = $trendMax > 0 ? min(100, max(6, ($trend['litres'] / $trendMax) * 100)) : 6;
                            $isToday = $trend['date'] === $today->toDateString();
                        @endphp
                        <div class="flex flex-col items-center flex-grow group relative h-full justify-end">
                            <!-- Tooltip on hover -->
                            <div class="absolute -top-9 bg-slate-900 text-white text-[11px] font-bold px-2.5 py-1 rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-10 whitespace-nowrap">
                                {{ number_format($trend['litres'], 2) }} L
                            </div>
                            
                            <!-- Bar -->
                            <div class="w-8 sm:w-12 rounded-t-xl transition-all duration-300 {{ $isToday ? 'bg-gradient-to-t from-[#003B73] to-[#005BAC] shadow-md shadow-blue-900/30' : 'bg-blue-100 hover:bg-[#005BAC]/30' }}" 
                                 style="height: {{ $heightPercent }}%; min-height: 16px;">
                            </div>
                            
                            <!-- X-Axis Label -->
                            <span class="text-xs font-bold {{ $isToday ? 'text-[#005BAC]' : 'text-slate-500' }} mt-2 tracking-tight">{{ $trend['label'] }}</span>
                            <span class="text-[10px] text-slate-400 font-medium tracking-tighter">{{ $trend['full_label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>
        </div>

        <!-- Today's Shift Breakdown -->
        <x-admin.card title="{{ __('Shift Breakdown (Today)') }}" description="{{ __('Morning vs Evening intake comparison') }}">
            <div class="space-y-5">
                <!-- Morning Shift -->
                <div class="bg-gradient-to-r from-amber-50/80 to-amber-50/20 border border-amber-200/70 rounded-2xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-amber-500 text-white shadow-sm shadow-amber-200">
                            <!-- Sun Icon -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
                            </svg>
                        </span>
                        <div>
                            <h5 class="text-xs font-bold text-amber-900 uppercase tracking-wider">{{ __('Morning Shift') }}</h5>
                            <p class="text-xs text-amber-700 mt-0.5">{{ __('Farmers') }}: <strong class="font-bold text-amber-950">{{ $todayOverview['morning']['farmers'] }}</strong></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-extrabold text-amber-950 tracking-tight">{{ number_format($todayOverview['morning']['quantity'], 2) }} L</span>
                    </div>
                </div>

                <!-- Evening Shift -->
                <div class="bg-gradient-to-r from-blue-50/80 to-blue-50/20 border border-blue-200/70 rounded-2xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-[#005BAC] text-white shadow-sm shadow-blue-200">
                            <!-- Moon Icon -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </span>
                        <div>
                            <h5 class="text-xs font-bold text-blue-900 uppercase tracking-wider">{{ __('Evening Shift') }}</h5>
                            <p class="text-xs text-blue-700 mt-0.5">{{ __('Farmers') }}: <strong class="font-bold text-blue-950">{{ $todayOverview['evening']['farmers'] }}</strong></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-extrabold text-blue-950 tracking-tight">{{ number_format($todayOverview['evening']['quantity'], 2) }} L</span>
                    </div>
                </div>

                <!-- Shift Split Meter -->
                @php
                    $totalQty = $todayOverview['morning']['quantity'] + $todayOverview['evening']['quantity'];
                    $morningPercent = $totalQty > 0 ? ($todayOverview['morning']['quantity'] / $totalQty) * 100 : 50;
                @endphp
                <div class="pt-2">
                    <div class="flex justify-between text-xs font-bold text-slate-600 mb-2">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Morning ({{ number_format($morningPercent, 0) }}%)
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#005BAC]"></span>
                            Evening ({{ number_format(100 - $morningPercent, 0) }}%)
                        </span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex p-0.5 border border-slate-200/60">
                        <div class="h-full bg-amber-500 rounded-l-full transition-all duration-500" style="width: {{ $morningPercent }}%;"></div>
                        <div class="h-full bg-[#005BAC] rounded-r-full flex-grow transition-all duration-500"></div>
                    </div>
                </div>
            </div>
        </x-admin.card>
    </div>

    <!-- Village Rankings & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Village Ranking Table -->
        <div class="lg:col-span-2">
            <x-admin.card title="{{ __('Village Performance (Today)') }}" description="{{ __('Live milk collection aggregated by village center') }}">
                @if($villagePerformance->isEmpty())
                    <div class="text-center py-8 text-slate-400 text-xs font-medium">
                        {{ __('No village collection records available for today.') }}
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 -mb-6">
                        <table class="min-w-full divide-y divide-slate-200/80">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th scope="col" class="pl-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Village') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Farmers') }}</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                                    @can('view-financials')
                                        <th scope="col" class="pr-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Estimated Value') }}</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($villagePerformance as $perf)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="pl-6 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <span class="w-2.5 h-2.5 rounded-full {{ $perf->status ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                                <span class="text-sm font-bold text-slate-800">{{ $perf->name }}</span>
                                                <span class="text-xs text-slate-400 font-semibold uppercase">({{ $perf->code }})</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-600">
                                            {{ $perf->farmers_count }} {{ __('farmers') }}
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-xs font-extrabold text-[#005BAC]">
                                            {{ number_format($perf->today_quantity, 2) }} L
                                        </td>
                                        @can('view-financials')
                                            <td class="pr-6 py-3.5 whitespace-nowrap text-right text-xs font-extrabold text-slate-900">
                                                ₹ {{ number_format($perf->today_amount, 2) }}
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

        <!-- Recent Activity Feed -->
        <x-admin.card title="{{ __('Recent Activity') }}" description="{{ __('Real-time log of system transactions') }}">
            @if($recentActivity->isEmpty())
                <div class="text-center py-8 text-slate-400 text-xs font-medium">
                    {{ __('No recent activity logged.') }}
                </div>
            @else
                <div class="flow-root">
                    <ul role="list" class="-mb-6">
                        @foreach($recentActivity as $index => $act)
                            @php
                                $isLast = $index === count($recentActivity) - 1;
                                $iconBg = match($act['icon']) {
                                    'collection' => 'bg-blue-50 text-[#005BAC] border-blue-200',
                                    'farmer' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                    'village' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                            @endphp
                            <li>
                                <div class="relative pb-6">
                                    @if(!$isLast)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div class="shrink-0">
                                            <span class="h-8 w-8 rounded-xl flex items-center justify-center border shadow-sm {{ $iconBg }}">
                                                @if($act['icon'] === 'collection')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                    </svg>
                                                @elseif($act['icon'] === 'farmer')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex-grow min-w-0 pt-0.5">
                                            <p class="text-xs font-bold text-slate-800 leading-snug">{{ $act['message'] }}</p>
                                            <div class="flex justify-between items-center gap-2 mt-1">
                                                <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">{{ $act['detail'] }}</span>
                                                <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap">{{ $act['timestamp']->diffForHumans() }}</span>
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
