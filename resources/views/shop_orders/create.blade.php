<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Create Shop Order') }}" description="{{ __('Place a new product order for a retail shop outlet with real-time stock validation.') }}">
            <x-slot name="actions">
                <a href="{{ route('shop-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Orders') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="max-w-5xl mx-auto" x-data="orderForm({{ json_encode($products) }})">
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

        <form method="POST" action="{{ route('shop-orders.store') }}" class="space-y-6">
            @csrf

            <!-- Header Info Card -->
            <x-admin.card title="{{ __('Order Header Information') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Shop Select -->
                    <div>
                        <x-input-label for="shop_id" :value="__('Select Retail Shop')" />
                        <select id="shop_id" name="shop_id" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" required>
                            <option value="">{{ __('-- Select Shop --') }}</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->name }} ({{ $shop->shop_code }}) - {{ $shop->owner_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('shop_id')" class="mt-1" />
                    </div>

                    <!-- Order Date -->
                    <div>
                        <x-input-label for="order_date" :value="__('Order Date')" />
                        <x-text-input id="order_date" name="order_date" type="date" class="mt-1 block w-full text-xs font-mono" :value="old('order_date', date('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('order_date')" class="mt-1" />
                    </div>

                    <!-- Initial Status -->
                    <div>
                        <x-input-label for="status" :value="__('Order Status')" />
                        <select id="status" name="status" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" required>
                            <option value="confirmed" {{ old('status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed (Deduct Stock Now)') }}</option>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending (Draft / Reserve Stock Later)') }}</option>
                            <option value="preparing" {{ old('status') === 'preparing' ? 'selected' : '' }}>{{ __('Preparing') }}</option>
                            <option value="dispatched" {{ old('status') === 'dispatched' ? 'selected' : '' }}>{{ __('Dispatched') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>
            </x-admin.card>

            <!-- Order Items Card -->
            <x-admin.card title="{{ __('Select Dairy Products & Quantities') }}" description="{{ __('Add one or more products to this shop order.') }}">
                <div class="space-y-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th scope="col" class="pl-4 py-2.5 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Product') }}</th>
                                    <th scope="col" class="px-3 py-2.5 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Available Stock') }}</th>
                                    <th scope="col" class="px-3 py-2.5 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Quantity') }}</th>
                                    <th scope="col" class="px-3 py-2.5 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Unit Price (₹)') }}</th>
                                    <th scope="col" class="px-3 py-2.5 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Line Total (₹)') }}</th>
                                    <th scope="col" class="pr-4 py-2.5 text-right text-xxs font-bold text-slate-450 uppercase"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <!-- Product select -->
                                        <td class="pl-4 py-3 w-1/3">
                                            <select :name="'items['+index+'][product_id]'" 
                                                    x-model="item.product_id" 
                                                    @change="updateItemProduct(index)"
                                                    class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs" required>
                                                <option value="">{{ __('-- Select Product --') }}</option>
                                                <template x-for="p in availableProducts" :key="p.id">
                                                    <option :value="p.id" x-text="p.name + ' (' + p.unit + ')'"></option>
                                                </template>
                                            </select>
                                        </td>

                                        <!-- Available Stock -->
                                        <td class="px-3 py-3 whitespace-nowrap text-xs font-mono">
                                            <span :class="item.quantity > item.available_stock ? 'text-rose-600 font-bold' : 'text-slate-600'" x-text="item.available_stock + ' ' + item.unit"></span>
                                        </td>

                                        <!-- Quantity -->
                                        <td class="px-3 py-3 w-32">
                                            <input type="number" step="0.01" min="0.01" 
                                                   :name="'items['+index+'][quantity]'" 
                                                   x-model.number="item.quantity" 
                                                   @input="recalculateLine(index)"
                                                   class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-mono" required />
                                        </td>

                                        <!-- Unit Price -->
                                        <td class="px-3 py-3 w-32">
                                            <input type="number" step="0.01" min="0" 
                                                   :name="'items['+index+'][unit_price]'" 
                                                   x-model.number="item.unit_price" 
                                                   @input="recalculateLine(index)"
                                                   class="block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-mono" required />
                                        </td>

                                        <!-- Line Total -->
                                        <td class="px-3 py-3 whitespace-nowrap text-right text-xs font-bold font-mono text-slate-800" x-text="'₹' + item.line_total.toFixed(2)">
                                        </td>

                                        <!-- Remove Row -->
                                        <td class="pr-4 py-3 text-right">
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-rose-500 hover:text-rose-700 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-2">
                        <button type="button" @click="addItem()" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            {{ __('+ Add Another Product Item') }}
                        </button>
                    </div>
                </div>
            </x-admin.card>

            <!-- Order Financial Summary Card -->
            <x-admin.card title="{{ __('Financial Summary & Notes') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="notes" :value="__('Order Notes / Special Delivery Instructions')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs" placeholder="{{ __('e.g. Deliver before 7:00 AM, require insulated crates...') }}">{{ old('notes') }}</textarea>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80 space-y-3 text-xs">
                        <div class="flex justify-between items-center font-medium text-slate-600">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="font-mono text-slate-800 font-bold" x-text="'₹' + subtotal.toFixed(2)"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <label for="discount" class="font-medium text-slate-600">{{ __('Discount (₹)') }}</label>
                            <input id="discount" name="discount" type="number" step="0.01" min="0" x-model.number="discount" @input="calculateTotals()" class="w-28 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-mono text-right" />
                        </div>

                        <div class="border-t border-slate-200 pt-2 flex justify-between items-center font-bold text-slate-900 text-sm">
                            <span>{{ __('Estimated Total Amount') }}</span>
                            <span class="font-mono text-indigo-600 text-base" x-text="'₹' + totalAmount.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-4 border-t border-slate-100">
                    <a href="{{ route('shop-orders.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition">
                        {{ __('Submit Order') }}
                    </button>
                </div>
            </x-admin.card>
        </form>
    </div>

    <script>
        function orderForm(productsList) {
            return {
                availableProducts: productsList,
                items: [
                    { product_id: productsList.length > 0 ? productsList[0].id : '', quantity: 1, unit_price: productsList.length > 0 ? productsList[0].unit_price : 0, line_total: productsList.length > 0 ? productsList[0].unit_price : 0, available_stock: productsList.length > 0 ? productsList[0].available_stock : 0, unit: productsList.length > 0 ? productsList[0].unit : '' }
                ],
                discount: 0,
                subtotal: 0,
                totalAmount: 0,

                init() {
                    this.calculateTotals();
                },

                addItem() {
                    let defaultProd = this.availableProducts.length > 0 ? this.availableProducts[0] : null;
                    this.items.push({
                        product_id: defaultProd ? defaultProd.id : '',
                        quantity: 1,
                        unit_price: defaultProd ? defaultProd.unit_price : 0,
                        line_total: defaultProd ? defaultProd.unit_price : 0,
                        available_stock: defaultProd ? defaultProd.available_stock : 0,
                        unit: defaultProd ? defaultProd.unit : ''
                    });
                    this.calculateTotals();
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotals();
                    }
                },

                updateItemProduct(index) {
                    let selectedId = this.items[index].product_id;
                    let prod = this.availableProducts.find(p => p.id == selectedId);
                    if (prod) {
                        this.items[index].unit_price = prod.unit_price;
                        this.items[index].available_stock = prod.available_stock;
                        this.items[index].unit = prod.unit;
                        this.recalculateLine(index);
                    }
                },

                recalculateLine(index) {
                    let item = this.items[index];
                    item.line_total = (item.quantity || 0) * (item.unit_price || 0);
                    this.calculateTotals();
                },

                calculateTotals() {
                    let sum = 0;
                    this.items.forEach(item => {
                        sum += (item.quantity || 0) * (item.unit_price || 0);
                    });
                    this.subtotal = sum;
                    this.totalAmount = Math.max(0, sum - (this.discount || 0));
                }
            }
        }
    </script>
</x-admin-layout>
