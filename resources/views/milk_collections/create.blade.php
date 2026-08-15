<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ __('Record Milk Collection') }}" 
            description="{{ __('Entry form for daily farmer milk deposits and quality measurement') }}">
            <x-slot name="actions">
                <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-xs transition-all">
                    ← {{ __('Back to List') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Main Form Card (Full Screen / Full Width) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 lg:p-8 space-y-8"
             x-data="{
                  selectedVillage: '{{ old('village_id') }}',
                  selectedFarmer: '{{ old('farmer_id') }}',
                  milkQuantity: '{{ old('milk_quantity', 0) }}',
                  rate: '{{ old('rate', 0) }}',
                  farmers: @js($farmers),
                  get filteredFarmers() {
                      if (!this.selectedVillage) return [];
                      return this.farmers.filter(f => f.village_id == this.selectedVillage);
                  },
                  get amount() {
                      let q = parseFloat(this.milkQuantity) || 0;
                      let r = parseFloat(this.rate) || 0;
                      return (q * r).toFixed(2);
                  }
              }">
            <form method="POST" action="{{ route('milk-collections.store') }}" class="space-y-8">
                @csrf

                <!-- Section 1: Session & Supplier Info -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 01-1.023-.547M19.428 15.428A2 2 0 0121 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 011.572-1.952m14.856 0A6 6 0 0017 12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v7a6 6 0 00-2.428 3.428M12 3v9"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Collection Session & Supplier') }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ __('Date, shift, and farmer selection') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Collection Date -->
                        <div>
                            <label for="collection_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Collection Date') }} <span class="text-rose-500">*</span>
                            </label>
                            <x-text-input id="collection_date" name="collection_date" type="date" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" :value="old('collection_date', date('Y-m-d'))" required />
                            <x-input-error class="mt-1.5" :messages="$errors->get('collection_date')" />
                        </div>

                        <!-- Shift -->
                        <div>
                            <label for="shift" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Shift') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="shift" name="shift" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" required>
                                <option value="morning" {{ old('shift', 'morning') === 'morning' ? 'selected' : '' }}>☀️ Morning Shift</option>
                                <option value="evening" {{ old('shift') === 'evening' ? 'selected' : '' }}>🌙 Evening Shift</option>
                            </select>
                            <x-input-error class="mt-1.5" :messages="$errors->get('shift')" />
                        </div>

                        <!-- Village Selector -->
                        <div>
                            <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Village Collection Center') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="village_id" name="village_id" x-model="selectedVillage" @change="selectedFarmer = ''" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" required>
                                <option value="" disabled selected>{{ __('Select active village...') }}</option>
                                @foreach($villages as $village)
                                    <option value="{{ $village->id }}">
                                        {{ $village->name }} ({{ $village->code }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1.5" :messages="$errors->get('village_id')" />
                        </div>

                        <!-- Farmer Selector -->
                        <div>
                            <label for="farmer_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Producer / Farmer') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="farmer_id" name="farmer_id" x-model="selectedFarmer" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" required :disabled="!selectedVillage">
                                <option value="" disabled selected x-text="selectedVillage ? 'Select active farmer...' : 'First select a village...'"></option>
                                <template x-for="farmer in filteredFarmers" :key="farmer.id">
                                    <option :value="farmer.id" :selected="selectedFarmer == farmer.id" x-text="farmer.name + ' (' + farmer.farmer_code + ')'"></option>
                                </template>
                            </select>
                            <x-input-error class="mt-1.5" :messages="$errors->get('farmer_id')" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Measurement & Settlement (Styled Blue Container Box) -->
                <div x-data="{ open: true }" class="bg-blue-50/40 border border-blue-100/80 rounded-2xl p-5 lg:p-6 space-y-5">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Measurement & Rate Settlement') }}</h3>
                                <p class="text-xs text-slate-500 font-medium">{{ __('Quantity in Litres, quality metrics (FAT/SNF), and calculated payout') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-5 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Milk Quantity -->
                            <div>
                                <label for="milk_quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Milk Quantity (Liters)') }} <span class="text-rose-500">*</span>
                                </label>
                                <x-text-input id="milk_quantity" name="milk_quantity" type="number" step="0.01" class="w-full py-2.5 px-3.5 text-base font-extrabold border-slate-200 rounded-xl shadow-xs bg-white text-slate-900" x-model="milkQuantity" required autocomplete="off" placeholder="0.00" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('milk_quantity')" />
                            </div>

                            <!-- Fat % -->
                            <div>
                                <label for="fat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Fat %') }}
                                </label>
                                <x-text-input id="fat" name="fat" type="number" step="0.01" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs bg-white" :value="old('fat')" placeholder="e.g. 4.5" autocomplete="off" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('fat')" />
                            </div>

                            <!-- SNF % -->
                            <div>
                                <label for="snf" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('SNF %') }}
                                </label>
                                <x-text-input id="snf" name="snf" type="number" step="0.01" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs bg-white" :value="old('snf')" placeholder="e.g. 8.5" autocomplete="off" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('snf')" />
                            </div>

                            <!-- Rate per Liter -->
                            <div>
                                <label for="rate" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Rate per Liter (₹)') }} <span class="text-rose-500">*</span>
                                </label>
                                <x-text-input id="rate" name="rate" type="number" step="0.01" class="w-full py-2.5 px-3.5 text-xs font-mono font-bold border-slate-200 rounded-xl shadow-xs bg-white" x-model="rate" required autocomplete="off" placeholder="0.00" />
                                <x-input-error class="mt-1.5" :messages="$errors->get('rate')" />
                            </div>

                            <!-- Calculated Payout Card -->
                            <div class="md:col-span-2 bg-white/90 border border-blue-200 rounded-xl p-4 flex items-center justify-between shadow-xs">
                                <div>
                                    <span class="text-xs font-extrabold text-[#005BAC] uppercase tracking-wider block">{{ __('Calculated Total Payout') }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium">{{ __('Quantity (Liters) × Rate per Liter') }}</span>
                                </div>
                                <span class="text-2xl font-black text-[#005BAC]" x-text="'₹ ' + amount">₹ 0.00</span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('Notes / Quality Remarks') }}
                            </label>
                            <textarea id="notes" name="notes" rows="2" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" placeholder="Any quality or temperature remarks...">{{ old('notes') }}</textarea>
                            <x-input-error class="mt-1.5" :messages="$errors->get('notes')" />
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Save Collection Entry') }}
                    </button>
                    <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
