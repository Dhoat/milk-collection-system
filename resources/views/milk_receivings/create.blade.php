<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Record Milk Intake') }}" description="{{ __('Register incoming milk batch details received from a village collection shift.') }}">
            <x-slot name="actions">
                <a href="{{ route('milk-receivings.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-xs transition-all">
                    ← {{ __('Back to List') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <!-- Alpine.js form controller for real-time calculations -->
    <div x-data="{
        villageId: '{{ old('village_id', '') }}',
        date: '{{ old('receiving_date', date('Y-m-d')) }}',
        shift: '{{ old('shift', 'morning') }}',
        expectedQty: 0.00,
        expectedFat: null,
        expectedSnf: null,
        farmerCount: 0,
        loading: false,

        fetchExpectedValues() {
            if (!this.villageId || !this.date || !this.shift) {
                this.expectedQty = 0;
                this.expectedFat = null;
                this.expectedSnf = null;
                this.farmerCount = 0;
                return;
            }
            this.loading = true;
            fetch(`/api/village-collection-summary?village_id=${this.villageId}&date=${this.date}&shift=${this.shift}`)
                .then(res => {
                    if (!res.ok) throw new Error();
                    return res.json();
                })
                .then(data => {
                    this.expectedQty = data.expected_quantity;
                    this.expectedFat = data.expected_fat;
                    this.expectedSnf = data.expected_snf;
                    this.farmerCount = data.farmer_count;
                    this.loading = false;
                })
                .catch(() => {
                    this.expectedQty = 0;
                    this.expectedFat = null;
                    this.expectedSnf = null;
                    this.farmerCount = 0;
                    this.loading = false;
                });
        }
    }" x-init="fetchExpectedValues(); $watch('villageId', value => fetchExpectedValues()); $watch('date', value => fetchExpectedValues()); $watch('shift', value => fetchExpectedValues());" class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full">
        
        <!-- Form Area (Left Main Card) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 lg:p-8 space-y-8">
                <form method="POST" action="{{ route('milk-receivings.store') }}" class="space-y-8">
                    @csrf

                    <!-- Section 1: Session & Source -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Intake Verification Details') }}</h3>
                                <p class="text-xs text-slate-500 font-medium">{{ __('Log actual received batch quantity and quality test results') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date Input -->
                            <div>
                                <label for="receiving_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Receiving Date') }} <span class="text-rose-500">*</span>
                                </label>
                                <x-text-input id="receiving_date" name="receiving_date" type="date" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs" x-model="date" required />
                                <x-input-error :messages="$errors->get('receiving_date')" class="mt-1.5" />
                            </div>

                            <!-- Shift Select -->
                            <div>
                                <label for="shift" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Shift') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="shift" name="shift" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" x-model="shift" required>
                                    <option value="morning">☀️ {{ __('Morning Shift') }}</option>
                                    <option value="evening">🌙 {{ __('Evening Shift') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('shift')" class="mt-1.5" />
                            </div>

                            <!-- Village Select -->
                            <div class="md:col-span-2">
                                <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Source Village Center') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="village_id" name="village_id" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" x-model="villageId" required>
                                    <option value="">{{ __('Select Village Sector...') }}</option>
                                    @foreach($villages as $village)
                                        <option value="{{ $village->id }}">
                                            {{ $village->name }} ({{ $village->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('village_id')" class="mt-1.5" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Actual Received Quantity & Quality (Styled Blue Container Box) -->
                    <div x-data="{ open: true }" class="bg-blue-50/40 border border-blue-100/80 rounded-2xl p-5 lg:p-6 space-y-5">
                        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 01-1.023-.547M19.428 15.428A2 2 0 0121 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 011.572-1.952m14.856 0A6 6 0 0017 12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v7a6 6 0 00-2.428 3.428M12 3v9"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Actual Intake Metrics') }}</h3>
                                    <p class="text-xs text-slate-500 font-medium">{{ __('Quantity measured at central plant and lab sample metrics') }}</p>
                                </div>
                            </div>
                            <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                                <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>

                        <div x-show="open" x-collapse class="space-y-5 pt-2">
                            <!-- Received Quantity -->
                            <div>
                                <label for="received_quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Actual Quantity Received (Liters)') }} <span class="text-rose-500">*</span>
                                </label>
                                <x-text-input id="received_quantity" name="received_quantity" type="number" step="0.01" min="0" class="w-full py-2.5 px-3.5 text-base font-extrabold border-slate-200 rounded-xl shadow-xs bg-white text-slate-900" :value="old('received_quantity')" placeholder="e.g. 150.50" required />
                                <x-input-error :messages="$errors->get('received_quantity')" class="mt-1.5" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Received Fat -->
                                <div>
                                    <label for="received_fat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        {{ __('Tested Fat (%)') }}
                                    </label>
                                    <x-text-input id="received_fat" name="received_fat" type="number" step="0.01" min="0" max="100" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs bg-white" :value="old('received_fat')" placeholder="e.g. 4.2" />
                                    <x-input-error :messages="$errors->get('received_fat')" class="mt-1.5" />
                                </div>

                                <!-- Received SNF -->
                                <div>
                                    <label for="received_snf" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        {{ __('Tested SNF (%)') }}
                                    </label>
                                    <x-text-input id="received_snf" name="received_snf" type="number" step="0.01" min="0" max="100" class="w-full py-2.5 px-3.5 text-xs font-mono border-slate-200 rounded-xl shadow-xs bg-white" :value="old('received_snf')" placeholder="e.g. 8.5" />
                                    <x-input-error :messages="$errors->get('received_snf')" class="mt-1.5" />
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('Remarks / Notes') }}
                                </label>
                                <textarea id="notes" name="notes" rows="2" class="w-full py-2.5 px-3.5 text-xs font-medium border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-xs bg-white" placeholder="Add any details about dispatch discrepancies, leakage or transportation notes...">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
                            </div>
                        </div>
                    </div>

                    <!-- Submission buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('Save Intake') }}
                        </button>
                        <a href="{{ route('milk-receivings.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                            ✕ {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Expected Status Preview Card (Right Column) -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">{{ __('Expected Batch Details') }}</h3>
                    <p class="text-[11px] text-slate-500 font-medium">{{ __('Aggregated from village farmer collections') }}</p>
                </div>

                <div class="relative min-h-48">
                    <!-- Loading overlay -->
                    <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center z-10" style="display: none;">
                        <svg class="animate-spin h-6 w-6 text-[#005BAC]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <div class="space-y-5">
                        <!-- Helper guidance if village not selected -->
                        <div x-show="!villageId" class="text-center py-8 text-slate-400 text-xs font-semibold">
                            {{ __('Select a village to fetch expected metrics.') }}
                        </div>

                        <!-- Expected Stats -->
                        <div x-show="villageId" class="space-y-4" style="display: none;">
                            <!-- Volume stat -->
                            <div class="p-4 bg-blue-50/70 border border-blue-100 rounded-2xl">
                                <span class="text-[10px] font-extrabold text-[#005BAC] uppercase tracking-wider block">{{ __('Expected Volume') }}</span>
                                <div class="text-2xl font-black text-slate-900 tracking-tight mt-1" x-text="expectedQty.toFixed(2) + ' Liters'"></div>
                            </div>

                            <!-- Quality metrics -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Weighted Fat') }}</span>
                                    <div class="text-base font-extrabold text-slate-800 mt-0.5" x-text="expectedFat !== null ? expectedFat.toFixed(2) + ' %' : '-'"></div>
                                </div>
                                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Weighted SNF') }}</span>
                                    <div class="text-base font-extrabold text-slate-800 mt-0.5" x-text="expectedSnf !== null ? expectedSnf.toFixed(2) + ' %' : '-'"></div>
                                </div>
                            </div>

                            <!-- Collections counter -->
                            <div class="flex items-center justify-between text-xs border-t border-slate-100 pt-3">
                                <span class="text-slate-500 font-semibold">{{ __('Farmer Collections:') }}</span>
                                <span class="font-black text-[#005BAC]" x-text="farmerCount + ' entries'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discrepancy Note Helper -->
            <div x-show="villageId" class="bg-amber-50/70 border border-amber-200 rounded-2xl p-5" style="display: none;">
                <h5 class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Discrepancy Threshold') }}
                </h5>
                <p class="text-xs text-amber-800 leading-relaxed mt-1.5 font-medium">
                    {{ __('If actual received quantity deviates from expected volume by more than 0.10 Liters, the system will automatically mark the status as DISCREPANCY for review.') }}
                </p>
            </div>
        </div>
    </div>
</x-admin-layout>
