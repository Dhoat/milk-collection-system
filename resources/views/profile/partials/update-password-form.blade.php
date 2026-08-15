<section class="space-y-4">
    <header class="space-y-1">
        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
            {{ __('Update Security Password') }}
        </h3>
        <p class="text-xs text-slate-500 font-semibold">
            {{ __('Ensure your account is using a strong password to safeguard administrative access.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <x-input-label for="update_password_current_password" :value="__('Current Password')" class="font-bold text-slate-700" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1.5 block w-full text-sm" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('New Password')" class="font-bold text-slate-700" />
                <x-text-input id="update_password_password" name="password" type="password" class="mt-1.5 block w-full text-sm" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm New Password')" class="font-bold text-slate-700" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1.5 block w-full text-sm" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                {{ __('Update Password') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-xs font-bold text-emerald-600">
                    ✓ {{ __('Password updated successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
