<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Account Profile') }}" description="{{ __('Manage your personal account details, change password, and security preferences.') }}" />
    </x-slot>

    <!-- Profile Overview Card Header -->
    <div class="w-full space-y-6">
        <x-admin.card title="{{ __('User Profile Details') }}">
            <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
                <div class="w-16 h-16 rounded-2xl bg-[#005BAC]/10 border border-[#005BAC]/20 text-[#005BAC] flex items-center justify-center text-2xl font-black">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        {{ auth()->user()->name }}
                        <span class="px-2.5 py-0.5 bg-blue-50 text-[#005BAC] border border-blue-200 rounded-full text-xs font-extrabold">{{ strtoupper(str_replace('_', ' ', auth()->user()->role ?? 'User')) }}</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-mono font-semibold mt-0.5">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <div class="pt-6 space-y-8">
                <!-- Update Profile Information Form -->
                @include('profile.partials.update-profile-information-form')

                <!-- Update Password Form -->
                <div class="pt-8 border-t border-slate-100">
                    @include('profile.partials.update-password-form')
                </div>

                <!-- Delete Account Form -->
                <div class="pt-8 border-t border-slate-100">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
