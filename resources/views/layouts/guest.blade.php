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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="relative min-h-screen bg-gradient-to-br from-sky-50 via-white to-sky-100 text-slate-700">
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -left-24 h-96 w-96 rounded-full bg-sky-200/50 blur-3xl"></div>
                <div class="absolute top-48 -right-20 h-96 w-96 rounded-full bg-sky-300/40 blur-3xl"></div>
                <div class="absolute bottom-[-6rem] left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-emerald-200/25 blur-3xl"></div>
            </div>

            <div class="relative flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
                <div class="w-full max-w-6xl overflow-hidden rounded-3xl bg-white/85 shadow-2xl backdrop-blur">
                    <div class="grid gap-0 lg:grid-cols-[1.1fr,1fr]">
                        <div class="hidden flex-col justify-between bg-sky-600/85 p-10 text-white lg:flex">
                            <div>
                                <span class="inline-flex items-center rounded-full bg-white/15 px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-sky-100">WR Platform</span>
                                <h2 class="mt-8 text-3xl font-semibold leading-tight text-white sm:text-4xl">Monitor aktivitas dan hasil kerja tim dalam satu tempat</h2>
                                <p class="mt-4 max-w-md text-sm text-sky-100/90 sm:text-base">Gabungkan tiket, task, dan aktivitas manual untuk laporan kerja yang konsisten dan rapi setiap hari.</p>
                            </div>
                            <dl class="mt-10 grid grid-cols-1 gap-6 text-sm text-sky-100/90 sm:grid-cols-2">
                                <div>
                                    <dt class="font-semibold text-white">Integrasi Data</dt>
                                    <dd class="mt-1">Import tiket & task, tambahkan aktivitas manual, dan hasilkan WR secara otomatis.</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-white">Profil Dinamis</dt>
                                    <dd class="mt-1">Setiap user mengatur identitas proyek sendiri agar header WR selalu akurat.</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-white">Aktivitas Repetitif</dt>
                                    <dd class="mt-1">Tetapkan pekerjaan rutin sekali, gunakan ulang setiap kali generate laporan.</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-white">Keamanan</dt>
                                    <dd class="mt-1">Data aktivitas terisolasi per user, memastikan privasi terjaga.</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="relative flex flex-col justify-center bg-white/90 p-8 text-slate-700 sm:p-10 lg:p-12">
                            <div class="mx-auto w-full max-w-md text-center">
                                <a href="/" class="inline-flex items-center gap-3 text-slate-700 hover:text-sky-600">
                                    <x-application-logo class="h-12 w-12" />
                                    <span class="text-xl font-semibold">{{ config('app.name', 'WR Portal') }}</span>
                                </a>
                                <h1 class="mt-6 text-2xl font-semibold text-slate-900 sm:text-3xl">{{ $title ?? 'Masuk ke akun Anda' }}</h1>
                                <p class="mt-2 text-sm text-slate-500 sm:text-base">{{ $subtitle ?? 'Masuk untuk mengelola aktivitas manual, repetitif, dan laporan WR Anda.' }}</p>
                            </div>

                            <div class="mx-auto mt-8 w-full max-w-md">
                                <div class="rounded-2xl border border-sky-100 bg-white/95 p-6 shadow-sm sm:p-8">
                                    {{ $slot }}
                                </div>
                                <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ now()->year }} {{ config('app.name', 'WR Portal') }}. Semua hak cipta dilindungi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
