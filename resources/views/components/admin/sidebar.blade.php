<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" 
     class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden"
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
     class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-white border-r border-slate-200/80 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
     style="display: flex;">
    
    <!-- Branding Header -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-100">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-200">
                <!-- Cow/Milk Bottle alternative premium icon -->
                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
            </span>
            <div class="flex flex-col">
                <span class="text-sm font-bold tracking-tight text-slate-800 group-hover:text-indigo-600 transition-colors">Milk Center</span>
                <span class="text-xxs text-indigo-500 font-semibold tracking-wider uppercase">Dairy Management</span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50 lg:hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Scroll -->
    <div class="flex-1 px-4 py-6 overflow-y-auto space-y-7">
        <!-- Dashboard Group -->
        <div class="space-y-1">
            <x-admin.nav-item route="dashboard" active="dashboard">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                </x-slot>
                {{ __('Dashboard') }}
            </x-admin.nav-item>
        </div>

        <!-- Milk Management Group -->
        <div class="space-y-1.5">
            <h3 class="text-xs font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">{{ __('Milk Management') }}</h3>
            
            <x-admin.nav-item route="villages.index" active="villages.*">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </x-slot>
                {{ __('Villages') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="farmers.index" active="farmers.*">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </x-slot>
                {{ __('Farmers') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="milk-collections.index" active="milk-collections.*">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </x-slot>
                {{ __('Milk Collections') }}
            </x-admin.nav-item>
        </div>

        <!-- Operations Group -->
        <div class="space-y-1.5">
            <h3 class="text-xs font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">{{ __('Operations') }}</h3>
            
            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </x-slot>
                {{ __('Main Milk Center') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </x-slot>
                {{ __('Milk Stock') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </x-slot>
                {{ __('Shops') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </x-slot>
                {{ __('Shop Orders') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                </x-slot>
                {{ __('Delivery') }}
            </x-admin.nav-item>
        </div>

        <!-- Reports Group -->
        <div class="space-y-1.5">
            <h3 class="text-xs font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">{{ __('Reports') }}</h3>
            
            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H3a2 2 0 01-2-2V5a2 2 0 012-2h6l2 2h7a2 2 0 012 2v11a2 2 0 01-2 2z"></path>
                </x-slot>
                {{ __('Daily Reports') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </x-slot>
                {{ __('Monthly Reports') }}
            </x-admin.nav-item>
        </div>

        <!-- Finance Group -->
        <div class="space-y-1.5">
            <h3 class="text-xs font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">{{ __('Finance') }}</h3>
            
            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M10 20H4a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"></path>
                </x-slot>
                {{ __('Payments') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </x-slot>
                {{ __('Expenses') }}
            </x-admin.nav-item>
        </div>

        <!-- System Group -->
        <div class="space-y-1.5">
            <h3 class="text-xs font-semibold tracking-wider text-slate-400 uppercase px-3 mb-2">{{ __('System') }}</h3>
            
            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </x-slot>
                {{ __('Users') }}
            </x-admin.nav-item>

            <x-admin.nav-item route="#" disabled="true" badge="Soon">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </x-slot>
                {{ __('Settings') }}
            </x-admin.nav-item>
        </div>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold uppercase">
                {{ substr(Auth::user()->name, 0, 2) }}
            </span>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</span>
                <span class="text-xxs text-slate-400 truncate">{{ Auth::user()->email }}</span>
            </div>
        </div>
    </div>
</div>
