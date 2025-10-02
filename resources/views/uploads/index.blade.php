<x-app-layout>
@php
    $latestUpload = $uploads->first();
@endphp
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <section class="bg-white/80 backdrop-blur rounded-3xl shadow-lg border border-white/60 p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="inline-flex items-center text-xs font-semibold uppercase tracking-widest text-sky-600">Monitoring WR Bulanan</span>
                <h1 class="mt-2 text-2xl sm:text-3xl font-semibold text-slate-900">Kelola Upload Dokumen WR</h1>
                <p class="mt-3 text-slate-600 max-w-2xl">Pantau riwayat upload file Ticket & Task, lalu generate dokumen WR versi terbaru dalam sekali klik.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('uploads.create') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white text-sky-600 px-5 py-2.5 font-semibold shadow-sm hover:bg-slate-100 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Upload Baru
                </a>
                <a href="{{ route('uploads.index') }}#generate" class="inline-flex items-center justify-center gap-2 rounded-full bg-sky-600 text-white px-5 py-2.5 font-semibold shadow-lg shadow-sky-500/30 hover:bg-sky-500 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16.5V7.5a2 2 0 012-2h2.379a2 2 0 011.414.586l1.828 1.828a2 2 0 001.414.586H18a2 2 0 012 2v6.5M8 19h8" />
                    </svg>
                    Generate WR
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl border border-sky-100/70 bg-sky-50/60 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-sky-600">Total Upload</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-semibold text-slate-900">{{ number_format($uploads->total()) }}</span>
                    <span class="text-xs text-slate-500">file tersimpan</span>
                </div>
            </div>
            <div class="rounded-2xl border border-sky-100/70 bg-white/80 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-sky-600">Upload Terakhir</p>
                <div class="mt-2 text-slate-700">
                    {{ $latestUpload?->created_at?->diffForHumans() ?? 'Belum ada data' }}
                </div>
            </div>
            <div class="rounded-2xl border border-sky-100/70 bg-white/80 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-sky-600">Periode Terbaru</p>
                <div class="mt-2 text-slate-700">
                    {{ $latestUpload?->for_month?->translatedFormat('F Y') ?? '-' }}
                </div>
            </div>
        </div>
    </section>

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-500/15 text-emerald-800 px-4 py-3 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <section class="bg-white/80 backdrop-blur rounded-3xl shadow-lg border border-white/60 p-6 sm:p-8">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Riwayat Upload</h2>
        <div class="overflow-hidden border border-sky-100 rounded-2xl shadow-sm">
            <table class="min-w-full divide-y divide-sky-100 text-sm">
                <thead class="bg-sky-100/70 text-sky-900 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Waktu</th>
                        <th class="px-4 py-3 text-left font-semibold">User</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama File</th>
                        <th class="px-4 py-3 text-left font-semibold">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold">Bulan</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50/60">
                    @forelse ($uploads as $u)
                        <tr class="bg-white/80 hover:bg-sky-50/70 transition">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $u->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $u->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600 break-all">{{ $u->original_name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700 uppercase">
                                    {{ $u->kind_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $u->for_month?->translatedFormat('F Y') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('uploads.destroy', $u) }}" onsubmit="return confirm('Hapus upload ini beserta data yang diparsing?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center gap-1 rounded-full bg-rose-500/90 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-300 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m1 0v11a2 2 0 01-2 2H9a2 2 0 01-2-2V7z" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                Belum ada upload. Mulai dengan menambahkan file ticket atau task.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $uploads->links() }}
        </div>
    </section>

    <section id="generate" class="bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 rounded-3xl shadow-xl text-white p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
                <h2 class="text-2xl font-semibold">Generate Dokumen WR</h2>
                <p class="mt-2 text-sky-100">Pilih bulan yang ingin dirangkum. Sistem akan menggabungkan data Ticket & Task dalam format WR siap unduh.</p>
            </div>
            <form method="POST" action="{{ route('wr.generate') }}" class="w-full max-w-2xl grid gap-4 sm:grid-cols-2 lg:grid-cols-3 items-end bg-white/10 backdrop-blur rounded-2xl px-5 py-4">
                @csrf
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-white/90">Untuk Bulan</label>
                    <input type="month" name="for_month" value="{{ old('for_month', now()->format('Y-m')) }}" class="mt-1 w-full rounded-xl border-none bg-white/80 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-white/90">Tanggal Tanda Tangan</label>
                    <input type="date" name="signature_date" value="{{ old('signature_date', now()->toDateString()) }}" class="mt-1 w-full rounded-xl border-none bg-white/80 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                </div>
                <div class="sm:col-span-2 lg:col-span-1 flex sm:justify-end items-end">
                    <button class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-white text-sky-600 font-semibold px-5 py-2.5 shadow-lg shadow-sky-900/20 hover:bg-slate-100 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0 5-5m-5 5V4" />
                        </svg>
                        Generate & Unduh
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
</x-app-layout>
