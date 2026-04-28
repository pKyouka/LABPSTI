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
    <body class="font-[Manrope] text-slate-900 antialiased">
        <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-100 overflow-hidden">
            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-cyan-200/80 via-blue-100/60 to-transparent"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -top-16 -right-16 h-64 w-64 rounded-full bg-cyan-300/40 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-blue-300/30 blur-3xl"></div>

            <div class="relative z-10">
                <a href="/">
                    <div class="h-20 w-20 rounded-3xl bg-gradient-to-br from-cyan-500 to-blue-700 text-white grid place-items-center shadow-lg shadow-cyan-300/50">
                        <span class="font-display text-3xl font-black tracking-tight">TI</span>
                    </div>
                </a>
            </div>

            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-6 bg-white/90 border border-white/70 shadow-xl shadow-slate-300/60 overflow-hidden sm:rounded-3xl backdrop-blur">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
