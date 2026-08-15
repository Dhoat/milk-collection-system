<header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white border-b border-slate-200/80 shadow-sm sm:px-6 lg:px-8">
    <div class="flex items-center gap-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" 
                class="p-2 text-slate-600 hover:text-[#005BAC] rounded-xl hover:bg-slate-100 focus:outline-none transition-colors lg:hidden"
                aria-label="Toggle Navigation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Breadcrumb / Location Bar -->
        <div class="hidden sm:block">
            <x-admin.breadcrumbs />
        </div>
    </div>

    <!-- Right Side Actions & User Info -->
    <div class="flex items-center gap-3 sm:gap-4">
        <!-- Current Shift & Date Badge -->
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100/80 border border-slate-200/60 text-xs font-semibold text-slate-700">
            <span class="inline-flex items-center gap-1.5 text-[#005BAC]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ now()->format('D, M j, Y') }}
            </span>
            <span class="text-slate-300">•</span>
            <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-bold tracking-wider {{ now()->hour < 14 ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800' }}">
                {{ now()->hour < 14 ? __('Morning Shift') : __('Evening Shift') }}
            </span>
        </div>

        <!-- Role Badge -->
        @php
            $roleName = Auth::user()->role ?? 'user';
            $roleLabel = ucwords(str_replace('_', ' ', $roleName));
            $roleColorClass = match($roleName) {
                'super_admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                'manager' => 'bg-blue-100 text-blue-800 border-blue-200',
                'collection_staff' => 'bg-teal-100 text-teal-800 border-teal-200',
                'center_staff' => 'bg-amber-100 text-amber-800 border-amber-200',
                default => 'bg-slate-100 text-slate-800 border-slate-200',
            };
        @endphp
        <span class="hidden lg:inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-lg border {{ $roleColorClass }}">
            {{ $roleLabel }}
        </span>

        <!-- Profile Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" 
                    class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-100/80 focus:outline-none transition-colors">
                <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-[#005BAC] text-white text-xs font-bold shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </span>
                <div class="hidden sm:flex flex-col text-left leading-tight">
                    <span class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-slate-500 truncate max-w-[120px]">{{ Auth::user()->email }}</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" 
                     x-bind:class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                 class="absolute right-0 w-52 mt-2 origin-top-right bg-white border border-slate-200 rounded-2xl shadow-xl focus:outline-none divide-y divide-slate-100" 
                 style="display: none;">
                
                <div class="px-4 py-3">
                    <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">{{ __('Signed in as') }}</p>
                    <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                    <p class="text-[10px] text-[#005BAC] font-semibold mt-0.5">{{ $roleLabel }}</p>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-[#005BAC] transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('My Profile') }}
                    </a>

                    @can('manage-settings')
                        <a href="{{ route('settings.index') }}" 
                           class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-[#005BAC] transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ __('System Settings') }}
                        </a>
                    @endcan
                </div>

                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center gap-2.5 w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
