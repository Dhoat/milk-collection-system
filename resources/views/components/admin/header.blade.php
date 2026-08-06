<header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white border-b border-slate-200/80 shadow-sm shadow-slate-100/40 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" 
                class="p-2 text-slate-500 hover:text-slate-600 rounded-xl hover:bg-slate-50 focus:outline-none lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Breadcrumb / Location Bar -->
        <div class="hidden sm:block">
            <x-admin.breadcrumbs />
        </div>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center gap-4">
        <!-- Search Bar UI Placeholder -->
        <div class="relative hidden md:block w-64">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" 
                   placeholder="Search anything..." 
                   disabled
                   class="block w-full py-1.5 pl-9 pr-3 text-xs bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 cursor-not-allowed opacity-75">
        </div>

        <!-- Notifications UI Placeholder -->
        <div class="relative">
            <button class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-50/80 transition-colors focus:outline-none" 
                    title="Notifications (Placeholder only)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span class="absolute top-1 right-1 flex w-2 h-2 bg-indigo-600 rounded-full ring-2 ring-white"></span>
            </button>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" 
                    class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-50 focus:outline-none transition-colors">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-600 text-white text-xs font-semibold shadow shadow-indigo-200">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </span>
                <span class="hidden sm:block text-xs font-semibold text-slate-700">{{ Auth::user()->name }}</span>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" 
                     x-bind:class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 w-48 mt-2 origin-top-right bg-white border border-slate-200/80 rounded-xl shadow-xl shadow-slate-100/50 focus:outline-none divide-y divide-slate-100" 
                 style="display: none;">
                
                <div class="px-4 py-2.5">
                    <p class="text-xs text-slate-400">{{ __('Signed in as') }}</p>
                    <p class="text-xs font-bold text-slate-700 truncate">{{ Auth::user()->email }}</p>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-2 px-4 py-2 text-xs text-slate-650 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('My Profile') }}
                    </a>
                </div>

                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs text-red-650 hover:bg-red-50 hover:text-red-700 transition-colors">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
