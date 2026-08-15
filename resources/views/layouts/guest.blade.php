<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Dairy Management System') }}</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
            .font-mono { font-family: 'JetBrains Mono', monospace; }
        </style>
    </head>
    <body class="h-full text-slate-900 antialiased selection:bg-[#005BAC] selection:text-white">
        <div class="min-h-screen flex flex-col lg:flex-row">
            <!-- Left SaaS Hero Branding Section (Visible on LG screens) -->
            <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-gradient-to-br from-[#002D5A] via-[#005BAC] to-[#003B73] p-12 flex-col justify-between relative overflow-hidden text-white shadow-2xl">
                <!-- Background Geometric Glow Pattern -->
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-sky-300/15 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Brand Header -->
                <div class="relative z-10 flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center shadow-inner">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black tracking-tight leading-none text-white">Dairy Management</h1>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-blue-200 mt-1">Milk Collection & Distribution System</p>
                    </div>
                </div>

                <!-- Hero Content & Highlights -->
                <div class="relative z-10 my-auto py-12 space-y-8">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-bold text-blue-100">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Commercial Dairy Enterprise Platform
                    </div>

                    <h2 class="text-3xl xl:text-4xl font-extrabold tracking-tight leading-tight text-white">
                        Streamlining Raw Milk Procurement & Supply Chain Distribution
                    </h2>

                    <p class="text-sm text-blue-100/90 leading-relaxed font-normal max-w-lg">
                        Empowering milk centers, village collection routes, processing inventory, and retail outlet dispatches with real-time FAT/SNF ledger accuracy and automated stock reconciliation.
                    </p>

                    <!-- Feature Badges Grid -->
                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <div class="p-4 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 space-y-1">
                            <div class="text-xl font-mono font-black text-white">100%</div>
                            <div class="text-xs font-semibold text-blue-200">Collection Transparency</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 space-y-1">
                            <div class="text-xl font-mono font-black text-white">Real-Time</div>
                            <div class="text-xs font-semibold text-blue-200">Stock & Dispatch Audit</div>
                        </div>
                    </div>
                </div>

                <!-- Footer Copyright -->
                <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-blue-200 font-medium">
                    <span>© {{ date('Y') }} Dairy Management System.</span>
                    <span>Commercial ERP Edition</span>
                </div>
            </div>

            <!-- Right Content Container -->
            <div class="w-full lg:w-7/12 xl:w-1/2 flex items-center justify-center p-6 sm:p-12 lg:p-16 bg-slate-50">
                <div class="w-full max-w-md space-y-6">
                    <!-- Mobile Brand Header (Visible on small screens) -->
                    <div class="lg:hidden flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-[#005BAC] text-white flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-base font-extrabold text-slate-900 leading-tight">Dairy Management</h1>
                            <p class="text-[10px] font-bold text-[#005BAC] uppercase tracking-wider">Milk Collection & Distribution</p>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
