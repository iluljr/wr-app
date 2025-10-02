<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WR Portal') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-sky-50 via-white to-sky-100 text-slate-700">
        <div class="min-h-screen flex flex-col relative">
            <div class="absolute inset-0 -z-10 overflow-hidden">
                <div class="absolute -top-32 -left-10 h-80 w-80 bg-sky-200/50 rounded-full blur-3xl"></div>
                <div class="absolute top-40 right-[-6rem] h-96 w-96 bg-sky-300/30 rounded-full blur-3xl"></div>
            </div>

            @include('layouts.navigation')

            @isset($header)
                <header class="bg-transparent">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1">
                <div class="py-8 sm:py-10">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
