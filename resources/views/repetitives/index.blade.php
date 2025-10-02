<x-app-layout>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <section class="bg-white/80 backdrop-blur rounded-3xl shadow-lg border border-white/60 p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="inline-flex items-center text-xs font-semibold uppercase tracking-widest text-sky-600">Input Aktivitas</span>
                <h1 class="mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">Aktivitas Repetitif</h1>
                <p class="mt-3 text-slate-600 max-w-2xl">Atur daftar aktivitas dan output harian yang berulang supaya otomatis masuk ke WR sesuai jam yang kamu pilih.</p>
            </div>
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
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Tambah Aktivitas Repetitif</h2>
        <form method="POST" action="{{ route('repetitives.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-600">Waktu Mulai</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600">Waktu Selesai</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
            </div>
            <div class="sm:col-span-2 lg:col-span-2">
                <label class="block text-sm font-medium text-slate-600">Nama Aktivitas</label>
                <input type="text" name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" placeholder="Contoh: Monitoring Daily Summary" required />
            </div>
            <div class="sm:col-span-2 lg:col-span-4">
                <label class="block text-sm font-medium text-slate-600">Output</label>
                <textarea name="output" rows="3" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" placeholder="Contoh: Laporan harian terkirim ke stakeholder">{{ old('output') }}</textarea>
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
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-slate-900">Daftar Aktivitas Repetitif</h2>
            <span class="text-sm text-slate-500">{{ $activities->count() }} aktivitas</span>
        </div>

        @if ($activities->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 text-slate-500 px-6 py-10 text-center">
                <p class="text-sm">Belum ada aktivitas repetitif. Tambahkan aktivitas harian yang sering kamu lakukan agar otomatis muncul di WR.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($activities as $activity)
                    <div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white/90 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-5 py-4">
                            <div>
                                @php
                                    $startTime = $activity->start_time instanceof \Carbon\CarbonInterface
                                        ? $activity->start_time->format('H:i')
                                        : substr((string) $activity->start_time, 0, 5);
                                    $endTime = $activity->end_time instanceof \Carbon\CarbonInterface
                                        ? $activity->end_time->format('H:i')
                                        : substr((string) $activity->end_time, 0, 5);
                                @endphp
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ $startTime }} - {{ $endTime }}
                                </div>
                                <div class="text-base font-semibold text-slate-800">{{ $activity->title }}</div>
                                @if ($activity->output)
                                    <div class="mt-1 text-sm text-slate-600">Output: {{ $activity->output }}</div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 rounded-full bg-sky-500/10 px-3 py-1.5 text-xs font-semibold text-sky-600 hover:bg-sky-500/20 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" x-show="open" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" x-show="!open" />
                                    </svg>
                                    <span x-text="open ? 'Tutup' : 'Edit'"></span>
                                </button>
                                <form method="POST" action="{{ route('repetitives.destroy', $activity) }}" onsubmit="return confirm('Hapus aktivitas ini?')">
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
                        </div>
                        <div x-show="open" x-collapse class="border-t border-slate-100 px-5 py-4 bg-slate-50/50">
                            <form method="POST" action="{{ route('repetitives.update', $activity) }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-sm font-medium text-slate-600">Waktu Mulai</label>
                                    <input type="time" name="start_time" value="{{ old('start_time', $startTime) }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-600">Waktu Selesai</label>
                                    <input type="time" name="end_time" value="{{ old('end_time', $endTime) }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                                </div>
                                <div class="sm:col-span-2 lg:col-span-2">
                                    <label class="block text-sm font-medium text-slate-600">Nama Aktivitas</label>
                                    <input type="text" name="title" value="{{ old('title', $activity->title) }}" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                                </div>
                                <div class="sm:col-span-2 lg:col-span-4">
                                    <label class="block text-sm font-medium text-slate-600">Output</label>
                                    <textarea name="output" rows="3" class="mt-1 w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" placeholder="Contoh: Memberikan update ke user">{{ old('output', $activity->output) }}</textarea>
                                </div>
                                <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
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
                @endforeach
            </div>
        @endif
    </section>
</div>
</x-app-layout>



