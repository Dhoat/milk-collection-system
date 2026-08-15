<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F5F8FC]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name', 'Dairy Management') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased bg-[#F5F8FC] text-slate-900 selection:bg-[#005BAC] selection:text-white" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen">
            <!-- Sidebar Component -->
            <x-admin.sidebar />

            <!-- Main Content Area Wrapper -->
            <div class="flex flex-col flex-1 min-w-0 min-h-screen">
                <!-- Top Header Component -->
                <x-admin.header />

                <!-- Main Section -->
                <main class="flex-1 py-6 px-4 sm:px-6 lg:px-8 overflow-y-auto">
                    <!-- Page Header / Breadcrumbs inside content if provided -->
                    @if (isset($header))
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endif

                    <!-- Flash Messages component -->
                    <x-admin.flash-messages />

                    <!-- Main View Slot -->
                    <div>
                        {{ $slot }}
                    </div>
                </main>

                <!-- Footer -->
                <footer class="py-4 px-6 border-t border-slate-200/60 text-center text-xs text-slate-400 bg-white/50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 max-w-7xl mx-auto">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-600">Dairy Management</span>
                            <span class="text-slate-300">•</span>
                            <span>Milk Collection & Distribution System</span>
                        </div>
                        <p>© {{ date('Y') }} All Rights Reserved. Commercial Dairy ERP Edition.</p>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
