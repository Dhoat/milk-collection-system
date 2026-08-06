<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Record Milk Intake') }}" description="{{ __('Register incoming milk batch details received from a village collection shift.') }}" />
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
    }" x-init="fetchExpectedValues(); $watch('villageId', value => fetchExpectedValues()); $watch('date', value => fetchExpectedValues()); $watch('shift', value => fetchExpectedValues());" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Form Area (Left) -->
        <div class="lg:col-span-2">
            <x-admin.card title="{{ __('Intake Verification Details') }}">
                <form method="POST" action="{{ route('milk-receivings.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date Input -->
                        <div>
                            <x-input-label for="receiving_date" :value="__('Receiving Date')" class="text-xs font-semibold text-slate-500 mb-1" />
                            <x-text-input id="receiving_date" name="receiving_date" type="date" class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-2" x-model="date" required />
                            <x-input-error :messages="$errors->get('receiving_date')" class="mt-2" />
                        </div>

                        <!-- Shift Select -->
                        <div>
                            <x-input-label for="shift" :value="__('Shift')" class="text-xs font-semibold text-slate-500 mb-1" />
                            <select id="shift" name="shift" class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-2" x-model="shift" required>
                                <option value="morning">{{ __('Morning') }}</option>
                                <option value="evening">{{ __('Evening') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('shift')" class="mt-2" />
                        </div>

                        <!-- Village Select -->
                        <div class="md:col-span-2">
                            <x-input-label for="village_id" :value="__('Source Village')" class="text-xs font-semibold text-slate-500 mb-1" />
                            <select id="village_id" name="village_id" class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-2 bg-white" x-model="villageId" required>
                                <option value="">{{ __('Select Village') }}</option>
                                @foreach($villages as $village)
                                    <option value="{{ $village->id }}">
                                        {{ $village->name }} ({{ $village->code }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('village_id')" class="mt-2" />
                        </div>

                        <!-- Received Quantity -->
                        <div>
                            <x-input-label for="received_quantity" :value="__('Actual Quantity Received (Liters)')" class="text-xs font-semibold text-slate-500 mb-1" />
                            <x-text-input id="received_quantity" name="received_quantity" type="number" step="0.01" min="0" class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-2" :value="old('received_quantity')" placeholder="e.g. 150.50" required />
                            <x-input-error :messages="$errors->get('received_quantity')" class="mt-2" />
                        </div>

                        <!-- Empty space or other items -->
                        <div></div>

                        <!-- Received Fat -->
                        <div>
                            <x-input-label for="received_fat" :value="__('Tested Fat (%)')" class="text-xs font-semibold text-slate-500 mb-1" />
                            <x-text-input id="received_fat" name="received_fat" type="number" step="0.01" min="0" max="100" class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-2" :value="old('received_fat')" placeholder="e.g. 4.2" />
                            <x-input-error :messages="$errors->get('received_fat')" class="mt-2" />
                        </div>

                        <!-- Received SNF -->
                        <div>
                            <x-input-label for="received_snf" :value="__('Tested SNF (%)')" class="text-xs font-semibold text-slate-500 mb-1" />
                            <x-text-input id="received_snf" name="received_snf" type="number" step="0.01" min="0" max="100" class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-2" :value="old('received_snf')" placeholder="e.g. 8.5" />
                            <x-input-error :messages="$errors->get('received_snf')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="__('Remarks / Notes')" class="text-xs font-semibold text-slate-500 mb-1" />
                            <textarea id="notes" name="notes" rows="3" class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs p-3" placeholder="Add any details about dispatch discrepancies, leakage or transportation notes...">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Submission buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('milk-receivings.index') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl font-semibold text-xxs text-slate-650 uppercase tracking-widest transition duration-150">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="py-2 text-xxs font-bold tracking-wider rounded-xl shadow-md shadow-indigo-200">
                            {{ __('Save Intake') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-admin.card>
        </div>

        <!-- Expected Status Preview Card (Right) -->
        <div class="space-y-6">
            <x-admin.card title="{{ __('Expected Batch Details') }}" description="{{ __('Data aggregated from farmer collections in the selected shift') }}">
                <div class="relative min-h-48">
                    <!-- Loading overlay -->
                    <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-3xs flex items-center justify-center z-10" style="display: none;">
                        <svg class="animate-spin h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <div class="space-y-5">
                        <!-- Helper guidance if village not selected -->
                        <div x-show="!villageId" class="text-center py-8 text-slate-400 text-xs">
                            {{ __('Select a village to fetch expected metrics.') }}
                        </div>

                        <!-- Expected Stats -->
                        <div x-show="villageId" class="space-y-5" style="display: none;">
                            <!-- Litres stat -->
                            <div class="p-3 bg-slate-50/70 border border-slate-100 rounded-xl">
                                <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider">{{ __('Expected Volume') }}</span>
                                <div class="text-xl font-bold text-slate-800 tracking-tight mt-0.5" x-text="expectedQty.toFixed(2) + ' Liters'"></div>
                            </div>

                            <!-- Quality metrics -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 bg-slate-50/70 border border-slate-100 rounded-xl">
                                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider">{{ __('Weighted Fat') }}</span>
                                    <div class="text-sm font-semibold text-slate-800 mt-0.5" x-text="expectedFat !== null ? expectedFat.toFixed(2) + ' %' : '-'"></div>
                                </div>
                                <div class="p-3 bg-slate-50/70 border border-slate-100 rounded-xl">
                                    <span class="text-xxs font-bold text-slate-400 uppercase tracking-wider">{{ __('Weighted SNF') }}</span>
                                    <div class="text-sm font-semibold text-slate-800 mt-0.5" x-text="expectedSnf !== null ? expectedSnf.toFixed(2) + ' %' : '-'"></div>
                                </div>
                            </div>

                            <!-- Collections counter -->
                            <div class="flex items-center justify-between text-xs border-t border-slate-100 pt-3">
                                <span class="text-slate-450">{{ __('Farmer Deliveries:') }}</span>
                                <span class="font-bold text-slate-700" x-text="farmerCount + ' collections'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Discrepancy Note Helper -->
            <div x-show="villageId" class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-5" style="display: none;">
                <h5 class="text-xs font-bold text-indigo-850 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Discrepancy Threshold') }}
                </h5>
                <p class="text-3xs text-indigo-650 leading-relaxed mt-1.5">
                    {{ __('If the actual quantity received deviates from the expected volume by more than 0.10 Liters, the system will automatically mark the status as a DISCREPANCY for follow-up review.') }}
                </p>
            </div>
        </div>

    </div>
</x-admin-layout>
