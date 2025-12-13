<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laundry System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <!-- Full page loader -->
        <div id="page-loader" class="fixed inset-0 bg-white bg-opacity-90 z-50 flex items-center justify-center">
            <div class="text-center">
                <!-- Spinner -->
                <div class="inline-block animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-600"></div>
                
                <!-- Text -->
                <p class="mt-4 text-lg font-semibold text-gray-700">Loading...</p>
                <p class="mt-2 text-sm text-gray-500">Please wait a moment</p>
                
                <!-- Optional progress bar -->
                <div class="mt-6 w-64 h-2 bg-gray-200 rounded-full overflow-hidden mx-auto">
                    <div class="h-full bg-blue-500 animate-pulse" style="width: 60%"></div>
                </div>
            </div>
        </div>

        <div class="min-h-screen bg-gray-100">
            @if (auth()->check() && auth()->user()->role === 'admin')
                @include('partials.navigation-menu-admin')
            @else
                @include('partials.navigation-menu-user')
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @stack('scripts')

        @livewireScripts
    </body>
</html>
