<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&family=space-grotesk:500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-[Manrope] antialiased text-slate-900">
        <div class="relative min-h-screen overflow-x-clip bg-slate-100">
            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-gradient-to-b from-cyan-100 via-blue-50 to-transparent"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -top-20 -right-24 h-72 w-72 rounded-full bg-cyan-200/50 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute top-48 -left-24 h-64 w-64 rounded-full bg-blue-200/40 blur-3xl"></div>
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="relative z-10">
                    <div class="panel-shell">
                        <div class="panel-card mt-2 mb-1 py-5 px-6 sm:px-8">
                        {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative z-10 pb-10">
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
