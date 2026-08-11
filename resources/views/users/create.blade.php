<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Create User Account') }}" description="{{ __('Add a new staff or admin user to the system.') }}">
            <x-slot name="actions">
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('Back to Directory') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="max-w-3xl">
        <x-admin.card title="{{ __('User Profile Details') }}">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Full Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-xs font-medium" :value="old('name')" required placeholder="e.g. Rahul Sharma" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email Address')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-xs font-mono" :value="old('email')" required placeholder="rahul@dairy.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- System Role -->
                    <div>
                        <x-input-label for="role" :value="__('System Role')" />
                        <select id="role" name="role" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs font-medium" required>
                            <option value="">{{ __('Select Assigned Role') }}</option>
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>{{ __('Super Admin (Full Access)') }}</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>{{ __('Manager (Operations & Reports)') }}</option>
                            <option value="center_staff" {{ old('role') == 'center_staff' ? 'selected' : '' }}>{{ __('Center Staff (Receivings, Stock & Orders)') }}</option>
                            <option value="collection_staff" {{ old('role') == 'collection_staff' ? 'selected' : '' }}>{{ __('Collection Staff (Farmer Milk Entry)') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <!-- Status -->
                    <div>
                        <x-input-label for="status" :value="__('Account Status')" />
                        <select id="status" name="status" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs font-medium" required>
                            <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full text-xs" required placeholder="Minimum 8 characters" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-xs" required placeholder="Re-enter password" />
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="py-2.5 px-5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm">
                        {{ __('Create User Account') }}
                    </button>
                </div>
            </form>
        </x-admin.card>
    </div>
</x-admin-layout>
