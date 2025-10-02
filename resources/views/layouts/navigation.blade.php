<nav x-data="{ open: false }" class="bg-sky-600/95 text-sky-50 shadow-lg backdrop-blur sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-10">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('uploads.index') }}" class="flex items-center gap-3">
                        <x-application-logo class="block h-9 w-9" />
                        <span class="font-semibold tracking-wide text-lg text-white">{{ config('app.name', 'WR Portal') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:space-x-3">
                    <x-nav-link :href="route('uploads.index')" :active="request()->routeIs('uploads.index')">
                        Riwayat Upload
                    </x-nav-link>
                    <x-nav-link :href="route('uploads.create')" :active="request()->routeIs('uploads.create')">
                        Upload Baru
                    </x-nav-link>
                    <x-nav-link :href="route('activities.index')" :active="request()->routeIs('activities.*')">
                        Input Aktivitas
                    </x-nav-link>
                    <x-nav-link :href="route('repetitives.index')" :active="request()->routeIs('repetitives.*')">
                        Aktivitas Repetitif
                    </x-nav-link>
                    <x-nav-link href="{{ route('uploads.index') }}#generate" :active="request()->routeIs('uploads.index')">
                        Generate WR
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4 relative z-50">
                <span class="hidden lg:inline-flex text-sm text-sky-100/80">Hai, {{ Auth::user()->name }}</span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-sky-50/90 bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150">
                            <div class="hidden sm:block">Menu</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-sky-100 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-sky-700/95">
        <div class="px-4 pt-4 pb-3 border-b border-white/20">
            <a href="{{ route('uploads.index') }}" class="flex items-center gap-3">
                <x-application-logo class="h-9 w-9" />
                <span class="text-base font-semibold text-white">{{ config('app.name', 'WR Portal') }}</span>
            </a>
        </div>
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('uploads.index')" :active="request()->routeIs('uploads.index')">
                Riwayat Upload
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('uploads.create')" :active="request()->routeIs('uploads.create')">
                Upload Baru
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('activities.index')" :active="request()->routeIs('activities.*')">
                Input Aktivitas
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('repetitives.index')" :active="request()->routeIs('repetitives.*')">
                Aktivitas Repetitif
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('uploads.index') }}#generate" :active="request()->routeIs('uploads.index')">
                Generate WR
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/20 text-sky-50">
            <div class="px-4">
                <div class="font-medium text-base">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-sky-100/80">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
