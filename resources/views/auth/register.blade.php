<x-guest-layout title="Buat akun baru" subtitle="Lengkapi identitas proyek agar WR ter-generate otomatis sesuai profil Anda.">
    <div class="space-y-6">
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                {{ __('Mohon periksa kembali data yang diisi, beberapa field belum valid.') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-2">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-2">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="space-y-2">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <x-input-label for="position" value="Posisi" />
                        <x-text-input id="position" class="block w-full" type="text" name="position" :value="old('position')" required autocomplete="organization-title" />
                        <x-input-error :messages="$errors->get('position')" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="project_name" value="Project" />
                        <x-text-input id="project_name" class="block w-full" type="text" name="project_name" :value="old('project_name')" required />
                        <x-input-error :messages="$errors->get('project_name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <x-input-label for="project_company" value="Perusahaan Project" />
                        <x-text-input id="project_company" class="block w-full" type="text" name="project_company" :value="old('project_company')" required />
                        <x-input-error :messages="$errors->get('project_company')" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="contractor_name" value="Kontraktor" />
                        <x-text-input id="contractor_name" class="block w-full" type="text" name="contractor_name" :value="old('contractor_name')" required />
                        <x-input-error :messages="$errors->get('contractor_name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <x-input-label for="contractor_supervisor_name" value="Nama Atasan Kontraktor" />
                        <x-text-input id="contractor_supervisor_name" class="block w-full" type="text" name="contractor_supervisor_name" :value="old('contractor_supervisor_name')" required />
                        <x-input-error :messages="$errors->get('contractor_supervisor_name')" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="contractor_supervisor_title" value="Jabatan Atasan Kontraktor" />
                        <x-text-input id="contractor_supervisor_title" class="block w-full" type="text" name="contractor_supervisor_title" :value="old('contractor_supervisor_title')" required />
                        <x-input-error :messages="$errors->get('contractor_supervisor_title')" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <x-input-label for="project_supervisor_name" value="Nama Atasan Perusahaan Project" />
                        <x-text-input id="project_supervisor_name" class="block w-full" type="text" name="project_supervisor_name" :value="old('project_supervisor_name')" required />
                        <x-input-error :messages="$errors->get('project_supervisor_name')" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="project_supervisor_title" value="Jabatan Atasan Perusahaan Project" />
                        <x-text-input id="project_supervisor_title" class="block w-full" type="text" name="project_supervisor_title" :value="old('project_supervisor_title')" required />
                        <x-input-error :messages="$errors->get('project_supervisor_title')" class="mt-2" />
                    </div>
                </div>
            </div>

            <x-primary-button class="w-full justify-center py-3 text-base">
                {{ __('Register') }}
            </x-primary-button>
        </form>

        <p class="text-center text-sm text-slate-500">
            {{ __('Sudah punya akun?') }}
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">{{ __('Masuk sekarang') }}</a>
        </p>
    </div>
</x-guest-layout>
