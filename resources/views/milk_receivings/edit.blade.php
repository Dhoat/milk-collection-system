<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Edit Milk Receiving Record') }}" description="{{ __('Update center intake measurements and quality test results for a received village batch.') }}">
            <x-slot name="actions">
                <a href="{{ route('milk-receivings.index') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-sm transition duration-150 ease-in-out">
                    {{ __('Back to List') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <x-admin.card title="{{ __('Milk Intake Entry Form') }}" description="{{ __('Modify the received quantity and fat/SNF values below.') }}">
            <form method="POST" action="{{ route('milk-receivings.update', $milkReceiving) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Village Selection -->
                    <div>
                        <x-input-label for="village_id" :value="__('Village')" />
                        <select id="village_id" name="village_id" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" required>
                            @foreach($villages as $village)
                                <option value="{{ $village->id }}" {{ old('village_id', $milkReceiving->village_id) == $village->id ? 'selected' : '' }}>
                                    {{ $village->name }} ({{ $village->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('village_id')" class="mt-2" />
                    </div>

                    <!-- Receiving Date -->
                    <div>
                        <x-input-label for="receiving_date" :value="__('Receiving Date')" />
                        <x-text-input id="receiving_date" name="receiving_date" type="date" class="mt-1 block w-full text-xs" :value="old('receiving_date', $milkReceiving->receiving_date->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('receiving_date')" class="mt-2" />
                    </div>

                    <!-- Shift -->
                    <div>
                        <x-input-label for="shift" :value="__('Shift')" />
                        <select id="shift" name="shift" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" required>
                            <option value="morning" {{ old('shift', $milkReceiving->shift) === 'morning' ? 'selected' : '' }}>{{ __('Morning') }}</option>
                            <option value="evening" {{ old('shift', $milkReceiving->shift) === 'evening' ? 'selected' : '' }}>{{ __('Evening') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('shift')" class="mt-2" />
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4">{{ __('Received Batch Measurements') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Received Quantity -->
                        <div>
                            <x-input-label for="received_quantity" :value="__('Received Quantity (Liters)')" />
                            <x-text-input id="received_quantity" name="received_quantity" type="number" step="0.01" min="0" class="mt-1 block w-full text-xs" :value="old('received_quantity', $milkReceiving->received_quantity)" required />
                            <x-input-error :messages="$errors->get('received_quantity')" class="mt-2" />
                        </div>

                        <!-- Received Fat -->
                        <div>
                            <x-input-label for="received_fat" :value="__('Tested Fat (%)')" />
                            <x-text-input id="received_fat" name="received_fat" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full text-xs" :value="old('received_fat', $milkReceiving->received_fat)" placeholder="e.g. 4.50" />
                            <x-input-error :messages="$errors->get('received_fat')" class="mt-2" />
                        </div>

                        <!-- Received SNF -->
                        <div>
                            <x-input-label for="received_snf" :value="__('Tested SNF (%)')" />
                            <x-text-input id="received_snf" name="received_snf" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full text-xs" :value="old('received_snf', $milkReceiving->received_snf)" placeholder="e.g. 8.50" />
                            <x-input-error :messages="$errors->get('received_snf')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <x-input-label for="notes" :value="__('Verification Notes / Discrepancy Remarks')" />
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" placeholder="{{ __('Optional notes regarding quality, tanker condition, or volume discrepancy...') }}">{{ old('notes', $milkReceiving->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('milk-receivings.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                        {{ __('Update Intake Record') }}
                    </button>
                </div>
            </form>
        </x-admin.card>
    </div>
</x-admin-layout>
