<x-guest-layout title="Selamat datang kembali" subtitle="Masuk untuk mengelola aktivitas dan menghasilkan WR harian Anda.">
    <div class="space-y-6">
        @if (session('status'))
            <x-auth-session-status class="text-sm" :status="session('status')" />
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Password')" />
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
                <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center gap-3 text-sm text-slate-600">
                    <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span>{{ __('Remember me') }}</span>
                </label>
            </div>

            <x-primary-button class="w-full justify-center py-3 text-base">
                {{ __('Log in') }}
            </x-primary-button>
        </form>

        <p class="text-center text-sm text-slate-500">
            {{ __('Belum punya akun?') }}
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">{{ __('Daftar sekarang') }}</a>
        </p>
    </div>
</x-guest-layout>
