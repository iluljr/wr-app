<x-app-layout>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <section class="bg-white/80 backdrop-blur rounded-3xl shadow-lg border border-white/60 p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="inline-flex items-center text-xs font-semibold uppercase tracking-widest text-sky-600">Input Aktivitas</span>
                <h1 class="mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">Input Aktivitas Harian</h1>
                <p class="mt-3 text-slate-600 max-w-2xl">Catat aktivitas di luar data Ticket dan Task agar ikut tergabung saat generate WR.</p>
            </div>
            <form method="GET" action="{{ route('activities.index') }}" class="bg-white/80 rounded-2xl border border-sky-100 px-4 py-3 shadow-sm flex items-center gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase">Pilih Tanggal</label>
                    <input type="date" name="for_date" value="{{ request('for_date', $selectedDate->toDateString()) }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" />
                </div>
                <button class="inline-flex items-center gap-2 rounded-xl bg-sky-600 text-white font-semibold px-4 py-2.5 shadow hover:bg-sky-500 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 10h18M3 16h18" />
                    </svg>
                    Tampilkan
                </button>
            </form>
        </div>
    </section>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-500/15 text-rose-800 px-4 py-3 shadow-sm">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-500/15 text-emerald-800 px-4 py-3 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <section class="bg-white/80 backdrop-blur rounded-3xl shadow-lg border border-white/60 p-6 sm:p-8">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Tambah Aktivitas</h2>
        <form method="POST" action="{{ route('activities.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-600">Tanggal</label>
                <input type="date" name="activity_date" value="{{ old('activity_date', $selectedDate->toDateString()) }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600">Waktu Mulai</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600">Waktu Selesai</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
            </div>
            <div class="sm:col-span-2 lg:col-span-4">
                <label class="block text-sm font-medium text-slate-600">Nama Aktivitas</label>
                <input type="text" name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" placeholder="Contoh: Koordinasi dengan vendor" required />
            </div>
            <div class="sm:col-span-2 lg:col-span-4">
                <label class="block text-sm font-medium text-slate-600">Output</label>
                <textarea name="output" rows="3" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" placeholder="Contoh: Mendapatkan update progres dari vendor">{{ old('output') }}</textarea>
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                <button class="inline-flex items-center gap-2 rounded-xl bg-sky-600 text-white font-semibold px-5 py-2.5 shadow-lg shadow-sky-500/30 hover:bg-sky-500 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Simpan Aktivitas
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white/80 backdrop-blur rounded-3xl shadow-lg border border-white/60 p-6 sm:p-8">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Daftar Aktivitas Tanggal {{ $selectedDate->locale('id')->translatedFormat('d F Y') }}</h2>
        @if ($activities->isEmpty())
            <div class="text-center text-slate-500 py-10">
                Belum ada aktivitas untuk tanggal ini.
            </div>
        @else
            <div class="space-y-4">
                @foreach ($activities as $activity)
                    <div class="border border-sky-100 rounded-2xl bg-white/85 px-4 py-4 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-slate-700">{{ substr($activity->start_time, 0, 5) }} - {{ substr($activity->end_time, 0, 5) }}</p>
                                <p class="text-base font-semibold text-slate-900">{{ $activity->title }}</p>
                                @if ($activity->output)
                                    <p class="text-sm text-slate-600">Output: {{ $activity->output }}</p>
                                @endif
                                <p class="text-xs text-slate-500">Dicatat oleh {{ $activity->user?->name ?? '-' }}</p>
                            </div>
                            @if ($activity->user_id === Auth::id())
                                <div class="flex items-center gap-2">
                                    <div x-data="{ open: false }" class="relative" x-cloak>
                                        <button type="button" @click="open = true" class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-700 shadow-sm hover:bg-sky-200 transition">Edit</button>
                                        <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center">
                                            <div class="absolute inset-0 bg-slate-900/40" @click="open = false"></div>
                                            <div class="relative z-50 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                                                <h3 class="text-lg font-semibold text-slate-900 mb-4">Ubah Aktivitas</h3>
                                                <form method="POST" action="{{ route('activities.update', $activity) }}" class="space-y-4">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label class="block text-sm font-medium text-slate-600">Tanggal</label>
                                                            <input type="date" name="activity_date" value="{{ $activity->activity_date->toDateString() }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-slate-600">Waktu Mulai</label>
                                                            <input type="time" name="start_time" value="{{ substr($activity->start_time, 0, 5) }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-slate-600">Waktu Selesai</label>
                                                            <input type="time" name="end_time" value="{{ substr($activity->end_time, 0, 5) }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label class="block text-sm font-medium text-slate-600">Nama Aktivitas</label>
                                                            <input type="text" name="title" value="{{ $activity->title }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label class="block text-sm font-medium text-slate-600">Output</label>
                                                            <textarea name="output" rows="3" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" placeholder="Output aktivitas">{{ $activity->output }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-between gap-3 pt-2">
                                                        <button type="button" @click="open = false" class="inline-flex items-center gap-2 rounded-full bg-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300 transition">Batal</button>
                                                        <button class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-sky-500 transition">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                            </svg>
                                                            Simpan Perubahan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('Hapus aktivitas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center gap-2 rounded-full bg-rose-500/90 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-300 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m1 0v11a2 2 0 01-2 2H9a2 2 0 01-2-2V7z" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
</x-app-layout>
