<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-55/30">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name', 'Dairy Management') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen">
            <!-- Sidebar Component -->
            <x-admin.sidebar />

            <!-- Main Content Area Wrapper -->
            <div class="flex flex-col flex-1 min-w-0">
                <!-- Top Header Component -->
                <x-admin.header />

                <!-- Main Section -->
                <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8 overflow-y-auto">
                    <!-- Page Header / Breadcrumbs inside content if provided, else fall back to $header -->
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
            </div>
        </div>
    </body>
</html>
