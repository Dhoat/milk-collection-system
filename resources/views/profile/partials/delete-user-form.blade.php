<section class="space-y-4">
    <header class="space-y-1">
        <h3 class="text-sm font-extrabold text-rose-700 uppercase tracking-wider">
            {{ __('Delete User Account') }}
        </h3>
        <p class="text-xs text-slate-500 font-semibold">
            {{ __('Once your account is deleted, all of its associated administrative records and authorizations will be removed.') }}
        </p>
    </header>

    <div class="pt-2">
        <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            {{ __('Delete Account') }}
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-4">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 text-rose-600">
                <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-base font-black text-slate-900">{{ __('Confirm Account Deletion') }}</h3>
            </div>

            <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                {{ __('Once your account is deleted, all resources will be permanently erased. Please enter your account password to confirm account deletion.') }}
            </p>

            <div>
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full text-sm" placeholder="{{ __('Enter your password to confirm') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('Permanently Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
