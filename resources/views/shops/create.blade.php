<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Register New Shop') }}" description="{{ __('Add a new retail shop outlet to the center dispatch directory.') }}">
            <x-slot name="actions">
                <a href="{{ route('shops.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-xs transition-all">
                    ← {{ __('Back to Directory') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Main Form Card (Full Screen / Full Width) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 lg:p-8 space-y-8">
            <form method="POST" action="{{ route('shops.store') }}" class="space-y-8">
                @csrf

                <!-- Section 1: Shop Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Shop Outlet Details') }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ __('Basic details, code, and owner contact information') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Shop Code -->
                        <div>
                            <label for="shop_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Shop Code') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="shop_code" name="shop_code" type="text" class="w-full py-2.5 px-3.5 text-xs font-mono font-bold uppercase border-slate-200 rounded-xl shadow-xs" :value="old('shop_code')" placeholder="e.g. SHP-001" required />
                            <p class="text-[11px] text-slate-400 mt-1.5 font-medium">{{ __('Unique identifier code for the retail shop.') }}</p>
                            <x-input-error :messages="$errors->get('shop_code')" class="mt-1.5" />
                        </div>

                        <!-- Shop Name -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Shop Name') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="name" name="name" type="text" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 rounded-xl shadow-xs" :value="old('name')" placeholder="e.g. Royal Dairy Store" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

                        <!-- Owner Name -->
                        <div>
                            <label for="owner_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Owner Name') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="owner_name" name="owner_name" type="text" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 rounded-xl shadow-xs" :value="old('owner_name')" placeholder="e.g. Rajesh Kumar" required />
                            <x-input-error :messages="$errors->get('owner_name')" class="mt-1.5" />
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Phone Number') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="phone" name="phone" type="text" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('phone')" placeholder="e.g. 9876543210" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Email Address (Optional)') }}
                            </label>
                            <x-text-input id="email" name="email" type="email" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('email')" placeholder="e.g. owner@example.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <!-- Credit Limit -->
                        <div>
                            <label for="credit_limit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Credit Limit (₹)') }}
                            </label>
                            <x-text-input id="credit_limit" name="credit_limit" type="number" step="0.01" min="0" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('credit_limit', '0.00')" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('credit_limit')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Location Details (Styled Container Box) -->
                <div x-data="{ open: true }" class="bg-blue-50/40 border border-blue-100/80 rounded-2xl p-5 lg:p-6 space-y-5">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Location Details') }}</h3>
                                <p class="text-xs text-slate-500 font-medium">{{ __('Associated village, market sector and complete address') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-5 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Village Dropdown -->
                            <div>
                                <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Associated Village (Optional)') }}
                                </label>
                                <select id="village_id" name="village_id" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white">
                                    <option value="">{{ __('None / Direct City Outlet') }}</option>
                                    @foreach($villages as $v)
                                        <option value="{{ $v->id }}" {{ old('village_id') == $v->id ? 'selected' : '' }}>
                                            {{ $v->name }} ({{ $v->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('village_id')" class="mt-1.5" />
                            </div>

                            <!-- Area / Market Sub-location -->
                            <div>
                                <label for="area" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Area / Market Sub-location') }}
                                </label>
                                <x-text-input id="area" name="area" type="text" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 rounded-xl shadow-xs bg-white" :value="old('area')" placeholder="e.g. Main Market, Sector 4" />
                                <x-input-error :messages="$errors->get('area')" class="mt-1.5" />
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Full Address') }}
                            </label>
                            <textarea id="address" name="address" rows="2" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" placeholder="Street name, landmark, city...">{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Status & Participation (Light Green Container) -->
                <div x-data="{ open: true, isActive: true }" class="bg-emerald-50/40 border border-emerald-100/80 rounded-2xl p-5 lg:p-6 space-y-5">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Status & Dispatch Authorization') }}</h3>
                                <p class="text-xs text-slate-500 font-medium">{{ __('Set the shop status for daily order dispatches') }}</p>
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
                                <h4 class="text-xs font-extrabold text-slate-900">{{ __('Active Shop Status') }}</h4>
                                <p class="text-[11px] text-slate-500 font-medium">{{ __('Active shop status enables receiving daily product order dispatches.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Save Shop') }}
                    </button>
                    <a href="{{ route('shops.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
