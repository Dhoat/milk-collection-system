<section class="space-y-4">
    <header class="space-y-1">
        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
            {{ __('Profile Information') }}
        </h3>
        <p class="text-xs text-slate-500 font-semibold">
            {{ __("Update your account's profile name and primary email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Full Name')" class="font-bold text-slate-700" />
                <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full text-sm font-semibold" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email Address')" class="font-bold text-slate-700" />
                <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full text-sm font-mono" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-xs text-amber-700 font-semibold">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="underline font-bold text-[#005BAC] hover:text-[#003B73]">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-1.5 font-bold text-xs text-emerald-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                {{ __('Save Profile Info') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-xs font-bold text-emerald-600">
                    ✓ {{ __('Profile updated successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
