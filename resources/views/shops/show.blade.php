<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ $shop->name }}" description="{{ __('Shop Code:') }} {{ $shop->shop_code }} • {{ __('Retail Outlet Profile') }}">
            <x-slot name="actions">
                <div class="flex items-center gap-3">
                    @can('update', $shop)
                        <a href="{{ route('shops.edit', $shop) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            {{ __('Edit Shop') }}
                        </a>
                    @endcan
                    <a href="{{ route('shops.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Back to Directory') }}
                    </a>
                </div>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="space-y-8">
        <!-- Overview Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Status Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Account Status') }}</span>
                    <div class="mt-1">
                        @if($shop->status)
                            <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wider">
                                {{ __('Active') }}
                            </span>
                        @else
                            <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wider">
                                {{ __('Inactive') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Owner Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Shop Owner') }}</span>
                    <span class="text-sm font-bold text-slate-800 mt-1 block truncate max-w-[150px]">{{ $shop->owner_name }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Phone Number') }}</span>
                    <span class="text-sm font-bold font-mono text-slate-800 mt-1 block">{{ $shop->phone }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
            </div>

            <!-- Credit Limit Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Credit Limit') }}</span>
                    <span class="text-sm font-bold font-mono text-emerald-600 mt-1 block">₹{{ number_format($shop->credit_limit, 2) }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M10 20H4a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Detailed Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Shop & Location Details -->
            <x-admin.card title="{{ __('Shop & Location Information') }}">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Shop Code') }}</dt>
                        <dd class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">{{ $shop->shop_code }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Shop Name') }}</dt>
                        <dd class="font-semibold text-slate-800">{{ $shop->name }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Associated Village') }}</dt>
                        <dd class="font-medium text-slate-800">{{ $shop->village->name ?? __('N/A (Direct Outlet)') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Area / Market Zone') }}</dt>
                        <dd class="font-medium text-slate-800">{{ $shop->area ?? __('N/A') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Full Address') }}</dt>
                        <dd class="text-right text-slate-700 max-w-xs">{{ $shop->address ?? __('No address recorded.') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Registered Date') }}</dt>
                        <dd class="text-slate-700">{{ $shop->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Contact & Terms Details -->
            <x-admin.card title="{{ __('Contact & Financial Parameters') }}">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Owner Name') }}</dt>
                        <dd class="font-semibold text-slate-800">{{ $shop->owner_name }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Primary Phone') }}</dt>
                        <dd class="font-mono font-bold text-slate-800">{{ $shop->phone }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Email Address') }}</dt>
                        <dd class="text-slate-700">{{ $shop->email ?? __('N/A') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Approved Credit Limit') }}</dt>
                        <dd class="font-mono font-bold text-emerald-600">₹{{ number_format($shop->credit_limit, 2) }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Notes & Remarks') }}</dt>
                        <dd class="text-right text-slate-700 max-w-xs italic">{{ $shop->notes ?? __('No notes specified.') }}</dd>
                    </div>
                </dl>
            </x-admin.card>
        </div>

        <!-- Future Extensibility Tabs / Placeholders (Orders, Deliveries, Payments, Purchase History) -->
        <x-admin.card title="{{ __('Shop Operations History') }}" description="{{ __('Future activity logs for orders, deliveries, payments, and product purchasing.') }}">
            <div x-data="{ activeTab: 'orders' }" class="space-y-6">
                <!-- Tab Navigation -->
                <div class="flex border-b border-slate-200 gap-6 text-xs font-semibold">
                    <button @click="activeTab = 'orders'" 
                            :class="activeTab === 'orders' ? 'border-b-2 border-indigo-600 text-indigo-600 py-3' : 'text-slate-500 hover:text-slate-700 py-3'">
                        {{ __('Shop Orders') }}
                    </button>
                    <button @click="activeTab = 'deliveries'" 
                            :class="activeTab === 'deliveries' ? 'border-b-2 border-indigo-600 text-indigo-600 py-3' : 'text-slate-500 hover:text-slate-700 py-3'">
                        {{ __('Deliveries') }}
                    </button>
                    <button @click="activeTab = 'payments'" 
                            :class="activeTab === 'payments' ? 'border-b-2 border-indigo-600 text-indigo-600 py-3' : 'text-slate-500 hover:text-slate-700 py-3'">
                        {{ __('Payments & Credit') }}
                    </button>
                    <button @click="activeTab = 'purchases'" 
                            :class="activeTab === 'purchases' ? 'border-b-2 border-indigo-600 text-indigo-600 py-3' : 'text-slate-500 hover:text-slate-700 py-3'">
                        {{ __('Purchase History') }}
                    </button>
                </div>

                <!-- Tab 1: Orders Placeholder -->
                <div x-show="activeTab === 'orders'" class="text-center py-10 border-2 border-dashed border-slate-200 rounded-2xl">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <h4 class="text-xs font-bold text-slate-700 mb-1">{{ __('No Shop Orders Yet') }}</h4>
                    <p class="text-xxs text-slate-400 max-w-sm mx-auto">{{ __('Shop orders will connect this outlet to raw milk / product stock dispatches in the upcoming phase.') }}</p>
                </div>

                <!-- Tab 2: Deliveries Placeholder -->
                <div x-show="activeTab === 'deliveries'" style="display: none;" class="text-center py-10 border-2 border-dashed border-slate-200 rounded-2xl">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                    <h4 class="text-xs font-bold text-slate-700 mb-1">{{ __('No Delivery Log') }}</h4>
                    <p class="text-xxs text-slate-400 max-w-sm mx-auto">{{ __('Dispatch vehicle delivery logs and receiving acknowledgements will be tracked here.') }}</p>
                </div>

                <!-- Tab 3: Payments Placeholder -->
                <div x-show="activeTab === 'payments'" style="display: none;" class="text-center py-10 border-2 border-dashed border-slate-200 rounded-2xl">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M10 20H4a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"></path></svg>
                    <h4 class="text-xs font-bold text-slate-700 mb-1">{{ __('No Payment Transactions') }}</h4>
                    <p class="text-xxs text-slate-400 max-w-sm mx-auto">{{ __('Shop payment collections, outstanding balances, and credit tracking will be recorded here.') }}</p>
                </div>

                <!-- Tab 4: Purchase History Placeholder -->
                <div x-show="activeTab === 'purchases'" style="display: none;" class="text-center py-10 border-2 border-dashed border-slate-200 rounded-2xl">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <h4 class="text-xs font-bold text-slate-700 mb-1">{{ __('Purchase Item Summary') }}</h4>
                    <p class="text-xxs text-slate-400 max-w-sm mx-auto">{{ __('Aggregated breakdown of purchased products (Raw Milk, Curd, Ghee, etc.) for this outlet.') }}</p>
                </div>
            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
