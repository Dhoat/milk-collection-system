<x-guest-layout>
    <div class="space-y-6">
        <!-- Login Card Header -->
        <div class="space-y-1.5">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ __('Welcome Back') }}</h2>
            <p class="text-xs text-slate-500 font-semibold">{{ __('Sign in to your Dairy Management admin panel account.') }}</p>
        </div>

        <!-- Session Status Alert -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Login Form Card -->
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
            <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
                @csrf

                <!-- Email Address Field -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ __('Email Address') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            placeholder="name@dairy.com"
                            class="w-full py-2.5 px-3.5 text-sm font-medium border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <!-- Password Field with Toggle -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ __('Password') }} <span class="text-rose-500">*</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-bold text-[#005BAC] hover:text-[#003B73] hover:underline" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full py-2.5 px-3.5 pr-10 text-sm font-medium border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.007 10.007 0 014.122-.971c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-[#005BAC] border-slate-300 rounded focus:ring-[#005BAC]/20">
                    <label for="remember_me" class="ms-2 text-xs font-semibold text-slate-600 select-none cursor-pointer">
                        {{ __('Remember my active session') }}
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 px-4 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    {{ __('Sign In to Dashboard') }}
                </button>
            </form>

            @if (Route::has('register'))
                <div class="pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500 font-semibold">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="font-bold text-[#005BAC] hover:text-[#003B73] hover:underline ms-1">
                            {{ __('Register here') }}
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>
