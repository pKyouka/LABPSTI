<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LABPSTI') }}</title>
        @include('partials.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-[#1F1F1F] antialiased bg-[#F3F3F3]">
        <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Mica background -->
            <div aria-hidden="true" class="fixed inset-0 -z-10 fluent-mica"></div>

            <!-- Accent top bar -->
            <div aria-hidden="true" class="fixed top-0 inset-x-0 h-1 bg-accent z-10"></div>

            <!-- Logo -->
            <div class="relative z-10 mb-6">
                <a href="/">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-accent to-accent-800 text-white grid place-items-center shadow-fluent-md">
                        <span class="text-2xl font-bold tracking-tight">TI</span>
                    </div>
                </a>
            </div>

            <!-- Auth Card -->
            <div class="relative z-10 w-full sm:max-w-md px-8 py-8 bg-white rounded-2xl border border-neutral-200 shadow-fluent-lg">
                <div class="mb-6 text-center">
                    <h1 class="text-xl font-semibold text-neutral-800">{{ config('app.name', 'LabPSTI') }}</h1>
                    <p class="text-sm text-neutral-500 mt-1">Sistem Informasi Laboratorium</p>
                </div>
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="relative z-10 mt-8 text-xs text-neutral-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'LabPSTI') }}
            </p>
        </div>
    </body>
</html>