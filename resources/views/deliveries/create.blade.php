<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('New Delivery Dispatch') }}" description="{{ __('Create a vehicle dispatch for a confirmed shop order and assign delivery personnel.') }}">
            <x-slot name="actions">
                <a href="{{ route('deliveries.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition-all">
                    ← {{ __('Back to Dispatches') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6" x-data="deliveryCreateForm({{ json_encode($orders) }}, '{{ $selectedOrderId }}')">
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
            <form method="POST" action="{{ route('deliveries.store') }}" class="space-y-8">
                @csrf

                <!-- Section 1: Shop Order Selection -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Shop Order Selection') }}</h3>
                            <p class="text-xs text-slate-400 font-medium">{{ __('Link this dispatch to an existing confirmed shop order') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Select Shop Order -->
                        <div>
                            <label for="shop_order_id" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('SELECT SHOP ORDER') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="shop_order_id" name="shop_order_id" x-model="selectedOrder" @change="onOrderChange()" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                <option value="">{{ __('-- Select Shop Order --') }}</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}">
                                        {{ $order->order_number }} - {{ $order->shop->name }} (₹{{ number_format($order->total_amount, 2) }}) [{{ ucfirst($order->status) }}]
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('shop_order_id')" class="mt-1.5" />
                        </div>

                        <!-- Scheduled Delivery Date -->
                        <div>
                            <label for="delivery_date" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('SCHEDULED DELIVERY DATE') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="delivery_date" name="delivery_date" type="date" value="{{ old('delivery_date', date('Y-m-d')) }}" required
                                   class="w-full py-2.5 px-3.5 text-xs font-mono border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                            <x-input-error :messages="$errors->get('delivery_date')" class="mt-1.5" />
                        </div>
                    </div>

                    <!-- Selected Order Preview Box -->
                    <div x-show="selectedOrderDetails" class="p-4 bg-blue-50/70 border border-blue-100 rounded-2xl space-y-2 text-xs" style="display:none;">
                        <div class="font-extrabold text-slate-900 flex justify-between">
                            <span>Outlet: <span x-text="selectedOrderDetails ? selectedOrderDetails.shop.name : ''" class="text-[#005BAC]"></span></span>
                            <span class="font-mono text-[#005BAC] font-black" x-text="selectedOrderDetails ? '₹' + parseFloat(selectedOrderDetails.total_amount).toFixed(2) : ''"></span>
                        </div>
                        <div class="text-slate-600 font-semibold text-[11px]">
                            Items (<span x-text="selectedOrderDetails ? selectedOrderDetails.items.length : 0"></span>):
                            <template x-if="selectedOrderDetails">
                                <span x-text="selectedOrderDetails.items.map(i => i.product_name + ' x ' + i.quantity + ' ' + i.unit).join(', ')"></span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Staff Assignment & Destination Details -->
                <div x-data="{ open: true }" class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 shadow-2xs">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Staff Assignment & Recipient Details') }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ __('Driver selection and destination contact information') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-6 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Assign Personnel -->
                            <div>
                                <label for="assigned_to" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('ASSIGN DELIVERY PERSONNEL') }}
                                </label>
                                <select id="assigned_to" name="assigned_to" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800">
                                    <option value="">{{ __('-- Unassigned (Assign Later) --') }}</option>
                                    @foreach($staffUsers as $staff)
                                        <option value="{{ $staff->id }}" {{ old('assigned_to') == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }} ({{ ucfirst(str_replace('_', ' ', $staff->role)) }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('assigned_to')" class="mt-1.5" />
                            </div>

                            <!-- Initial Status -->
                            <div>
                                <label for="status" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('INITIAL DISPATCH STATUS') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="status" name="status" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                    <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="assigned" {{ old('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="out_for_delivery" {{ old('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
                            </div>

                            <!-- Contact Person -->
                            <div>
                                <label for="contact_person" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('CONTACT PERSON') }}
                                </label>
                                <input id="contact_person" name="contact_person" type="text" x-model="contactPerson" placeholder="Shop owner / receiver name"
                                       class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                                <x-input-error :messages="$errors->get('contact_person')" class="mt-1.5" />
                            </div>

                            <!-- Contact Phone -->
                            <div>
                                <label for="contact_phone" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('CONTACT PHONE') }}
                                </label>
                                <input id="contact_phone" name="contact_phone" type="text" x-model="contactPhone" placeholder="Mobile phone number"
                                       class="w-full py-2.5 px-3.5 text-xs font-mono border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                                <x-input-error :messages="$errors->get('contact_phone')" class="mt-1.5" />
                            </div>

                            <!-- Delivery Address -->
                            <div class="md:col-span-2">
                                <label for="delivery_address" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('DELIVERY ADDRESS') }}
                                </label>
                                <textarea id="delivery_address" name="delivery_address" rows="2" x-model="deliveryAddress" placeholder="Shop street address..."
                                          class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800"></textarea>
                                <x-input-error :messages="$errors->get('delivery_address')" class="mt-1.5" />
                            </div>

                            <!-- Driver Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('DRIVER NOTES / DELIVERY INSTRUCTIONS') }}
                                </label>
                                <textarea id="notes" name="notes" rows="2" placeholder="e.g. Call before arrival, drop at back door..."
                                          class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Create Dispatch Record') }}
                    </button>
                    <a href="{{ route('deliveries.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
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
