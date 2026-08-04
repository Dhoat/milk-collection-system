<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Record Milk Collection') }}
            </h2>
            <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('milk-collections.store') }}" 
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
                          }" 
                          class="space-y-6 max-w-2xl">
                        @csrf

                        <!-- General Selection Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Collection Date -->
                            <div>
                                <x-input-label for="collection_date" :value="__('Collection Date')" />
                                <x-text-input id="collection_date" name="collection_date" type="date" class="mt-1 block w-full" :value="old('collection_date', date('Y-m-d'))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('collection_date')" />
                            </div>

                            <!-- Shift -->
                            <div>
                                <x-input-label for="shift" :value="__('Shift')" />
                                <select id="shift" name="shift" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="morning" {{ old('shift', 'morning') === 'morning' ? 'selected' : '' }}>{{ __('Morning') }}</option>
                                    <option value="evening" {{ old('shift') === 'evening' ? 'selected' : '' }}>{{ __('Evening') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('shift')" />
                            </div>

                            <!-- Village Selector -->
                            <div>
                                <x-input-label for="village_id" :value="__('Village')" />
                                <select id="village_id" name="village_id" x-model="selectedVillage" @change="selectedFarmer = ''" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled selected>{{ __('Select active village...') }}</option>
                                    @foreach($villages as $village)
                                        <option value="{{ $village->id }}">
                                            {{ $village->name }} ({{ $village->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('village_id')" />
                            </div>

                            <!-- Farmer Selector (Dependent on Village Selector) -->
                            <div>
                                <x-input-label for="farmer_id" :value="__('Farmer')" />
                                <select id="farmer_id" name="farmer_id" x-model="selectedFarmer" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required :disabled="!selectedVillage">
                                    <option value="" disabled selected x-text="selectedVillage ? 'Select active farmer...' : 'First select a village...'"></option>
                                    <template x-for="farmer in filteredFarmers" :key="farmer.id">
                                        <option :value="farmer.id" :selected="selectedFarmer == farmer.id" x-text="farmer.name + ' (' + farmer.farmer_code + ')'"></option>
                                    </template>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('farmer_id')" />
                            </div>
                        </div>

                        <!-- Milk Specifications and Rates -->
                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-md font-semibold text-gray-700 mb-4">{{ __('Measurement & Payment') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Milk Quantity -->
                                <div>
                                    <x-input-label for="milk_quantity" :value="__('Milk Quantity (Liters)')" />
                                    <x-text-input id="milk_quantity" name="milk_quantity" type="number" step="0.01" class="mt-1 block w-full" x-model="milkQuantity" required autocomplete="off" />
                                    <x-input-error class="mt-2" :messages="$errors->get('milk_quantity')" />
                                </div>

                                <!-- Fat % -->
                                <div>
                                    <x-input-label for="fat" :value="__('Fat % (Optional)')" />
                                    <x-text-input id="fat" name="fat" type="number" step="0.01" class="mt-1 block w-full" :value="old('fat')" placeholder="e.g. 4.5" autocomplete="off" />
                                    <x-input-error class="mt-2" :messages="$errors->get('fat')" />
                                </div>

                                <!-- SNF % -->
                                <div>
                                    <x-input-label for="snf" :value="__('SNF % (Optional)')" />
                                    <x-text-input id="snf" name="snf" type="number" step="0.01" class="mt-1 block w-full" :value="old('snf')" placeholder="e.g. 8.5" autocomplete="off" />
                                    <x-input-error class="mt-2" :messages="$errors->get('snf')" />
                                </div>

                                <!-- Rate -->
                                <div>
                                    <x-input-label for="rate" :value="__('Rate per Liter (₹)')" />
                                    <x-text-input id="rate" name="rate" type="number" step="0.01" class="mt-1 block w-full" x-model="rate" required autocomplete="off" />
                                    <x-input-error class="mt-2" :messages="$errors->get('rate')" />
                                </div>

                                <!-- Calculated Amount (Disabled for manual entry) -->
                                <div>
                                    <x-input-label for="amount_display" :value="__('Total Amount (₹)')" />
                                    <x-text-input id="amount_display" type="text" class="mt-1 block w-full bg-gray-50 font-bold text-indigo-700" :value="0" x-bind:value="'₹ ' + amount" readonly disabled />
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes / Remarks')" />
                            <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('notes') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                            <x-primary-button>{{ __('Save Entry') }}</x-primary-button>
                            <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
