<x-guest-layout>
    <div class="space-y-6">
        <!-- Register Card Header -->
        <div class="space-y-1.5">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Create Your Account') }}</h2>
            <p class="text-xs text-slate-500 font-semibold">{{ __('Register a new Dairy Management account.') }}</p>
        </div>

        <!-- Register Form Card -->
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ showPassword: false }">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ __('Full Name') }} <span class="text-rose-500">*</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                        placeholder="Rahul Sharma"
                        class="w-full py-2.5 px-3.5 text-sm font-semibold border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                </div>

                <!-- Email Address Field -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ __('Email Address') }} <span class="text-rose-500">*</span>
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                        placeholder="rahul@dairy.com"
                        class="w-full py-2.5 px-3.5 text-sm font-mono border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ __('Password') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password"
                            placeholder="Minimum 8 characters"
                            class="w-full py-2.5 px-3.5 pr-10 text-sm font-medium border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.007 10.007 0 014.122-.971c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ __('Confirm Password') }} <span class="text-rose-500">*</span>
                    </label>
                    <input id="password_confirmation" :type="showPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                        placeholder="Re-enter password"
                        class="w-full py-2.5 px-3.5 text-sm font-medium border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 px-4 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    {{ __('Create Account') }}
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-semibold">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="font-bold text-[#005BAC] hover:text-[#003B73] hover:underline ms-1">
                        {{ __('Sign in') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
