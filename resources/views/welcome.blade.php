<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Milk Center - Farm Fresh Milk, Quality Checked Dairy Products, and Complete Enterprise Dairy Management System.">
    <title>{{ config('app.name', 'Milk Center') }} | Fresh Milk & Premium Dairy Management</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'sans-serif'],
                            heading: ['Outfit', 'sans-serif'],
                        },
                        colors: {
                            dairy: {
                                50: '#eff6ff',
                                100: '#dbeafe',
                                200: '#bfdbfe',
                                300: '#93c5fd',
                                400: '#60a5fa',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#005BAC',
                                900: '#003B73',
                                950: '#091E42',
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #091E42 0%, #003B73 35%, #005BAC 75%, #0284C7 100%);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .glass-badge {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
    </style>
</head>
<body class="bg-stone-50 text-slate-800 antialiased selection:bg-[#005BAC] selection:text-white" x-data="{ mobileMenu: false, modalOpen: false, modalProduct: {} }">

    <!-- ========================================== -->
    <!-- NAVBAR                                     -->
    <!-- ========================================== -->
    <header class="sticky top-0 z-50 glass-nav border-b border-blue-900/10 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Left: Milk Center Logo + Subtitle -->
                <a href="#top" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#003B73] to-[#005BAC] flex items-center justify-center text-white shadow-lg shadow-blue-900/20 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 01-1.023-.547M19.428 15.428A2 2 0 0121 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 011.572-1.952m14.856 0A6 6 0 0017 12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v7a6 6 0 00-2.428 3.428M12 3v9" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xl font-heading font-extrabold tracking-tight text-slate-900 leading-none">
                            Milk<span class="text-[#005BAC]">Center</span>
                        </span>
                        <span class="text-[10px] font-bold tracking-widest text-[#003B73] uppercase">DAIRY MANAGEMENT</span>
                    </div>
                </a>

                <!-- Middle: Navigation Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#top" class="text-sm font-semibold text-slate-700 hover:text-[#005BAC] transition-colors">Home</a>
                    <a href="#about" class="text-sm font-semibold text-slate-700 hover:text-[#005BAC] transition-colors">About</a>
                    <a href="#products" class="text-sm font-semibold text-slate-700 hover:text-[#005BAC] transition-colors">Products</a>
                    <a href="#process" class="text-sm font-semibold text-slate-700 hover:text-[#005BAC] transition-colors">Our Process</a>
                    <a href="#contact" class="text-sm font-semibold text-slate-700 hover:text-[#005BAC] transition-colors">Contact</a>
                </nav>

                <!-- Right: Auth Buttons (Standard Laravel Routes) -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm bg-[#005BAC] hover:bg-[#003B73] text-white shadow-md shadow-blue-600/20 transition-all transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Admin Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 hover:text-[#005BAC] transition-colors">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl font-semibold text-sm bg-slate-900 hover:bg-[#005BAC] text-white shadow-md shadow-slate-900/10 transition-all transform hover:-translate-y-0.5">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenu = !mobileMenu" type="button" class="p-2.5 rounded-xl text-slate-700 hover:text-[#005BAC] hover:bg-blue-50 focus:outline-none transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Navigation Menu Drawer -->
        <div x-show="mobileMenu" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-3">
            <a @click="mobileMenu = false" href="#top" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-[#005BAC]">Home</a>
            <a @click="mobileMenu = false" href="#about" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-[#005BAC]">About</a>
            <a @click="mobileMenu = false" href="#products" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-[#005BAC]">Products</a>
            <a @click="mobileMenu = false" href="#process" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-[#005BAC]">Our Process</a>
            <a @click="mobileMenu = false" href="#contact" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-blue-50 hover:text-[#005BAC]">Contact</a>
            
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center px-5 py-3 rounded-xl font-semibold text-sm bg-[#005BAC] text-white shadow-md">
                        Admin Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center px-5 py-2.5 rounded-xl font-semibold text-sm bg-slate-100 text-slate-800 hover:bg-slate-200">
                        Login
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="w-full text-center px-5 py-2.5 rounded-xl font-semibold text-sm bg-[#005BAC] text-white hover:bg-[#003B73]">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- HERO SECTION                               -->
    <!-- ========================================== -->
    <section id="top" class="relative overflow-hidden hero-gradient text-white pt-12 pb-24 lg:pt-20 lg:pb-36">
        <!-- Ambient Graphic Orbs -->
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 1000 1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="150" cy="150" r="280" fill="white" filter="blur(80px)" opacity="0.3"/>
                <circle cx="850" cy="650" r="300" fill="#38bdf8" filter="blur(90px)" opacity="0.4"/>
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    
                    <!-- Small Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-badge border border-white/20 text-blue-100 text-xs sm:text-sm font-semibold tracking-wide shadow-inner">
                        <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
                        100% Pure · Farm-Fresh · Quality Tested
                    </div>

                    <!-- Main Heading -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-extrabold tracking-tight text-white leading-tight">
                        Fresh Milk. <br class="hidden sm:inline"/>
                        <span class="text-sky-200">Trusted Quality.</span> <br class="hidden sm:inline"/>
                        Better Dairy.
                    </h1>

                    <!-- Supporting Text -->
                    <p class="text-lg sm:text-xl text-blue-50 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                        From local farmers to our milk center, we manage every step with quality, transparency and care.
                    </p>

                    <!-- Hero Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                        <a href="#products" class="w-full sm:w-auto px-8 py-4 rounded-2xl font-bold text-slate-900 bg-white hover:bg-blue-50 shadow-xl shadow-blue-950/20 transition-all transform hover:-translate-y-1 text-center">
                            Explore Our Products
                        </a>
                        <a href="#process" class="w-full sm:w-auto px-8 py-4 rounded-2xl font-semibold text-white bg-[#003B73]/60 hover:bg-[#003B73]/90 border border-sky-400/30 backdrop-blur-md transition-all transform hover:-translate-y-1 text-center">
                            Learn About Our Process
                        </a>
                    </div>
                </div>

                <!-- Right Visual: High-Quality Dairy Illustration -->
                <div class="lg:col-span-5 relative flex justify-center">
                    <div class="relative w-full max-w-md aspect-square rounded-3xl bg-gradient-to-b from-white/25 to-white/10 border border-white/20 backdrop-blur-xl p-6 shadow-2xl flex flex-col justify-between overflow-hidden">
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 bg-[#091E42]/60 backdrop-blur-md px-3 py-1.5 rounded-xl border border-sky-400/30">
                                <svg class="w-5 h-5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-semibold text-blue-100">ISO Standard Quality</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-blue-200 bg-white/10 px-2.5 py-1 rounded-lg">Milk Hub #01</span>
                        </div>

                        <!-- Central Dairy Milk SVG Visual -->
                        <div class="my-auto py-6 flex flex-col items-center justify-center text-center space-y-4">
                            <div class="relative">
                                <div class="absolute -inset-4 bg-white/25 rounded-full blur-xl"></div>
                                <div class="relative w-36 h-36 rounded-3xl bg-white text-[#005BAC] flex items-center justify-center shadow-2xl transform hover:scale-105 transition-transform">
                                    <svg class="w-24 h-24 text-[#005BAC]" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Farm Fresh Milk Can Graphic -->
                                        <path d="M22 16H42V10C42 8.89543 41.1046 8 40 8H24C22.8954 8 22 8.89543 22 10V16Z" fill="#005BAC"/>
                                        <path d="M18 20H46L50 54C50 56.2091 48.2091 58 46 58H18C15.7909 58 14 56.2091 14 54L18 20Z" fill="#E0F2FE" stroke="#003B73" stroke-width="3"/>
                                        <path d="M24 28C24 28 28 34 32 34C36 34 40 28 40 28" stroke="#003B73" stroke-width="3" stroke-linecap="round"/>
                                        <circle cx="32" cy="44" r="5" fill="#003B73"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-heading font-bold text-white">Central Milk Hub</h3>
                                <p class="text-xs text-blue-100 font-medium">Digital Collection & Stock Processing</p>
                            </div>
                        </div>

                        <!-- Live Status Pill -->
                        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 text-slate-800 shadow-lg flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#005BAC] flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs text-slate-500 font-medium">Daily Milk Receiving</p>
                                    <p class="text-sm font-bold text-slate-900">100% Tested & Verified</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-[#003B73]">
                                Fresh Yield
                            </span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- TRUST / QUALITY STATS (Floating Bar)      -->
    <!-- ========================================== -->
    <section class="relative -mt-12 z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-900/5 border border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                
                <div class="flex items-center gap-4 justify-center md:justify-start pt-2 md:pt-0 md:pl-4 first:pl-0">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#005BAC] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-heading font-extrabold text-slate-900">100%</p>
                        <p class="text-xs font-semibold text-slate-500">Pure & Quality Checked</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 justify-center md:justify-start pt-4 md:pt-0 md:pl-6">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-heading font-extrabold text-slate-900">4°C</p>
                        <p class="text-xs font-semibold text-slate-500">Cold Chain Storage</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 justify-center md:justify-start pt-4 md:pt-0 md:pl-6">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-heading font-extrabold text-slate-900">Daily</p>
                        <p class="text-xs font-semibold text-slate-500">Fresh Farmer Collection</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- BUSINESS STATISTICS (4 Cards)              -->
    <!-- ========================================== -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                
                <!-- Farmers -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-md shadow-slate-900/5 hover:border-blue-200 transition-all text-center sm:text-left space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#005BAC] flex items-center justify-center mx-auto sm:mx-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900">
                        {{ number_format($stats['farmers']) }}+
                    </p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">Registered Farmers</p>
                </div>

                <!-- Villages Network -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-md shadow-slate-900/5 hover:border-sky-200 transition-all text-center sm:text-left space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto sm:mx-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <p class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900">
                        {{ number_format($stats['villages']) }}+
                    </p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">Villages Network</p>
                </div>

                <!-- Milk Collected -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-md shadow-slate-900/5 hover:border-blue-200 transition-all text-center sm:text-left space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#005BAC] flex items-center justify-center mx-auto sm:mx-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 01-1.023-.547M19.428 15.428A2 2 0 0121 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 011.572-1.952m14.856 0A6 6 0 0017 12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v7a6 6 0 00-2.428 3.428M12 3v9" />
                        </svg>
                    </div>
                    <p class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900">
                        {{ number_format($stats['milk_collected'], 0) }}L+
                    </p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">Milk Collected (Liters)</p>
                </div>

                <!-- Dairy Products -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-md shadow-slate-900/5 hover:border-indigo-200 transition-all text-center sm:text-left space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto sm:mx-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <p class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900">
                        {{ number_format($stats['products']) }}+
                    </p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">Fresh Dairy Products</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- WHY CHOOSE OUR MILK CENTER                 -->
    <!-- ========================================== -->
    <section class="py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="inline-block px-3.5 py-1.5 rounded-full bg-blue-100 text-[#003B73] text-xs font-bold tracking-wide uppercase">
                    Core Pillars
                </span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900">
                    Why Choose Our Milk Center
                </h2>
                <p class="text-slate-600 text-base sm:text-lg">
                    We bring digital precision, transparency, and strict hygiene to every step of milk procurement and distribution.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <!-- Card 1 -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-lg shadow-slate-900/5 hover:shadow-xl hover:border-blue-200 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-[#005BAC] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-3">Quality Checked Milk</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Every batch of milk undergoes quality checks before entering our center.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-lg shadow-slate-900/5 hover:shadow-xl hover:border-sky-200 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-3">Direct Farmer Collection</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        We work directly with local farmers and village collection points.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-lg shadow-slate-900/5 hover:shadow-xl hover:border-blue-200 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-[#005BAC] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 01-1.023-.547M19.428 15.428A2 2 0 0121 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 011.572-1.952m14.856 0A6 6 0 0017 12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v7a6 6 0 00-2.428 3.428M12 3v9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-3">Hygienic Processing</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Milk is handled through controlled and hygienic processes.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-lg shadow-slate-900/5 hover:shadow-xl hover:border-blue-200 transition-all group">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-[#003B73] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-3">Transparent Management</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Complete visibility of collections, receiving, stock and distribution.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- OUR DAIRY PRODUCTS                         -->
    <!-- ========================================== -->
    <section id="products" class="py-20 bg-blue-900/5 border-y border-blue-900/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="inline-block px-3.5 py-1.5 rounded-full bg-blue-100 text-[#003B73] text-xs font-bold tracking-wide uppercase">
                    Product Showcase
                </span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900">
                    Our Dairy Products
                </h2>
                <p class="text-slate-600 text-base sm:text-lg">
                    Fresh dairy products made with quality milk and careful processing.
                </p>
            </div>

            <!-- Product Showcase Grid (Dynamic Loading from DB / Fallback) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                @foreach ($products as $product)
                    <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-md hover:shadow-2xl transition-all group flex flex-col justify-between">
                        <div>
                            <!-- Header Graphic Banner -->
                            <div class="h-48 bg-gradient-to-tr from-[#003B73] to-[#005BAC] p-6 flex items-center justify-center relative overflow-hidden">
                                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-lg"></div>
                                
                                <!-- Product Icon Illustration -->
                                <div class="w-24 h-24 rounded-2xl bg-white/90 backdrop-blur-md flex items-center justify-center text-[#005BAC] shadow-lg group-hover:scale-110 transition-transform">
                                    @if (($product['icon_type'] ?? '') === 'jar')
                                        <svg class="w-14 h-14 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    @elseif (($product['icon_type'] ?? '') === 'block')
                                        <svg class="w-14 h-14 text-sky-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h16M10 4v16" />
                                        </svg>
                                    @elseif (($product['icon_type'] ?? '') === 'bowl')
                                        <svg class="w-14 h-14 text-sky-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    @elseif (($product['icon_type'] ?? '') === 'glass')
                                        <svg class="w-14 h-14 text-[#003B73]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    @else
                                        <svg class="w-14 h-14 text-[#005BAC]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 01-1.023-.547M19.428 15.428A2 2 0 0121 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 011.572-1.952m14.856 0A6 6 0 0017 12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v7a6 6 0 00-2.428 3.428M12 3v9" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="absolute top-4 right-4 bg-white/90 text-slate-800 text-xs font-bold px-3 py-1 rounded-full shadow">
                                    {{ $product['category'] ?? 'Fresh Dairy' }}
                                </span>
                            </div>

                            <!-- Body Text -->
                            <div class="p-6 space-y-3">
                                <span class="inline-block text-xs font-bold text-[#003B73] uppercase tracking-wide">
                                    {{ $product['badge'] ?? 'Milk Center Pure' }}
                                </span>
                                <h3 class="text-xl font-heading font-bold text-slate-900">{{ $product['name'] }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">
                                    {{ $product['description'] }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Button (Informational ONLY) -->
                        <div class="px-6 pb-6 pt-2 border-t border-slate-50">
                            <button @click="modalProduct = {{ json_encode($product) }}; modalOpen = true" type="button" class="w-full py-3 rounded-xl font-semibold text-sm bg-slate-900 hover:bg-[#005BAC] text-white transition-colors text-center">
                                View Product
                            </button>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- OUR DAIRY PROCESS FLOW                     -->
    <!-- ========================================== -->
    <section id="process" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="inline-block px-3.5 py-1.5 rounded-full bg-blue-100 text-[#003B73] text-xs font-bold tracking-wide uppercase">
                    7-Step Supply Chain
                </span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900">
                    Our Dairy Process Flow
                </h2>
                <p class="text-slate-600 text-base sm:text-lg">
                    A transparent step-by-step workflow connecting village farmers directly to retail partner shops.
                </p>
            </div>

            <!-- Responsive Step Timeline Flow -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4 relative">
                
                <!-- Step 01 -->
                <div class="bg-stone-50 rounded-2xl p-5 border border-slate-100 text-center space-y-3 relative group hover:border-[#005BAC] hover:bg-blue-50/50 transition-all">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white text-xs font-extrabold flex items-center justify-center mx-auto shadow-md">01</span>
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-[#005BAC] flex items-center justify-center mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <h3 class="text-sm font-heading font-bold text-slate-900">Farmers</h3>
                    <p class="text-xs text-slate-500">Local village farmers produce fresh morning & evening milk yields.</p>
                </div>

                <!-- Step 02 -->
                <div class="bg-stone-50 rounded-2xl p-5 border border-slate-100 text-center space-y-3 relative group hover:border-[#005BAC] hover:bg-blue-50/50 transition-all">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white text-xs font-extrabold flex items-center justify-center mx-auto shadow-md">02</span>
                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h3 class="text-sm font-heading font-bold text-slate-900">Village Milk Collection</h3>
                    <p class="text-xs text-slate-500">Milk quantity, shift, and farmer logs recorded at collection points.</p>
                </div>

                <!-- Step 03 -->
                <div class="bg-stone-50 rounded-2xl p-5 border border-slate-100 text-center space-y-3 relative group hover:border-[#005BAC] hover:bg-blue-50/50 transition-all">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white text-xs font-extrabold flex items-center justify-center mx-auto shadow-md">03</span>
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-[#005BAC] flex items-center justify-center mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-sm font-heading font-bold text-slate-900">Quality Checking</h3>
                    <p class="text-xs text-slate-500">Fat, SNF, temperature, and purity tested before approval.</p>
                </div>

                <!-- Step 04 -->
                <div class="bg-stone-50 rounded-2xl p-5 border border-slate-100 text-center space-y-3 relative group hover:border-[#005BAC] hover:bg-blue-50/50 transition-all">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white text-xs font-extrabold flex items-center justify-center mx-auto shadow-md">04</span>
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-[#003B73] flex items-center justify-center mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8" /></svg>
                    </div>
                    <h3 class="text-sm font-heading font-bold text-slate-900">Main Milk Center</h3>
                    <p class="text-xs text-slate-500">Central receiving hub logs village collection transfers.</p>
                </div>

                <!-- Step 05 -->
                <div class="bg-stone-50 rounded-2xl p-5 border border-slate-100 text-center space-y-3 relative group hover:border-[#005BAC] hover:bg-blue-50/50 transition-all">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white text-xs font-extrabold flex items-center justify-center mx-auto shadow-md">05</span>
                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h3 class="text-sm font-heading font-bold text-slate-900">Milk Stock / Processing</h3>
                    <p class="text-xs text-slate-500">Chilling to 4°C, HTST pasteurization, & stock updating.</p>
                </div>

                <!-- Step 06 -->
                <div class="bg-stone-50 rounded-2xl p-5 border border-slate-100 text-center space-y-3 relative group hover:border-[#005BAC] hover:bg-blue-50/50 transition-all">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white text-xs font-extrabold flex items-center justify-center mx-auto shadow-md">06</span>
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <h3 class="text-sm font-heading font-bold text-slate-900">Dairy Products</h3>
                    <p class="text-xs text-slate-500">Manufacturing Desi Ghee, Paneer, Dahi, Lassi & Milk.</p>
                </div>

                <!-- Step 07 -->
                <div class="bg-stone-50 rounded-2xl p-5 border border-slate-100 text-center space-y-3 relative group hover:border-[#005BAC] hover:bg-blue-50/50 transition-all">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white text-xs font-extrabold flex items-center justify-center mx-auto shadow-md">07</span>
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-[#005BAC] flex items-center justify-center mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    </div>
                    <h3 class="text-sm font-heading font-bold text-slate-900">Shops & Distribution</h3>
                    <p class="text-xs text-slate-500">Express delivery dispatch to authorized retail outlets.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- ABOUT THE SYSTEM (Complete Dairy Management) -->
    <!-- ========================================== -->
    <section id="about" class="py-20 lg:py-28 bg-slate-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="inline-block px-3.5 py-1.5 rounded-full bg-blue-500/20 border border-sky-400/30 text-sky-300 text-xs font-bold tracking-wide uppercase">
                    Enterprise Platform
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-heading font-extrabold text-white">
                    Complete Dairy Management
                </h2>
                <p class="text-slate-300 text-base sm:text-lg">
                    Our platform manages every module of dairy operations with role-based security, automated ledgers, and real-time reports.
                </p>
            </div>

            <!-- Grid of Managed System Modules -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">01</div>
                    <h3 class="text-lg font-heading font-bold text-white">Village Operations</h3>
                    <p class="text-xs text-slate-400">Manage village centers, local supervisor assignments, and collection routes.</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">02</div>
                    <h3 class="text-lg font-heading font-bold text-white">Farmer Management</h3>
                    <p class="text-xs text-slate-400">Complete profiles, rate charts, shift records, and payment calculations per farmer.</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">03</div>
                    <h3 class="text-lg font-heading font-bold text-white">Milk Collection</h3>
                    <p class="text-xs text-slate-400">Morning and evening shift milk logging with Fat, SNF, and rate entry.</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">04</div>
                    <h3 class="text-lg font-heading font-bold text-white">Main Center Receiving</h3>
                    <p class="text-xs text-slate-400">Receiving pooled milk transfers from villages with quality re-verification.</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">05</div>
                    <h3 class="text-lg font-heading font-bold text-white">Milk Stock Control</h3>
                    <p class="text-xs text-slate-400">Automated milk inventory updates, pasteurization logs, and processing dispatches.</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">06</div>
                    <h3 class="text-lg font-heading font-bold text-white">Dairy Products</h3>
                    <p class="text-xs text-slate-400">Product catalog, unit pricing, batch manufacturing, and stock availability.</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">07</div>
                    <h3 class="text-lg font-heading font-bold text-white">Shop Management</h3>
                    <p class="text-xs text-slate-400">Authorized retail shop profiles, outlet locations, and status toggles.</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">08</div>
                    <h3 class="text-lg font-heading font-bold text-white">Shop Orders & Delivery</h3>
                    <p class="text-xs text-slate-400">Order processing, status tracking (pending, confirmed, delivered), and dispatch logs.</p>
                </div>

                <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 space-y-3 hover:border-blue-500/40 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-sky-400 flex items-center justify-center font-bold">09</div>
                    <h3 class="text-lg font-heading font-bold text-white">Daily & Monthly Reports</h3>
                    <p class="text-xs text-slate-400">Automated executive summaries for daily collections, monthly payouts, and sales.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- CONTACT SECTION                            -->
    <!-- ========================================== -->
    <section id="contact" class="py-20 lg:py-28 bg-stone-50" x-data="{ submitted: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="inline-block px-3.5 py-1.5 rounded-full bg-blue-100 text-[#003B73] text-xs font-bold tracking-wide uppercase">
                    Connect With Us
                </span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900">
                    Contact Our Milk Center
                </h2>
                <p class="text-slate-600 text-base sm:text-lg">
                    Questions about farmer registration, shop partnerships, or bulk product inquiries? Reach out today.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                
                <!-- Contact Info -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-start gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#005BAC] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-heading font-bold text-slate-900">Head Office</h3>
                            <p class="text-slate-600 text-sm mt-1">Plot 45, Main Dairy Village Road, Central Milk Center Hub</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-start gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-heading font-bold text-slate-900">Phone Support</h3>
                            <p class="text-slate-600 text-sm mt-1">+92 (300) 123-4567 / +92 (42) 3555-7890</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-start gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#005BAC] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-heading font-bold text-slate-900">Email Address</h3>
                            <p class="text-slate-600 text-sm mt-1">info@milkcenter.com / support@milkcenter.com</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-start gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-heading font-bold text-slate-900">Operating Hours</h3>
                            <p class="text-slate-600 text-sm mt-1">Monday – Sunday: 5:00 AM – 9:00 PM (Morning & Evening Shifts)</p>
                        </div>
                    </div>

                </div>

                <!-- Inquiry Form -->
                <div class="lg:col-span-7 bg-white rounded-3xl p-8 border border-slate-100 shadow-xl shadow-slate-900/5">
                    <h3 class="text-2xl font-heading font-bold text-slate-900 mb-2">Send an Inquiry</h3>
                    <p class="text-slate-500 text-sm mb-6">Fill in your details and our Milk Center management team will get back to you.</p>

                    <div x-show="submitted" class="p-4 mb-6 rounded-2xl bg-blue-50 border border-blue-200 text-[#003B73] text-sm font-semibold flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#005BAC]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Thank you for reaching out! Your inquiry has been received.
                    </div>

                    <form @submit.prevent="submitted = true" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Full Name</label>
                                <input type="text" required placeholder="e.g. Ahmad Khan" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-blue-200 outline-none text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Phone Number</label>
                                <input type="tel" required placeholder="e.g. 0300 1234567" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-blue-200 outline-none text-sm transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Inquiry Type</label>
                            <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-blue-200 outline-none text-sm transition-all">
                                <option>Farmer Registration Inquiry</option>
                                <option>Retail Shop Outlet Partnership</option>
                                <option>Bulk Dairy Supply Query</option>
                                <option>General Inquiry</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Message</label>
                            <textarea rows="4" required placeholder="Enter your query or message..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-blue-200 outline-none text-sm transition-all"></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 rounded-2xl font-bold text-sm bg-[#005BAC] hover:bg-[#003B73] text-white shadow-lg shadow-blue-600/20 transition-all">
                            Send Message
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- FOOTER                                     -->
    <!-- ========================================== -->
    <footer class="bg-slate-950 text-slate-400 py-16 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Brand Info -->
                <div class="space-y-4 md:col-span-2">
                    <a href="#top" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#005BAC] flex items-center justify-center text-white font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 01-1.023-.547M19.428 15.428A2 2 0 0121 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 011.572-1.952m14.856 0A6 6 0 0017 12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v7a6 6 0 00-2.428 3.428M12 3v9" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xl font-heading font-extrabold text-white">Milk<span class="text-[#005BAC]">Center</span></span>
                            <span class="block text-[9px] font-bold text-sky-400 uppercase tracking-widest">DAIRY MANAGEMENT</span>
                        </div>
                    </a>
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed">
                        A modern enterprise dairy management platform powering farmer collection, quality testing, milk receiving, stock control, and distribution.
                    </p>
                </div>

                <!-- Navigation Links -->
                <div class="space-y-3">
                    <p class="text-sm font-heading font-bold text-white uppercase tracking-wider">Navigation</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#top" class="hover:text-sky-400 transition-colors">Home</a></li>
                        <li><a href="#about" class="hover:text-sky-400 transition-colors">About Us</a></li>
                        <li><a href="#products" class="hover:text-sky-400 transition-colors">Products</a></li>
                        <li><a href="#process" class="hover:text-sky-400 transition-colors">Our Process</a></li>
                        <li><a href="#contact" class="hover:text-sky-400 transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Auth & Portal -->
                <div class="space-y-3">
                    <p class="text-sm font-heading font-bold text-white uppercase tracking-wider">System Portal</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('login') }}" class="hover:text-sky-400 transition-colors">Login</a></li>
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="hover:text-sky-400 transition-colors">Register</a></li>
                        @endif
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-sky-400 font-semibold hover:underline">Admin Dashboard →</a></li>
                        @endauth
                    </ul>
                </div>

            </div>

            <div class="pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>© {{ date('Y') }} Milk Center Dairy Management System. All rights reserved.</p>
                <p class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#005BAC]"></span>
                    Operational & Quality Monitored System
                </p>
            </div>

        </div>
    </footer>

    <!-- ========================================== -->
    <!-- INFORMATIONAL PRODUCT MODAL                -->
    <!-- ========================================== -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div @click.away="modalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative space-y-6">
            
            <!-- Close Button -->
            <button @click="modalOpen = false" class="absolute top-5 right-5 p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="space-y-2">
                <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-[#003B73] text-xs font-bold" x-text="modalProduct.badge"></span>
                <h3 class="text-2xl font-heading font-bold text-slate-900" x-text="modalProduct.name"></h3>
                <p class="text-slate-600 text-sm leading-relaxed" x-text="modalProduct.description"></p>
            </div>

            <!-- Product Specs Table -->
            <div class="bg-stone-50 rounded-2xl p-4 border border-slate-100 space-y-3">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Product Specifications & Purity</p>
                <div class="divide-y divide-slate-200/60">
                    <template x-for="spec in (modalProduct.specs || [])" :key="spec.label">
                        <div class="py-2.5 flex items-center justify-between text-sm">
                            <span class="text-slate-600 font-medium" x-text="spec.label"></span>
                            <span class="font-bold text-slate-900" x-text="spec.val"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Informational Notice (No Cart / Checkout) -->
            <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200/60 text-[#003B73] text-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-[#005BAC] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Informational showcase item. Products are distributed directly through authorized retail shops & milk center outlets.</span>
            </div>

            <!-- Action Button -->
            <button @click="modalOpen = false" class="w-full py-3.5 rounded-2xl font-bold text-sm bg-slate-900 text-white hover:bg-[#005BAC] transition-colors">
                Close Information
            </button>

        </div>
    </div>

</body>
</html>
