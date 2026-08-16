<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" 
     class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     style="display: none;">
</div>

<!-- Sidebar Layout Container -->
<div x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
     class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-[#00264D] text-white border-r border-slate-800 shadow-2xl transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0"
     style="display: flex;">
    
    <!-- Branding Header -->
    <div class="flex items-center justify-between h-20 px-5 border-b border-sky-900/50 bg-[#001D3D]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <!-- Original Milk Drop / Dairy Logo Concept -->
            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-[#0072E5] to-[#004788] text-white shadow-md shadow-sky-900/50 border border-sky-400/30 group-hover:scale-105 transition-transform duration-200">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Milk Drop Path -->
                    <path d="M12 2.5C12 2.5 5.5 11 5.5 15.5C5.5 19.0899 8.41015 22 12 22C15.5899 22 18.5 19.0899 18.5 15.5C18.5 11 12 2.5 12 2.5Z" fill="currentColor" fill-opacity="0.25" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <!-- Inner Milk Droplet Wave line -->
                    <path d="M7 16.5C8 18 10 19 12 19C14.5 19 16.5 17.5 17 16" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="11" r="1.5" fill="white"/>
                </svg>
            </span>
            <div class="flex flex-col min-w-0">
                <span class="text-base font-bold tracking-tight text-white group-hover:text-sky-300 transition-colors leading-snug truncate">Dairy Management</span>
                <span class="text-[10px] text-sky-300/80 font-medium tracking-wider uppercase truncate">Milk Collection & Distribution</span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="p-1.5 text-sky-200/70 hover:text-white rounded-lg hover:bg-white/10 lg:hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Scroll -->
    <div class="flex-1 px-3.5 py-5 overflow-y-auto space-y-6 scrollbar-thin">
        <!-- Dashboard Group -->
        <div class="space-y-1">
            <x-admin.nav-item route="dashboard" active="dashboard">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                </x-slot>
                {{ __('Dashboard') }}
            </x-admin.nav-item>
        </div>

        <!-- OPERATIONS Group -->
        @if(Gate::allows('view-villages') || Gate::allows('manage-farmers') || Gate::allows('manage-collections') || Gate::allows('manage-receiving') || Gate::allows('manage-stock'))
            <div class="space-y-1">
                <h3 class="text-[11px] font-extrabold tracking-wider text-sky-300/60 uppercase px-3 mb-2">{{ __('OPERATIONS') }}</h3>
                
                @can('view-villages')
                    <x-admin.nav-item route="villages.index" active="villages.*">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </x-slot>
                        {{ __('Villages') }}
                    </x-admin.nav-item>
                @endcan

                @can('manage-farmers')
                    <x-admin.nav-item route="farmers.index" active="farmers.*">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </x-slot>
                        {{ __('Farmers') }}
                    </x-admin.nav-item>
                @endcan

                @can('manage-collections')
                    <x-admin.nav-item route="milk-collections.index" active="milk-collections.*">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </x-slot>
                        {{ __('Milk Collection') }}
                    </x-admin.nav-item>
                @endcan

                @can('manage-receiving')
                    <x-admin.nav-item route="milk-receivings.index" active="milk-receivings.*">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </x-slot>
                        {{ __('Milk Receiving') }}
                    </x-admin.nav-item>
                @endcan

                @can('manage-stock')
                    <x-admin.nav-item route="milk-stocks.index" active="milk-stocks.*">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </x-slot>
                        {{ __('Milk Stock') }}
                    </x-admin.nav-item>
                @endcan
            </div>
        @endif

        <!-- SALES Group -->
        @if(Gate::allows('view-shops') || Gate::allows('view-orders') || Gate::allows('view-deliveries'))
            <div class="space-y-1">
                <h3 class="text-[11px] font-extrabold tracking-wider text-sky-300/60 uppercase px-3 mb-2">{{ __('SALES') }}</h3>
                
                @can('view-shops')
                    <x-admin.nav-item route="shops.index" active="shops.*">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </x-slot>
                        {{ __('Shops') }}
                    </x-admin.nav-item>
                @endcan

                @can('view-orders')
                    <x-admin.nav-item route="shop-orders.index" active="shop-orders.*">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </x-slot>
                        {{ __('Shop Orders') }}
                    </x-admin.nav-item>
                @endcan

                @can('view-deliveries')
                    <x-admin.nav-item route="deliveries.index" active="deliveries.*">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </x-slot>
                        {{ __('Deliveries') }}
                    </x-admin.nav-item>
                @endcan
            </div>
        @endif

        <!-- ANALYTICS Group -->
        @can('view-village-payment-statement')
            <div class="space-y-1">
                <h3 class="text-[11px] font-extrabold tracking-wider text-sky-300/60 uppercase px-3 mb-2">{{ __('STATEMENTS & REPORTS') }}</h3>
                
                <x-admin.nav-item route="village-payment-statement.index" active="village-payment-statement.*">
                    <x-slot name="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </x-slot>
                    {{ __('Village Statement') }}
                </x-admin.nav-item>

                @can('view-reports')
                    <x-admin.nav-item route="reports.daily" active="reports.daily">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </x-slot>
                        {{ __('Daily Report') }}
                    </x-admin.nav-item>

                    <x-admin.nav-item route="reports.monthly" active="reports.monthly">
                        <x-slot name="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </x-slot>
                        {{ __('Monthly Report') }}
                    </x-admin.nav-item>
                @endcan
            </div>
        @endcan

        <!-- ADMINISTRATION Group -->
        <div class="space-y-1">
            <h3 class="text-[11px] font-extrabold tracking-wider text-sky-300/60 uppercase px-3 mb-2">{{ __('ADMINISTRATION') }}</h3>
            
            @can('manage-users')
                <x-admin.nav-item route="users.index" active="users.*">
                    <x-slot name="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </x-slot>
                    {{ __('User Management') }}
                </x-admin.nav-item>
            @endcan

            <x-admin.nav-item route="profile.edit" active="profile.*">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </x-slot>
                {{ __('Profile') }}
            </x-admin.nav-item>

            @can('manage-settings')
                <x-admin.nav-item route="settings.index" active="settings.*">
                    <x-slot name="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </x-slot>
                    {{ __('Settings') }}
                </x-admin.nav-item>
            @endcan
        </div>
    </div>

    <!-- Sidebar Footer Profile Badge -->
    <div class="p-4 border-t border-sky-900/50 bg-[#001D3D]">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-[#005BAC] text-white text-xs font-bold uppercase shadow-sm border border-sky-400/30">
                {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
            </span>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</span>
                <span class="text-[11px] text-sky-300/80 truncate capitalize">
                    {{ str_replace('_', ' ', Auth::user()->role ?? 'user') }}
                </span>
            </div>
        </div>
    </div>
</div>
