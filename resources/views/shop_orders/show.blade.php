<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Order Details') }}: {{ $order->order_number }}" description="{{ __('Shop dispatch order profile, item list, and inventory status.') }}">
            <x-slot name="actions">
                <div class="flex items-center gap-3">
                    <a href="{{ route('shop-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Back to Orders') }}
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
                    <span class="text-xs font-medium">{{ session('success') }}</span>
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
                    <span class="text-xs font-medium">{{ $errors->first('status') }}</span>
                </div>
                <button class="text-rose-500 hover:text-rose-700" onclick="this.parentElement.remove();">&times;</button>
            </div>
        @endif

        <!-- Status & Actions Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <span class="text-lg font-mono font-extrabold text-slate-900">{{ $order->order_number }}</span>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'confirmed' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'preparing' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'dispatched' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full border uppercase tracking-wider {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                    @if($order->stock_deducted)
                        <span class="px-2 py-0.5 text-xxs font-bold rounded bg-emerald-100 text-emerald-800 border border-emerald-200">
                            ✓ {{ __('Stock Deducted') }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 text-xxs font-bold rounded bg-slate-100 text-slate-600 border border-slate-200">
                            {{ __('No Stock Deducted') }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500">
                    {{ __('Order Date:') }} <span class="font-mono text-slate-700 font-semibold">{{ $order->order_date->format('F d, Y') }}</span> • 
                    {{ __('Created By:') }} <span class="text-slate-700 font-semibold">{{ $order->creator->name ?? __('System') }}</span>
                </p>
            </div>

            <!-- Change Status Form -->
            @can('updateStatus', $order)
                <form method="POST" action="{{ route('shop-orders.update-status', $order) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <label for="update_status" class="text-xs font-semibold text-slate-600 whitespace-nowrap">{{ __('Change Status:') }}</label>
                    <select id="update_status" name="status" class="border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-1.5">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed (Deduct Stock)') }}</option>
                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>{{ __('Preparing') }}</option>
                        <option value="dispatched" {{ $order->status === 'dispatched' ? 'selected' : '' }}>{{ __('Dispatched') }}</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled (Reverse Stock)') }}</option>
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition">
                        {{ __('Update') }}
                    </button>
                </form>
            @endcan
        </div>

        <!-- Shop & Financial Overview Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Shop Information -->
            <x-admin.card title="{{ __('Shop & Contact Details') }}">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Shop Name') }}</dt>
                        <dd class="font-bold text-slate-800">
                            <a href="{{ route('shops.show', $order->shop) }}" class="hover:text-indigo-600 underline">
                                {{ $order->shop->name }}
                            </a>
                        </dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Shop Code') }}</dt>
                        <dd class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $order->shop->shop_code }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Owner Name') }}</dt>
                        <dd class="font-semibold text-slate-800">{{ $order->shop->owner_name }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Phone') }}</dt>
                        <dd class="font-mono font-bold text-slate-800">{{ $order->shop->phone }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Delivery Address') }}</dt>
                        <dd class="text-right text-slate-700 max-w-xs">{{ $order->shop->address ?? __('N/A') }}</dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Financial Summary -->
            <x-admin.card title="{{ __('Financial & Stock Summary') }}">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Items Subtotal') }}</dt>
                        <dd class="font-mono font-bold text-slate-800">₹{{ number_format($order->subtotal, 2) }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Applied Discount') }}</dt>
                        <dd class="font-mono font-semibold text-emerald-600">-₹{{ number_format($order->discount, 2) }}</dd>
                    </div>
                    <div class="py-3 flex justify-between text-sm border-t border-slate-200">
                        <dt class="font-bold text-slate-900">{{ __('Net Total Amount') }}</dt>
                        <dd class="font-mono font-extrabold text-indigo-600 text-base">₹{{ number_format($order->total_amount, 2) }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Special Notes') }}</dt>
                        <dd class="text-right text-slate-700 italic max-w-xs">{{ $order->notes ?? __('None') }}</dd>
                    </div>
                </dl>
            </x-admin.card>
        </div>

        <!-- Order Items Breakdown -->
        <x-admin.card title="{{ __('Ordered Products & Line Totals') }}">
            <div class="overflow-x-auto -mx-6 -my-6">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Item #') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Product Name') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Unit') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Quantity') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Unit Price') }}</th>
                            <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Line Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($order->items as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="pl-6 py-3.5 whitespace-nowrap text-xs font-mono text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-bold text-slate-800">
                                    {{ $item->product_name }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                    <span class="px-2 py-0.5 bg-slate-100 rounded text-3xs font-semibold">{{ $item->unit }}</span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-mono font-bold text-slate-700">
                                    {{ number_format($item->quantity, 2) }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-mono text-slate-600">
                                    ₹{{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="pr-6 py-3.5 whitespace-nowrap text-right text-xs font-mono font-bold text-slate-900">
                                    ₹{{ number_format($item->line_total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>

        <!-- Future Compatibility Modules Placeholder Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Next Module: Delivery -->
            <x-admin.card title="{{ __('Delivery & Dispatch Status') }}" description="{{ __('Log vehicle dispatch, driver assignment, and shop delivery receipt.') }}">
                <div class="p-4 border-2 border-dashed border-slate-200 rounded-xl text-center">
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                    <h5 class="text-xs font-bold text-slate-700 mb-1">{{ __('Delivery Dispatch Ready') }}</h5>
                    <p class="text-xxs text-slate-400 max-w-xs mx-auto">{{ __('The upcoming Delivery module will allow assigning vehicle dispatches directly from this order.') }}</p>
                </div>
            </x-admin.card>

            <!-- Future Module: Payments -->
            <x-admin.card title="{{ __('Payment Ledger & Outstanding Balance') }}" description="{{ __('Track shop payment entries and invoice status.') }}">
                <div class="p-4 border-2 border-dashed border-slate-200 rounded-xl text-center">
                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M10 20H4a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z"></path></svg>
                    <h5 class="text-xs font-bold text-slate-700 mb-1">{{ __('Invoice Financial Ledger Ready') }}</h5>
                    <p class="text-xxs text-slate-400 max-w-xs mx-auto">{{ __('The upcoming Payments module will record partial/full cash payments against this order.') }}</p>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
