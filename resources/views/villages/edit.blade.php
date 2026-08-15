<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ __('Edit Village') }}: {{ $village->name }}" 
            description="{{ __('Update sector details and configuration') }}">
            <x-slot name="actions">
                <a href="{{ route('villages.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition-all">
                    ← {{ __('Back to List') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Main Form Container Card (Full Screen / Full Width) -->
        <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs p-6 lg:p-8 space-y-8">
            <form method="POST" action="{{ route('villages.update', $village) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Village Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Village Information') }}</h3>
                            <p class="text-xs text-slate-400 font-medium">{{ __('Modify sector properties and details below') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Village Code -->
                        <div>
                            <label for="code" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('VILLAGE CODE') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="code" name="code" type="text" value="{{ old('code', $village->code) }}" required autocomplete="off"
                                   class="w-full py-2.5 px-3.5 text-xs font-mono font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                            <p class="text-[11px] text-slate-400 mt-1.5 font-medium">{{ __('Unique identifier code for the village.') }}</p>
                            <x-input-error class="mt-1.5" :messages="$errors->get('code')" />
                        </div>

                        <!-- Village Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('VILLAGE NAME') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="name" name="name" type="text" value="{{ old('name', $village->name) }}" required
                                   class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                        </div>
                    </div>

                    <!-- Full Address -->
                    <div>
                        <label for="address" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                            {{ __('FULL ADDRESS / SECTOR DETAILS') }} <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="address" name="address" rows="3" required
                                  class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all">{{ old('address', $village->address) }}</textarea>
                        <p class="text-[11px] text-slate-400 mt-1.5 font-medium">{{ __('Provide detailed address, nearby landmarks, or sector information') }}</p>
                        <x-input-error class="mt-1.5" :messages="$errors->get('address')" />
                    </div>
                </div>

                <!-- Section 2: Location Information (Card Container) -->
                <div x-data="{ open: true }" class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 shadow-2xs">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Location Information') }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ __('Geographical location parameters') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-6 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- State -->
                            <div>
                                <label for="state" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('STATE') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="state" name="state" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800">
                                    <option value="Punjab" selected>Punjab</option>
                                    <option value="Haryana">Haryana</option>
                                    <option value="Himachal Pradesh">Himachal Pradesh</option>
                                </select>
                            </div>

                            <!-- District -->
                            <div>
                                <label for="district" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('DISTRICT') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="district" name="district" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800">
                                    <option value="Ludhiana">Ludhiana</option>
                                    <option value="Amritsar">Amritsar</option>
                                    <option value="Jalandhar">Jalandhar</option>
                                    <option value="Patiala">Patiala</option>
                                    <option value="Malerkotla" selected>Malerkotla</option>
                                </select>
                            </div>

                            <!-- Tehsil / Block -->
                            <div>
                                <label for="tehsil" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('TEHSIL / BLOCK') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="tehsil" name="tehsil" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800">
                                    <option value="Central Tehsil">Central Tehsil</option>
                                    <option value="North Block">North Block</option>
                                    <option value="South Block">South Block</option>
                                    <option value="Malerkotla" selected>Malerkotla</option>
                                </select>
                            </div>

                            <!-- Pincode -->
                            <div>
                                <label for="pincode" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('PINCODE') }}
                                </label>
                                <input id="pincode" name="pincode" type="text" placeholder="e.g. 140001"
                                       class="w-full py-2.5 px-3.5 text-xs font-mono border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Status & Participation -->
                <div x-data="{ open: true, isActive: {{ old('status', $village->status) ? 'true' : 'false' }} }" class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 shadow-2xs">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#10B981] text-white flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Status & Participation') }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ __('Set the village status for milk collection') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#10B981] hover:text-emerald-700">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="pt-2">
                        <div class="p-4 bg-slate-50/70 border border-slate-200/80 rounded-xl flex items-center gap-4">
                            <button type="button" @click="isActive = !isActive" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="isActive ? 'bg-[#005BAC]' : 'bg-slate-300'">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="isActive ? 'translate-x-5' : 'translate-x-0'"></span>
                            </button>
                            <input type="hidden" name="status" :value="isActive ? '1' : '0'">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900">{{ __('Active Status') }}</h4>
                                <p class="text-[11px] text-slate-500 font-medium">{{ __('Active villages can participate in daily milk collection') }}</p>
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
                    <a href="{{ route('villages.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
