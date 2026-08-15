<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Edit User Account') }}" description="{{ __('Update account credentials, role assignments, or account status.') }}">
            <x-slot name="actions">
                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition-all">
                    ← {{ __('Back to Directory') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Main Form Container Card (Full Screen / Full Width) -->
        <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs p-6 lg:p-8 space-y-8">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: User Identity Details -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('User Profile Details') }}</h3>
                            <p class="text-xs text-slate-400 font-medium">{{ __('Update full name and official email address') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('FULL NAME') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                                   class="w-full py-2.5 px-3.5 text-xs font-semibold border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('EMAIL ADDRESS') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full py-2.5 px-3.5 text-xs font-mono border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Role & Status Container -->
                <div x-data="{ open: true }" class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 shadow-2xs">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Role & Access Permissions') }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ __('Update user system role and operational status') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-6 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- System Role -->
                            <div>
                                <label for="role" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('SYSTEM ROLE') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="role" name="role" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                    <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
                                    <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>{{ __('Manager') }}</option>
                                    <option value="center_staff" {{ old('role', $user->role) == 'center_staff' ? 'selected' : '' }}>{{ __('Center Staff') }}</option>
                                    <option value="collection_staff" {{ old('role', $user->role) == 'collection_staff' ? 'selected' : '' }}>{{ __('Collection Staff') }}</option>
                                </select>
                                <x-input-error class="mt-1.5" :messages="$errors->get('role')" />
                            </div>

                            <!-- Account Status -->
                            <div>
                                <label for="status" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('ACCOUNT STATUS') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="status" name="status" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                    <option value="1" {{ old('status', $user->status ? '1' : '0') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="0" {{ old('status', $user->status ? '1' : '0') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                                <x-input-error class="mt-1.5" :messages="$errors->get('status')" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Optional Password Reset -->
                <div x-data="{ open: true }" class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 shadow-2xs">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#10B981] text-white flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Change Password (Optional)') }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ __('Leave empty if you do not wish to update the current password') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#10B981] hover:text-emerald-700">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-6 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('NEW PASSWORD') }}
                                </label>
                                <input id="password" name="password" type="password" placeholder="Leave blank to keep existing"
                                       class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs placeholder:text-slate-300 transition-all" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('password')" />
                            </div>

                            <!-- Confirm New Password -->
                            <div>
                                <label for="password_confirmation" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('CONFIRM NEW PASSWORD') }}
                                </label>
                                <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Re-enter new password"
                                       class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs placeholder:text-slate-300 transition-all" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Save Changes') }}
                    </button>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
