<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('System Settings') }}" description="{{ __('Configure business profile, milk procurement parameters, order numbering, and system behavior.') }}" />
    </x-slot>

    <div x-data="{ activeTab: 'business' }" class="space-y-6">
        <!-- Settings Category Navigation Tabs -->
        <div class="border-b border-slate-200">
            <nav class="-mb-px flex space-x-6 overflow-x-auto">
                <button type="button" @click="activeTab = 'business'" :class="activeTab === 'business' ? 'border-[#005BAC] text-[#005BAC]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider transition-all">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        {{ __('Business Profile') }}
                    </span>
                </button>

                <button type="button" @click="activeTab = 'milk'" :class="activeTab === 'milk' ? 'border-[#005BAC] text-[#005BAC]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider transition-all">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        {{ __('Milk Parameters') }}
                    </span>
                </button>

                <button type="button" @click="activeTab = 'orders'" :class="activeTab === 'orders' ? 'border-[#005BAC] text-[#005BAC]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider transition-all">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        {{ __('Order & Logistics') }}
                    </span>
                </button>

                <button type="button" @click="activeTab = 'system'" :class="activeTab === 'system' ? 'border-[#005BAC] text-[#005BAC]' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider transition-all">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        {{ __('System Config') }}
                    </span>
                </button>
            </nav>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. BUSINESS INFORMATION TAB -->
            <div x-show="activeTab === 'business'" class="space-y-6" x-cloak>
                <x-admin.card title="{{ __('Dairy / Business Profile') }}">
                    <div class="space-y-6">
                        <!-- Logo Upload Section -->
                        <div>
                            <label for="business_logo" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Dairy Business Logo') }}</label>
                            <div class="mt-2 flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-[#005BAC]/10 border border-[#005BAC]/20 flex items-center justify-center overflow-hidden">
                                    @if($settings['business_logo'])
                                        <img src="{{ asset('storage/' . $settings['business_logo']) }}" alt="Business Logo" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-8 h-8 text-[#005BAC]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <input id="business_logo" name="business_logo" type="file" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#005BAC] hover:file:bg-blue-100 cursor-pointer" />
                                    <p class="text-xs text-slate-400 font-medium">{{ __('Supported formats: PNG, JPG, JPEG, SVG. Maximum file size: 2 MB.') }}</p>
                                    <x-input-error :messages="$errors->get('business_logo')" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Business Name -->
                            <div>
                                <x-input-label for="business_name" :value="__('Dairy / Business Name')" class="font-bold text-slate-700" />
                                <x-text-input id="business_name" name="business_name" type="text" class="mt-1.5 block w-full text-sm font-bold" :value="old('business_name', $settings['business_name'])" required />
                                <x-input-error :messages="$errors->get('business_name')" class="mt-1.5" />
                            </div>

                            <!-- GST Number -->
                            <div>
                                <x-input-label for="business_gst" :value="__('GSTIN / Business Registration')" class="font-bold text-slate-700" />
                                <x-text-input id="business_gst" name="business_gst" type="text" class="mt-1.5 block w-full text-sm font-mono" :value="old('business_gst', $settings['business_gst'])" placeholder="e.g. 03AAAAA0000A1Z5" />
                                <x-input-error :messages="$errors->get('business_gst')" class="mt-1.5" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <!-- Phone -->
                            <div>
                                <x-input-label for="business_phone" :value="__('Contact Phone')" class="font-bold text-slate-700" />
                                <x-text-input id="business_phone" name="business_phone" type="text" class="mt-1.5 block w-full text-sm font-mono" :value="old('business_phone', $settings['business_phone'])" required />
                                <x-input-error :messages="$errors->get('business_phone')" class="mt-1.5" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="business_email" :value="__('Support Email')" class="font-bold text-slate-700" />
                                <x-text-input id="business_email" name="business_email" type="email" class="mt-1.5 block w-full text-sm font-mono" :value="old('business_email', $settings['business_email'])" required />
                                <x-input-error :messages="$errors->get('business_email')" class="mt-1.5" />
                            </div>

                            <!-- Website -->
                            <div>
                                <x-input-label for="business_website" :value="__('Website URL')" class="font-bold text-slate-700" />
                                <x-text-input id="business_website" name="business_website" type="url" class="mt-1.5 block w-full text-sm font-mono" :value="old('business_website', $settings['business_website'])" placeholder="https://dhoatdairy.com" />
                                <x-input-error :messages="$errors->get('business_website')" class="mt-1.5" />
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="business_address" :value="__('Street Address')" class="font-bold text-slate-700" />
                            <x-text-input id="business_address" name="business_address" type="text" class="mt-1.5 block w-full text-sm" :value="old('business_address', $settings['business_address'])" required />
                            <x-input-error :messages="$errors->get('business_address')" class="mt-1.5" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <!-- City -->
                            <div>
                                <x-input-label for="business_city" :value="__('City')" class="font-bold text-slate-700" />
                                <x-text-input id="business_city" name="business_city" type="text" class="mt-1.5 block w-full text-sm" :value="old('business_city', $settings['business_city'])" required />
                                <x-input-error :messages="$errors->get('business_city')" class="mt-1.5" />
                            </div>

                            <!-- State -->
                            <div>
                                <x-input-label for="business_state" :value="__('State')" class="font-bold text-slate-700" />
                                <x-text-input id="business_state" name="business_state" type="text" class="mt-1.5 block w-full text-sm" :value="old('business_state', $settings['business_state'])" required />
                                <x-input-error :messages="$errors->get('business_state')" class="mt-1.5" />
                            </div>

                            <!-- Pincode -->
                            <div>
                                <x-input-label for="business_pincode" :value="__('Postal / Pincode')" class="font-bold text-slate-700" />
                                <x-text-input id="business_pincode" name="business_pincode" type="text" class="mt-1.5 block w-full text-sm font-mono" :value="old('business_pincode', $settings['business_pincode'])" required />
                                <x-input-error :messages="$errors->get('business_pincode')" class="mt-1.5" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="business_description" :value="__('Business / Tagline Description')" class="font-bold text-slate-700" />
                            <textarea id="business_description" name="business_description" rows="3" class="mt-1.5 block w-full border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-sm">{{ old('business_description', $settings['business_description']) }}</textarea>
                            <x-input-error :messages="$errors->get('business_description')" class="mt-1.5" />
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- 2. MILK BUSINESS SETTINGS TAB -->
            <div x-show="activeTab === 'milk'" class="space-y-6" x-cloak>
                <x-admin.card title="{{ __('Milk Operations Configuration') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="milk_default_unit" :value="__('Default Volume Unit')" class="font-bold text-slate-700" />
                            <x-text-input id="milk_default_unit" name="milk_default_unit" type="text" class="mt-1.5 block w-full text-sm font-bold" :value="old('milk_default_unit', $settings['milk_default_unit'])" required />
                            <x-input-error :messages="$errors->get('milk_default_unit')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="milk_default_currency" :value="__('Currency Code')" class="font-bold text-slate-700" />
                            <x-text-input id="milk_default_currency" name="milk_default_currency" type="text" class="mt-1.5 block w-full text-sm font-mono uppercase" :value="old('milk_default_currency', $settings['milk_default_currency'])" required />
                            <x-input-error :messages="$errors->get('milk_default_currency')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="milk_currency_symbol" :value="__('Currency Symbol')" class="font-bold text-slate-700" />
                            <x-text-input id="milk_currency_symbol" name="milk_currency_symbol" type="text" class="mt-1.5 block w-full text-sm font-bold" :value="old('milk_currency_symbol', $settings['milk_currency_symbol'])" required />
                            <x-input-error :messages="$errors->get('milk_currency_symbol')" class="mt-1.5" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-4">
                        <div>
                            <x-input-label for="milk_base_fat" :value="__('Standard Base FAT (%)')" class="font-bold text-slate-700" />
                            <x-text-input id="milk_base_fat" name="milk_base_fat" type="number" step="0.1" class="mt-1.5 block w-full text-sm font-mono" :value="old('milk_base_fat', $settings['milk_base_fat'])" required />
                            <x-input-error :messages="$errors->get('milk_base_fat')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="milk_base_snf" :value="__('Standard Base SNF (%)')" class="font-bold text-slate-700" />
                            <x-text-input id="milk_base_snf" name="milk_base_snf" type="number" step="0.1" class="mt-1.5 block w-full text-sm font-mono" :value="old('milk_base_snf', $settings['milk_base_snf'])" required />
                            <x-input-error :messages="$errors->get('milk_base_snf')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="milk_default_shift" :value="__('Default Entry Shift')" class="font-bold text-slate-700" />
                            <select id="milk_default_shift" name="milk_default_shift" class="mt-1.5 block w-full border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-sm" required>
                                <option value="morning" {{ old('milk_default_shift', $settings['milk_default_shift']) === 'morning' ? 'selected' : '' }}>{{ __('Morning Shift') }}</option>
                                <option value="evening" {{ old('milk_default_shift', $settings['milk_default_shift']) === 'evening' ? 'selected' : '' }}>{{ __('Evening Shift') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('milk_default_shift')" class="mt-1.5" />
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- 3. ORDER & DELIVERY SETTINGS TAB -->
            <div x-show="activeTab === 'orders'" class="space-y-6" x-cloak>
                <x-admin.card title="{{ __('Order & Logistics Configuration') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="order_number_prefix" :value="__('Shop Order Number Prefix')" class="font-bold text-slate-700" />
                            <x-text-input id="order_number_prefix" name="order_number_prefix" type="text" class="mt-1.5 block w-full text-sm font-mono uppercase" :value="old('order_number_prefix', $settings['order_number_prefix'])" required />
                            <x-input-error :messages="$errors->get('order_number_prefix')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="delivery_number_prefix" :value="__('Delivery Dispatch Number Prefix')" class="font-bold text-slate-700" />
                            <x-text-input id="delivery_number_prefix" name="delivery_number_prefix" type="text" class="mt-1.5 block w-full text-sm font-mono uppercase" :value="old('delivery_number_prefix', $settings['delivery_number_prefix'])" required />
                            <x-input-error :messages="$errors->get('delivery_number_prefix')" class="mt-1.5" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                        <div>
                            <x-input-label for="default_order_status" :value="__('Default New Order Status')" class="font-bold text-slate-700" />
                            <select id="default_order_status" name="default_order_status" class="mt-1.5 block w-full border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-sm" required>
                                <option value="pending" {{ old('default_order_status', $settings['default_order_status']) === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="confirmed" {{ old('default_order_status', $settings['default_order_status']) === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('default_order_status')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="default_delivery_status" :value="__('Default Delivery Status')" class="font-bold text-slate-700" />
                            <select id="default_delivery_status" name="default_delivery_status" class="mt-1.5 block w-full border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-sm" required>
                                <option value="pending" {{ old('default_delivery_status', $settings['default_delivery_status']) === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="assigned" {{ old('default_delivery_status', $settings['default_delivery_status']) === 'assigned' ? 'selected' : '' }}>{{ __('Assigned') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('default_delivery_status')" class="mt-1.5" />
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- 4. SYSTEM SETTINGS TAB -->
            <div x-show="activeTab === 'system'" class="space-y-6" x-cloak>
                <x-admin.card title="{{ __('System Preferences & Control') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="system_timezone" :value="__('Application Timezone')" class="font-bold text-slate-700" />
                            <x-text-input id="system_timezone" name="system_timezone" type="text" class="mt-1.5 block w-full text-sm font-mono" :value="old('system_timezone', $settings['system_timezone'])" required />
                            <x-input-error :messages="$errors->get('system_timezone')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="system_date_format" :value="__('Display Date Format')" class="font-bold text-slate-700" />
                            <select id="system_date_format" name="system_date_format" class="mt-1.5 block w-full border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-sm" required>
                                <option value="Y-m-d" {{ old('system_date_format', $settings['system_date_format']) === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2026-08-11)</option>
                                <option value="d/m/Y" {{ old('system_date_format', $settings['system_date_format']) === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (11/08/2026)</option>
                                <option value="M d, Y" {{ old('system_date_format', $settings['system_date_format']) === 'M d, Y' ? 'selected' : '' }}>MMM DD, YYYY (Aug 11, 2026)</option>
                            </select>
                            <x-input-error :messages="$errors->get('system_date_format')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="system_pagination_limit" :value="__('Default Pagination Limit')" class="font-bold text-slate-700" />
                            <x-text-input id="system_pagination_limit" name="system_pagination_limit" type="number" class="mt-1.5 block w-full text-sm font-mono" :value="old('system_pagination_limit', $settings['system_pagination_limit'])" required />
                            <x-input-error :messages="$errors->get('system_pagination_limit')" class="mt-1.5" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                        <div>
                            <x-input-label for="customer_ordering_enabled" :value="__('Public / Customer Online Ordering')" class="font-bold text-slate-700" />
                            <select id="customer_ordering_enabled" name="customer_ordering_enabled" class="mt-1.5 block w-full border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-sm font-semibold" required>
                                <option value="1" {{ old('customer_ordering_enabled', $settings['customer_ordering_enabled']) == '1' ? 'selected' : '' }}>{{ __('Enabled (Allow shop portal ordering)') }}</option>
                                <option value="0" {{ old('customer_ordering_enabled', $settings['customer_ordering_enabled']) == '0' ? 'selected' : '' }}>{{ __('Disabled (Admin only ordering)') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('customer_ordering_enabled')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="maintenance_mode_enabled" :value="__('System Maintenance Mode')" class="font-bold text-slate-700" />
                            <select id="maintenance_mode_enabled" name="maintenance_mode_enabled" class="mt-1.5 block w-full border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-sm font-semibold" required>
                                <option value="0" {{ old('maintenance_mode_enabled', $settings['maintenance_mode_enabled']) == '0' ? 'selected' : '' }}>{{ __('Disabled (Normal Operational State)') }}</option>
                                <option value="1" {{ old('maintenance_mode_enabled', $settings['maintenance_mode_enabled']) == '1' ? 'selected' : '' }}>{{ __('Enabled (Restrict access for updates)') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('maintenance_mode_enabled')" class="mt-1.5" />
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Global Action Bar -->
            <div class="pt-6 border-t border-slate-200 flex justify-end">
                <x-primary-button type="submit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('Save All Configuration Settings') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-admin-layout>
