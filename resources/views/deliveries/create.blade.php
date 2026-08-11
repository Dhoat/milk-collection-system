<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('New Delivery Dispatch') }}" description="{{ __('Create a vehicle dispatch for a confirmed shop order and assign delivery personnel.') }}">
            <x-slot name="actions">
                <a href="{{ route('deliveries.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Dispatches') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="deliveryCreateForm({{ json_encode($orders) }}, '{{ $selectedOrderId }}')">
        <!-- Error alert -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-xl shadow-sm space-y-1">
                <div class="font-bold text-xs">{{ __('Please correct the validation errors below:') }}</div>
                <ul class="list-disc list-inside text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('deliveries.store') }}" class="space-y-6">
            @csrf

            <!-- Order Selection Card -->
            <x-admin.card title="{{ __('Shop Order Selection') }}" description="{{ __('Link this dispatch to an existing confirmed shop order.') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="shop_order_id" :value="__('Select Shop Order')" />
                        <select id="shop_order_id" name="shop_order_id" x-model="selectedOrder" @change="onOrderChange()" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" required>
                            <option value="">{{ __('-- Select Shop Order --') }}</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}">
                                    {{ $order->order_number }} - {{ $order->shop->name }} (₹{{ number_format($order->total_amount, 2) }}) [{{ ucfirst($order->status) }}]
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('shop_order_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="delivery_date" :value="__('Scheduled Delivery Date')" />
                        <x-text-input id="delivery_date" name="delivery_date" type="date" class="mt-1 block w-full text-xs font-mono" :value="old('delivery_date', date('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('delivery_date')" class="mt-1" />
                    </div>
                </div>

                <!-- Selected Order Preview Box -->
                <div x-show="selectedOrderDetails" class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs" style="display:none;">
                    <div class="font-bold text-slate-800 flex justify-between">
                        <span>Shop Outlet: <span x-text="selectedOrderDetails ? selectedOrderDetails.shop.name : ''"></span></span>
                        <span class="font-mono text-indigo-600 font-extrabold" x-text="selectedOrderDetails ? '₹' + parseFloat(selectedOrderDetails.total_amount).toFixed(2) : ''"></span>
                    </div>
                    <div class="text-slate-600 text-xxs">
                        Items (<span x-text="selectedOrderDetails ? selectedOrderDetails.items.length : 0"></span>):
                        <template x-if="selectedOrderDetails">
                            <span x-text="selectedOrderDetails.items.map(i => i.product_name + ' x ' + i.quantity + ' ' + i.unit).join(', ')"></span>
                        </template>
                    </div>
                </div>
            </x-admin.card>

            <!-- Dispatch Assignment & Contact Details Card -->
            <x-admin.card title="{{ __('Staff Assignment & Recipient Details') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="assigned_to" :value="__('Assign Delivery Personnel')" />
                        <select id="assigned_to" name="assigned_to" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                            <option value="">{{ __('-- Unassigned (Assign Later) --') }}</option>
                            @foreach($staffUsers as $staff)
                                <option value="{{ $staff->id }}" {{ old('assigned_to') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ ucfirst(str_replace('_', ' ', $staff->role)) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('assigned_to')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Initial Dispatch Status')" />
                        <select id="status" name="status" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" required>
                            <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="assigned" {{ old('status') === 'assigned' ? 'selected' : '' }}>{{ __('Assigned') }}</option>
                            <option value="out_for_delivery" {{ old('status') === 'out_for_delivery' ? 'selected' : '' }}>{{ __('Out for Delivery') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="contact_person" :value="__('Contact Person')" />
                        <x-text-input id="contact_person" name="contact_person" type="text" x-model="contactPerson" class="mt-1 block w-full text-xs" placeholder="{{ __('Shop owner / receiver name') }}" />
                        <x-input-error :messages="$errors->get('contact_person')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="contact_phone" :value="__('Contact Phone')" />
                        <x-text-input id="contact_phone" name="contact_phone" type="text" x-model="contactPhone" class="mt-1 block w-full text-xs font-mono" placeholder="{{ __('Mobile phone number') }}" />
                        <x-input-error :messages="$errors->get('contact_phone')" class="mt-1" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                        <textarea id="delivery_address" name="delivery_address" rows="2" x-model="deliveryAddress" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" placeholder="{{ __('Shop street address...') }}"></textarea>
                        <x-input-error :messages="$errors->get('delivery_address')" class="mt-1" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="notes" :value="__('Driver Notes / Delivery Instructions')" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" placeholder="{{ __('e.g. Call before arrival, drop at back door...') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 mt-4 border-t border-slate-100">
                    <a href="{{ route('deliveries.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                        {{ __('Create Dispatch Record') }}
                    </button>
                </div>
            </x-admin.card>
        </form>
    </div>

    <script>
        function deliveryCreateForm(ordersList, initialOrderId) {
            return {
                orders: ordersList,
                selectedOrder: initialOrderId || '',
                selectedOrderDetails: null,
                deliveryAddress: '',
                contactPerson: '',
                contactPhone: '',

                init() {
                    if (this.selectedOrder) {
                        this.onOrderChange();
                    }
                },

                onOrderChange() {
                    let found = this.orders.find(o => o.id == this.selectedOrder);
                    if (found) {
                        this.selectedOrderDetails = found;
                        this.deliveryAddress = found.shop.address || 'Main Shop Premises';
                        this.contactPerson = found.shop.owner_name || 'Shop Manager';
                        this.contactPhone = found.shop.phone || '';
                    } else {
                        this.selectedOrderDetails = null;
                        this.deliveryAddress = '';
                        this.contactPerson = '';
                        this.contactPhone = '';
                    }
                }
            }
        }
    </script>
</x-admin-layout>
