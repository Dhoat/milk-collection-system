<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Farmer') }}: {{ $farmer->name }}
            </h2>
            <a href="{{ route('farmers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('farmers.update', $farmer) }}" class="space-y-6 max-w-2xl">
                        @csrf
                        @method('PUT')

                        <div class="border-b border-gray-100 pb-4 mb-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Personal & System Details') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Key identifier and contact details.') }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Farmer Code -->
                            <div>
                                <x-input-label for="farmer_code" :value="__('Farmer Code')" />
                                <x-text-input id="farmer_code" name="farmer_code" type="text" class="mt-1 block w-full" :value="old('farmer_code', $farmer->farmer_code)" required autocomplete="off" />
                                <x-input-error class="mt-2" :messages="$errors->get('farmer_code')" />
                            </div>

                            <!-- Village Dropdown -->
                            <div>
                                <x-input-label for="village_id" :value="__('Village')" />
                                <select id="village_id" name="village_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled>{{ __('Select village...') }}</option>
                                    @foreach($villages as $village)
                                        <option value="{{ $village->id }}" {{ old('village_id', $farmer->village_id) == $village->id ? 'selected' : '' }}>
                                            {{ $village->name }} ({{ $village->code }}) @if(!$village->status) *({{ __('Inactive') }})@endif
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('village_id')" />
                            </div>

                            <!-- Farmer Name -->
                            <div>
                                <x-input-label for="name" :value="__('Farmer Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $farmer->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Father Name -->
                            <div>
                                <x-input-label for="father_name" :value="__('Father Name')" />
                                <x-text-input id="father_name" name="father_name" type="text" class="mt-1 block w-full" :value="old('father_name', $farmer->father_name)" />
                                <x-input-error class="mt-2" :messages="$errors->get('father_name')" />
                            </div>

                            <!-- Gender -->
                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Select Gender...') }}</option>
                                    <option value="male" {{ old('gender', $farmer->gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                    <option value="female" {{ old('gender', $farmer->gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                    <option value="other" {{ old('gender', $farmer->gender) === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                            </div>

                            <!-- Joining Date -->
                            <div>
                                <x-input-label for="joining_date" :value="__('Joining Date')" />
                                <x-text-input id="joining_date" name="joining_date" type="date" class="mt-1 block w-full" :value="old('joining_date', $farmer->joining_date ? $farmer->joining_date->format('Y-m-d') : '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('joining_date')" />
                            </div>

                            <!-- Mobile -->
                            <div>
                                <x-input-label for="mobile" :value="__('Mobile Number')" />
                                <x-text-input id="mobile" name="mobile" type="text" class="mt-1 block w-full" :value="old('mobile', $farmer->mobile)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('mobile')" />
                            </div>

                            <!-- Alternate Mobile -->
                            <div>
                                <x-input-label for="alternate_mobile" :value="__('Alternate Mobile (Optional)')" />
                                <x-text-input id="alternate_mobile" name="alternate_mobile" type="text" class="mt-1 block w-full" :value="old('alternate_mobile', $farmer->alternate_mobile)" />
                                <x-input-error class="mt-2" :messages="$errors->get('alternate_mobile')" />
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('address', $farmer->address) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>

                        <div class="border-b border-gray-100 pt-6 pb-4 mb-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Bank & Payment Details') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Used for direct payouts to the farmer.') }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Bank Name -->
                            <div>
                                <x-input-label for="bank_name" :value="__('Bank Name')" />
                                <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" :value="old('bank_name', $farmer->bank_name)" />
                                <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
                            </div>

                            <!-- Account Number -->
                            <div>
                                <x-input-label for="account_number" :value="__('Account Number')" />
                                <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" :value="old('account_number', $farmer->account_number)" />
                                <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
                            </div>

                            <!-- IFSC Code -->
                            <div>
                                <x-input-label for="ifsc_code" :value="__('IFSC Code')" />
                                <x-text-input id="ifsc_code" name="ifsc_code" type="text" class="mt-1 block w-full text-uppercase" :value="old('ifsc_code', $farmer->ifsc_code)" />
                                <x-input-error class="mt-2" :messages="$errors->get('ifsc_code')" />
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="block pt-2">
                            <label for="status" class="inline-flex items-center">
                                <input id="status" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="status" value="1" {{ old('status', $farmer->status) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">{{ __('Active Status') }}</span>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                            <x-primary-button>{{ __('Update Farmer') }}</x-primary-button>
                            <a href="{{ route('farmers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
