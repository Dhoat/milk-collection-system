<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Order Details') }}: {{ $order->order_number }}" description="{{ __('Shop dispatch order profile, item list, and inventory status.') }}">
            <x-slot name="actions">
                <div class="flex items-center gap-3">
                    <a href="{{ route('shop-orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ← {{ __('Back to Orders') }}
                    </a>
                </div>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="space-y-6">
        <!-- Flash & Error Messages -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-xl shadow-sm flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-bold">{{ session('success') }}</span>
                </div>
                <button class="text-emerald-500 hover:text-emerald-700" onclick="this.parentElement.remove();">&times;</button>
            </div>
        @endif

        @if ($errors->has('status'))
            <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-xl shadow-sm flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-bold">{{ $errors->first('status') }}</span>
                </div>
                <button class="text-rose-500 hover:text-rose-700" onclick="this.parentElement.remove();">&times;</button>
            </div>
        @endif

        <!-- Status & Actions Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-3">
                    <span class="text-xl font-mono font-black text-slate-900">{{ $order->order_number }}</span>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'confirmed' => 'bg-blue-50 text-[#005BAC] border-blue-200',
                            'preparing' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'dispatched' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-extrabold rounded-full border uppercase tracking-wider {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                    @if($order->stock_deducted)
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                            ✓ {{ __('Stock Deducted') }}
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wider">
                            {{ __('No Stock Deducted') }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 font-medium">
                    {{ __('Order Date:') }} <span class="font-mono text-slate-900 font-bold">{{ $order->order_date->format('F d, Y') }}</span> • 
                    {{ __('Created By:') }} <span class="text-slate-900 font-bold">{{ $order->creator->name ?? __('System') }}</span>
                </p>
            </div>

            <!-- Change Status Form -->
            @can('updateStatus', $order)
                <form method="POST" action="{{ route('shop-orders.update-status', $order) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <label for="update_status" class="text-xs font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap">{{ __('Status:') }}</label>
                    <select id="update_status" name="status" class="border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm text-xs py-2">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed (Deduct Stock)</option>
                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="dispatched" {{ $order->status === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled (Reverse Stock)</option>
                    </select>
                    <x-primary-button type="submit" class="py-2">
                        {{ __('Update') }}
                    </x-primary-button>
                </form>
            @endcan
        </div>

        <!-- Shop & Financial Overview Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Shop Information -->
            <x-admin.card title="{{ __('Shop & Contact Details') }}">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex justify-between">
                        <dt class="font-bold text-slate-500 uppercase">{{ __('Shop Name') }}</dt>
                        <dd class="font-extrabold text-slate-900">
                            <a href="{{ route('shops.show', $order->shop) }}" class="text-[#005BAC] hover:underline">
                                {{ $order->shop->name }}
                            </a>
                        </dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-bold text-slate-500 uppercase">{{ __('Shop Code') }}</dt>
                        <dd class="font-mono font-bold text-[#005BAC] bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-200/80">{{ $order->shop->shop_code }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-bold text-slate-500 uppercase">{{ __('Owner Name') }}</dt>
                        <dd class="font-extrabold text-slate-900">{{ $order->shop->owner_name }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-bold text-slate-500 uppercase">{{ __('Phone') }}</dt>
                        <dd class="font-mono font-bold text-slate-900">{{ $order->shop->phone }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-bold text-slate-500 uppercase">{{ __('Delivery Address') }}</dt>
                        <dd class="text-right text-slate-700 max-w-xs font-semibold">{{ $order->shop->address ?? __('N/A') }}</dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Financial Summary -->
            <x-admin.card title="{{ __('Financial & Stock Summary') }}">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex justify-between">
                        <dt class="font-bold text-slate-500 uppercase">{{ __('Items Subtotal') }}</dt>
                        <dd class="font-mono font-bold text-slate-900">₹{{ number_format($order->subtotal, 2) }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-bold text-slate-500 uppercase">{{ __('Applied Discount') }}</dt>
                        <dd class="font-mono font-bold text-emerald-600">-₹{{ number_format($order->discount, 2) }}</dd>
                    </div>
                    <div class="py-3 flex justify-between text-sm border-t border-slate-200">
                        <dt class="font-black text-slate-900 uppercase">{{ __('Net Total Amount') }}</dt>
                        <dd class="font-mono font-black text-[#005BAC] text-lg">₹{{ number_format($order->total_amount, 2) }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-bold text-slate-500 uppercase">{{ __('Special Notes') }}</dt>
                        <dd class="text-right text-slate-700 italic max-w-xs font-medium">{{ $order->notes ?? __('None') }}</dd>
                    </div>
                </dl>
            </x-admin.card>
        </div>

        <!-- Order Items Breakdown -->
        <x-admin.card title="{{ __('Ordered Products & Line Totals') }}">
            <div class="overflow-x-auto -mx-6 -my-6">
                <table class="min-w-full divide-y divide-slate-200/80">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Item #') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Product Name') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Unit') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Unit Price') }}</th>
                            <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Line Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($order->items as $index => $item)
                            <tr class="hover:bg-blue-50/20 transition-colors">
                                <td class="pl-6 py-4 whitespace-nowrap text-xs font-mono font-bold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs font-extrabold text-slate-900">
                                    {{ $item->product_name }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-600">
                                    <span class="px-2 py-0.5 bg-slate-100 rounded text-[10px] font-bold uppercase">{{ $item->unit }}</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs font-mono font-bold text-slate-800">
                                    {{ number_format($item->quantity, 2) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs font-mono text-slate-600 font-bold">
                                    ₹{{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-mono font-black text-slate-900">
                                    ₹{{ number_format($item->line_total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>

        <!-- Dispatch & Payment Integration Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Next Module: Delivery -->
            <x-admin.card title="{{ __('Delivery & Dispatch Status') }}" description="{{ __('Vehicle dispatch, driver assignment, and shop delivery receipt') }}">
                <div class="p-5 border-2 border-dashed border-slate-200 rounded-2xl text-center">
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                    <h5 class="text-xs font-bold text-slate-700 mb-1">{{ __('Delivery Dispatch Tracking') }}</h5>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto font-medium">{{ __('Assign vehicle dispatches directly from this order in the Deliveries module.') }}</p>
                </div>
            </x-admin.card>

            <!-- Payments -->
            <x-admin.card title="{{ __('Payment Ledger & Outstanding Balance') }}" description="{{ __('Track shop payment entries and invoice status') }}">
                <div class="p-5 border-2 border-dashed border-slate-200 rounded-2xl text-center">
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M10 20H4a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"></path></svg>
                    <h5 class="text-xs font-bold text-slate-700 mb-1">{{ __('Invoice Financial Ledger') }}</h5>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto font-medium">{{ __('Record partial/full cash payments against this order invoice.') }}</p>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
