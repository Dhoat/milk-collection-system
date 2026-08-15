<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Create Shop Order') }}" description="{{ __('Place a new product order for a retail shop outlet with real-time stock validation.') }}">
            <x-slot name="actions">
                <a href="{{ route('shop-orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition-all">
                    ← {{ __('Back to Orders') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6" x-data="orderForm({{ json_encode($products) }})">
        <!-- Error alert -->
        @if ($errors->any())
            <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-2xl shadow-2xs space-y-1">
                <div class="font-bold text-xs">{{ __('Please correct the validation errors below:') }}</div>
                <ul class="list-disc list-inside text-xs font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form Container Card -->
        <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs p-6 lg:p-8 space-y-8">
            <form method="POST" action="{{ route('shop-orders.store') }}" class="space-y-8">
                @csrf

                <!-- Section 1: Order Header Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Order Header Information') }}</h3>
                            <p class="text-xs text-slate-400 font-medium">{{ __('Target retail shop outlet and initial status configuration') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Shop Select -->
                        <div>
                            <label for="shop_id" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('SELECT RETAIL SHOP') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="shop_id" name="shop_id" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                <option value="">{{ __('-- Select Shop --') }}</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                        {{ $shop->name }} ({{ $shop->shop_code }}) - {{ $shop->owner_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('shop_id')" class="mt-1.5" />
                        </div>

                        <!-- Order Date -->
                        <div>
                            <label for="order_date" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('ORDER DATE') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="order_date" name="order_date" type="date" value="{{ old('order_date', date('Y-m-d')) }}" required
                                   class="w-full py-2.5 px-3.5 text-xs font-mono border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                            <x-input-error :messages="$errors->get('order_date')" class="mt-1.5" />
                        </div>

                        <!-- Initial Status -->
                        <div>
                            <label for="status" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('ORDER STATUS') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="status" name="status" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                <option value="confirmed" {{ old('status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>Confirmed (Deduct Stock Now)</option>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending (Draft / Reserve Stock Later)</option>
                                <option value="preparing" {{ old('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                <option value="dispatched" {{ old('status') === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Product Items Container -->
                <div x-data="{ open: true }" class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 shadow-2xs">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Select Dairy Products & Quantities') }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ __('Add one or more products to this shop order') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-4 pt-2">
                        <div class="overflow-x-auto border border-slate-200/80 rounded-xl">
                            <table class="min-w-full divide-y divide-slate-200/80">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="pl-4 py-3 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Available Stock') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Unit Price (₹)') }}</th>
                                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Line Total (₹)') }}</th>
                                        <th scope="col" class="pr-4 py-3 text-right text-[11px] font-extrabold text-slate-500 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-blue-50/20 transition-colors">
                                            <!-- Product select -->
                                            <td class="pl-4 py-3 w-1/3">
                                                <select :name="'items['+index+'][product_id]'" 
                                                        x-model="item.product_id" 
                                                        @change="updateItemProduct(index)"
                                                        class="w-full py-2 px-3 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                                    <option value="">{{ __('-- Select Product --') }}</option>
                                                    <template x-for="p in availableProducts" :key="p.id">
                                                        <option :value="p.id" x-text="p.name + ' (' + p.unit + ')'"></option>
                                                    </template>
                                                </select>
                                            </td>

                                            <!-- Available Stock -->
                                            <td class="px-4 py-3 whitespace-nowrap text-xs font-mono">
                                                <span :class="item.quantity > item.available_stock ? 'text-rose-600 font-extrabold' : 'text-slate-700 font-bold'" x-text="item.available_stock + ' ' + item.unit"></span>
                                            </td>

                                            <!-- Quantity -->
                                            <td class="px-4 py-3 w-32">
                                                <input type="number" step="0.01" min="0.01" 
                                                       :name="'items['+index+'][quantity]'" 
                                                       x-model.number="item.quantity" 
                                                       @input="recalculateLine(index)"
                                                       class="w-full py-2 px-3 text-xs font-mono font-bold border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs" required />
                                            </td>

                                            <!-- Unit Price -->
                                            <td class="px-4 py-3 w-32">
                                                <input type="number" step="0.01" min="0" 
                                                       :name="'items['+index+'][unit_price]'" 
                                                       x-model.number="item.unit_price" 
                                                       @input="recalculateLine(index)"
                                                       class="w-full py-2 px-3 text-xs font-mono font-bold border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs" required />
                                            </td>

                                            <!-- Line Total -->
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-black font-mono text-slate-900" x-text="'₹' + item.line_total.toFixed(2)">
                                            </td>

                                            <!-- Remove Row -->
                                            <td class="pr-4 py-3 text-right">
                                                <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="pt-2">
                            <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#005BAC] hover:text-[#003B73]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                {{ __('+ Add Another Product Item') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Financial Summary & Notes -->
                <div x-data="{ open: true }" class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 shadow-2xs">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#10B981] text-white flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Financial Summary & Notes') }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ __('Calculate order totals, discount adjustments, and delivery instructions') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#10B981] hover:text-emerald-700">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-6 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="notes" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('ORDER NOTES / SPECIAL DELIVERY INSTRUCTIONS') }}
                                </label>
                                <textarea id="notes" name="notes" rows="4" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" placeholder="e.g. Deliver before 7:00 AM, require insulated crates...">{{ old('notes') }}</textarea>
                            </div>

                            <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 space-y-3 text-xs">
                                <div class="flex justify-between items-center font-bold text-slate-600">
                                    <span>{{ __('Subtotal') }}</span>
                                    <span class="font-mono text-slate-900 font-extrabold text-sm" x-text="'₹' + subtotal.toFixed(2)"></span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <label for="discount" class="font-bold text-slate-600">{{ __('Discount (₹)') }}</label>
                                    <input id="discount" name="discount" type="number" step="0.01" min="0" x-model.number="discount" @input="calculateTotals()" class="w-32 py-1.5 px-2.5 border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl text-xs font-mono font-bold text-right bg-white" />
                                </div>

                                <div class="border-t border-slate-200 pt-3 flex justify-between items-center font-extrabold text-slate-900 text-sm">
                                    <span>{{ __('Estimated Total Amount') }}</span>
                                    <span class="font-mono text-[#005BAC] text-xl font-black" x-text="'₹' + totalAmount.toFixed(2)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Submit Order') }}
                    </button>
                    <a href="{{ route('shop-orders.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
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
