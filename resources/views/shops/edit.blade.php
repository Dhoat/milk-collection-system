<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Edit Shop') }}: {{ $shop->name }}" description="{{ __('Update retail shop details, owner contact information, and status.') }}">
            <x-slot name="actions">
                <a href="{{ route('shops.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Directory') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <x-admin.card title="{{ __('Update Shop Information') }}">
            <form method="POST" action="{{ route('shops.update', $shop) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Shop Code -->
                    <div>
                        <x-input-label for="shop_code" :value="__('Shop Code')" />
                        <x-text-input id="shop_code" name="shop_code" type="text" class="mt-1 block w-full text-xs font-mono" :value="old('shop_code', $shop->shop_code)" required />
                        <x-input-error :messages="$errors->get('shop_code')" class="mt-1" />
                    </div>

                    <!-- Shop Name -->
                    <div>
                        <x-input-label for="name" :value="__('Shop Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-xs" :value="old('name', $shop->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <!-- Owner Name -->
                    <div>
                        <x-input-label for="owner_name" :value="__('Owner Name')" />
                        <x-text-input id="owner_name" name="owner_name" type="text" class="mt-1 block w-full text-xs" :value="old('owner_name', $shop->owner_name)" required />
                        <x-input-error :messages="$errors->get('owner_name')" class="mt-1" />
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full text-xs font-mono" :value="old('phone', $shop->phone)" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address (Optional)')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-xs" :value="old('email', $shop->email)" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Credit Limit -->
                    <div>
                        <x-input-label for="credit_limit" :value="__('Credit Limit (₹)')" />
                        <x-text-input id="credit_limit" name="credit_limit" type="number" step="0.01" min="0" class="mt-1 block w-full text-xs font-mono" :value="old('credit_limit', number_format($shop->credit_limit, 2, '.', ''))" />
                        <x-input-error :messages="$errors->get('credit_limit')" class="mt-1" />
                    </div>

                    <!-- Village / Location -->
                    <div>
                        <x-input-label for="village_id" :value="__('Associated Village (Optional)')" />
                        <select id="village_id" name="village_id" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                            <option value="">{{ __('None / Direct City Outlet') }}</option>
                            @foreach($villages as $v)
                                <option value="{{ $v->id }}" {{ old('village_id', $shop->village_id) == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} ({{ $v->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('village_id')" class="mt-1" />
                    </div>

                    <!-- Area / Market Sub-location -->
                    <div>
                        <x-input-label for="area" :value="__('Area / Market Sub-location')" />
                        <x-text-input id="area" name="area" type="text" class="mt-1 block w-full text-xs" :value="old('area', $shop->area)" />
                        <x-input-error :messages="$errors->get('area')" class="mt-1" />
                    </div>

                    <!-- Status -->
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" required>
                            <option value="1" {{ old('status', (string)(int)$shop->status) === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="0" {{ old('status', (string)(int)$shop->status) === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <x-input-label for="address" :value="__('Full Address')" />
                    <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">{{ old('address', $shop->address) }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>

                <!-- Notes / Remarks -->
                <div>
                    <x-input-label for="notes" :value="__('Notes / Remarks (Optional)')" />
                    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">{{ old('notes', $shop->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('shops.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                        {{ __('Update Shop') }}
                    </button>
                </div>
            </form>
        </x-admin.card>
    </div>
</x-admin-layout>
