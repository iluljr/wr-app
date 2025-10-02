<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="position" value="Posisi" />
                <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $user->position)" autocomplete="organization-title" />
                <x-input-error class="mt-2" :messages="$errors->get('position')" />
            </div>
            <div>
                <x-input-label for="project_name" value="Project" />
                <x-text-input id="project_name" name="project_name" type="text" class="mt-1 block w-full" :value="old('project_name', $user->project_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('project_name')" />
            </div>
            <div>
                <x-input-label for="project_company" value="Perusahaan Project" />
                <x-text-input id="project_company" name="project_company" type="text" class="mt-1 block w-full" :value="old('project_company', $user->project_company)" />
                <x-input-error class="mt-2" :messages="$errors->get('project_company')" />
            </div>
            <div>
                <x-input-label for="contractor_name" value="Kontraktor" />
                <x-text-input id="contractor_name" name="contractor_name" type="text" class="mt-1 block w-full" :value="old('contractor_name', $user->contractor_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('contractor_name')" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="contractor_supervisor_name" value="Nama Atasan Kontraktor" />
                <x-text-input id="contractor_supervisor_name" name="contractor_supervisor_name" type="text" class="mt-1 block w-full" :value="old('contractor_supervisor_name', $user->contractor_supervisor_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('contractor_supervisor_name')" />
            </div>
            <div>
                <x-input-label for="contractor_supervisor_title" value="Jabatan Atasan Kontraktor" />
                <x-text-input id="contractor_supervisor_title" name="contractor_supervisor_title" type="text" class="mt-1 block w-full" :value="old('contractor_supervisor_title', $user->contractor_supervisor_title)" />
                <x-input-error class="mt-2" :messages="$errors->get('contractor_supervisor_title')" />
            </div>
            <div>
                <x-input-label for="project_supervisor_name" value="Nama Atasan Perusahaan Project" />
                <x-text-input id="project_supervisor_name" name="project_supervisor_name" type="text" class="mt-1 block w-full" :value="old('project_supervisor_name', $user->project_supervisor_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('project_supervisor_name')" />
            </div>
            <div>
                <x-input-label for="project_supervisor_title" value="Jabatan Atasan Perusahaan Project" />
                <x-text-input id="project_supervisor_title" name="project_supervisor_title" type="text" class="mt-1 block w-full" :value="old('project_supervisor_title', $user->project_supervisor_title)" />
                <x-input-error class="mt-2" :messages="$errors->get('project_supervisor_title')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
