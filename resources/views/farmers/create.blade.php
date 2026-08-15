<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ __('Register New Farmer') }}" 
            description="{{ __('Create a new dairy producer profile and bank payment record') }}">
            <x-slot name="actions">
                <a href="{{ route('farmers.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-xs transition-all">
                    ← {{ __('Back to Directory') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Main Form Card (Full Screen / Full Width) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 lg:p-8 space-y-8">
            <form method="POST" action="{{ route('farmers.store') }}" class="space-y-8">
                @csrf

                <!-- Section 1: Personal & System Profile -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Farmer Profile Information') }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ __('Identifier codes, contact details and village assignment') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Farmer Code -->
                        <div>
                            <label for="farmer_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Farmer Code') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="farmer_code" name="farmer_code" type="text" class="w-full py-2.5 px-3.5 text-xs font-mono font-bold border-slate-200 rounded-xl shadow-xs" :value="old('farmer_code')" required autofocus placeholder="e.g. FMR-1001" />
                            <p class="text-[11px] text-slate-400 mt-1.5 font-medium">{{ __('Unique identifier code assigned to farmer.') }}</p>
                            <x-input-error class="mt-1.5" :messages="$errors->get('farmer_code')" />
                        </div>

                        <!-- Village Dropdown -->
                        <div>
                            <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Assigned Village Sector') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="village_id" name="village_id" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" required>
                                <option value="" disabled selected>{{ __('Select active village...') }}</option>
                                @foreach($villages as $village)
                                    <option value="{{ $village->id }}" {{ old('village_id') == $village->id ? 'selected' : '' }}>
                                        {{ $village->name }} ({{ $village->code }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1.5" :messages="$errors->get('village_id')" />
                        </div>

                        <!-- Farmer Name -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Farmer Full Name') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="name" name="name" type="text" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 rounded-xl shadow-xs" :value="old('name')" required placeholder="e.g. Gurpreet Singh" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                        </div>

                        <!-- Father Name -->
                        <div>
                            <label for="father_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Father / Guardian Name') }}
                            </label>
                            <x-text-input id="father_name" name="father_name" type="text" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 rounded-xl shadow-xs" :value="old('father_name')" placeholder="e.g. Harjit Singh" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('father_name')" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Gender') }}
                            </label>
                            <select id="gender" name="gender" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white">
                                <option value="">{{ __('Select Gender...') }}</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                            </select>
                            <x-input-error class="mt-1.5" :messages="$errors->get('gender')" />
                        </div>

                        <!-- Registration Date -->
                        <div>
                            <label for="joining_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Registration Date') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="joining_date" name="joining_date" type="date" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('joining_date', date('Y-m-d'))" required />
                            <x-input-error class="mt-1.5" :messages="$errors->get('joining_date')" />
                        </div>

                        <!-- Mobile -->
                        <div>
                            <label for="mobile" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Primary Mobile Contact') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="mobile" name="mobile" type="text" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('mobile')" required placeholder="e.g. 9876543210" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('mobile')" />
                        </div>

                        <!-- Alternate Mobile -->
                        <div>
                            <label for="alternate_mobile" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Alternate Phone (Optional)') }}
                            </label>
                            <x-text-input id="alternate_mobile" name="alternate_mobile" type="text" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('alternate_mobile')" placeholder="e.g. 9876543211" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('alternate_mobile')" />
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            {{ __('Residential Address') }}
                        </label>
                        <textarea id="address" name="address" rows="2" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs" placeholder="Street address, house number...">{{ old('address') }}</textarea>
                        <x-input-error class="mt-1.5" :messages="$errors->get('address')" />
                    </div>
                </div>

                <!-- Section 2: Financial & Banking Information (Styled Container Box) -->
                <div x-data="{ open: true }" class="bg-blue-50/40 border border-blue-100/80 rounded-2xl p-5 lg:p-6 space-y-5">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Financial & Payout Information') }}</h3>
                                <p class="text-xs text-slate-500 font-medium">{{ __('Bank account details for automated milk collection payments') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-5 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Bank Name -->
                            <div>
                                <label for="bank_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Bank Name') }}
                                </label>
                                <x-text-input id="bank_name" name="bank_name" type="text" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 rounded-xl shadow-xs bg-white" :value="old('bank_name')" placeholder="e.g. State Bank of India" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('bank_name')" />
                            </div>

                            <!-- Account Number -->
                            <div>
                                <label for="account_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Account Number') }}
                                </label>
                                <x-text-input id="account_number" name="account_number" type="text" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs bg-white" :value="old('account_number')" placeholder="e.g. 1234567890" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('account_number')" />
                            </div>

                            <!-- IFSC Code -->
                            <div>
                                <label for="ifsc_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('IFSC Code') }}
                                </label>
                                <x-text-input id="ifsc_code" name="ifsc_code" type="text" class="w-full py-2.5 px-3.5 text-xs font-mono uppercase border-slate-200 rounded-xl shadow-xs bg-white" :value="old('ifsc_code')" placeholder="e.g. SBIN0001234" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('ifsc_code')" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Status & Participation (Light Green Container) -->
                <div x-data="{ open: true, isActive: true }" class="bg-emerald-50/40 border border-emerald-100/80 rounded-2xl p-5 lg:p-6 space-y-5">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Status & Participation') }}</h3>
                                <p class="text-xs text-slate-500 font-medium">{{ __('Set the farmer status for milk collection') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-emerald-700 hover:text-emerald-900">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="pt-2">
                        <div class="p-4 bg-white/90 border border-slate-200/80 rounded-xl flex items-center gap-4 shadow-xs">
                            <button type="button" @click="isActive = !isActive" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="isActive ? 'bg-[#005BAC]' : 'bg-slate-300'">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="isActive ? 'translate-x-5' : 'translate-x-0'"></span>
                            </button>
                            <input type="hidden" name="status" :value="isActive ? '1' : '0'">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900">{{ __('Active Farmer Account') }}</h4>
                                <p class="text-[11px] text-slate-500 font-medium">{{ __('Active status allows recording daily morning & evening milk collections.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Register Farmer') }}
                    </button>
                    <a href="{{ route('farmers.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
