<x-app-layout>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="bg-white/85 backdrop-blur rounded-3xl shadow-lg border border-white/70 p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Upload Data WR</h1>
                <p class="mt-2 text-slate-600">Unggah file Ticket (Resolved) atau Task (Actual End) dalam format Excel untuk diproses otomatis.</p>
            </div>
            <a href="{{ route('uploads.index') }}" class="inline-flex items-center gap-2 rounded-full bg-sky-100 text-sky-700 px-4 py-2 text-sm font-semibold hover:bg-sky-200 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Riwayat
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-500/10 px-4 py-3 text-rose-800">
                <p class="font-semibold">Ada input yang perlu dicek ulang:</p>
                <ul class="mt-2 list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('uploads.store') }}" enctype="multipart/form-data" class="grid gap-6">
            @csrf
            <div class="grid gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700">File Excel</label>
                    <div x-data="{ fileName: '' }" class="mt-2 flex items-center gap-4 rounded-2xl border border-dashed border-sky-300 bg-sky-50/60 px-4 py-6">
                        <svg class="h-12 w-12 text-sky-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5l3 3m0 0l3-3m-3 3V6m6 3V5.25A2.25 2.25 0 0015.75 3h-7.5A2.25 2.25 0 006 5.25v13.5A2.25 2.25 0 008.25 21H12" />
                        </svg>
                        <div class="space-y-1">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 transition">
                                <input type="file" name="file" accept=".xlsx,.xls" class="hidden" required @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''">
                                <span>Pilih File Excel</span>
                            </label>
                            <p class="text-xs text-slate-500" x-text="fileName ? fileName : 'Belum ada file dipilih'">Belum ada file dipilih</p>
                            <p class="text-[11px] uppercase tracking-widest text-slate-400">Format yang didukung: .xlsx, .xls</p>
                        </div>
                    </div>
                    @error('file')<div class="mt-2 text-sm text-rose-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Jenis Dataset</label>
                    <select name="kind" class="mt-2 w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required>
                        <option value="resolved">Ticket (Resolved)</option>
                        <option value="actual_end">Task (Actual End)</option>
                    </select>
                    @error('kind')<div class="mt-2 text-sm text-rose-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Untuk Bulan</label>
                    <input type="month" name="for_month" value="{{ old('for_month', now()->format('Y-m')) }}" class="mt-2 w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300" required />
                    <p class="mt-1 text-xs text-slate-500">Pilih bulan yang sesuai dengan dataset.</p>
                    @error('for_month')<div class="mt-2 text-sm text-rose-600">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">
                <p class="text-sm text-slate-500">File akan diproses di background dan siap digunakan untuk generate WR.</p>
                <button class="inline-flex items-center justify-center gap-2 rounded-full bg-sky-600 px-6 py-2.5 font-semibold text-white shadow-lg shadow-sky-500/40 hover:bg-sky-500 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 9 17.25l11-11" />
                    </svg>
                    Upload & Proses
                </button>
            </div>
        </form>
    </section>
</div>
</x-app-layout>






