<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Delivery Details') }}: {{ $delivery->delivery_number }}" description="{{ __('Delivery dispatch status, route details, recipient contact, and ordered items.') }}">
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

        <!-- Status & Update Controls Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <span class="text-lg font-mono font-extrabold text-slate-900">{{ $delivery->delivery_number }}</span>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'assigned' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'out_for_delivery' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full border uppercase tracking-wider {{ $statusClasses[$delivery->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ str_replace('_', ' ', ucfirst($delivery->status)) }}
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    {{ __('Linked Order:') }} 
                    <a href="{{ route('shop-orders.show', $delivery->shopOrder) }}" class="font-mono text-indigo-600 font-bold hover:underline">
                        {{ $delivery->shopOrder->order_number }}
                    </a> • 
                    {{ __('Delivery Date:') }} <span class="font-mono text-slate-700 font-semibold">{{ $delivery->delivery_date->format('F d, Y') }}</span>
                </p>
            </div>

            <!-- Status Transition Form -->
            @can('updateStatus', $delivery)
                <form method="POST" action="{{ route('deliveries.update-status', $delivery) }}" class="flex flex-wrap items-center gap-2">
                    @csrf
                    @method('PATCH')
                    
                    <select name="assigned_to" class="border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-1.5">
                        <option value="">{{ __('-- Unassigned --') }}</option>
                        @foreach($staffUsers as $staff)
                            <option value="{{ $staff->id }}" {{ $delivery->assigned_to == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs py-1.5">
                        <option value="pending" {{ $delivery->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="assigned" {{ $delivery->status === 'assigned' ? 'selected' : '' }}>{{ __('Assigned') }}</option>
                        <option value="out_for_delivery" {{ $delivery->status === 'out_for_delivery' ? 'selected' : '' }}>{{ __('Out for Delivery') }}</option>
                        <option value="delivered" {{ $delivery->status === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="failed" {{ $delivery->status === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                        <option value="cancelled" {{ $delivery->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>

                    <button type="submit" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition">
                        {{ __('Update Status') }}
                    </button>
                </form>
            @endcan
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recipient & Delivery Information -->
            <x-admin.card title="{{ __('Recipient & Route Details') }}">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Shop Outlet') }}</dt>
                        <dd class="font-bold text-slate-800">
                            <a href="{{ route('shops.show', $delivery->shop) }}" class="hover:text-indigo-600 underline">
                                {{ $delivery->shop->name }}
                            </a>
                        </dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Contact Person') }}</dt>
                        <dd class="font-semibold text-slate-800">{{ $delivery->contact_person }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Contact Phone') }}</dt>
                        <dd class="font-mono font-bold text-slate-800">{{ $delivery->contact_phone }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Assigned Staff') }}</dt>
                        <dd class="font-semibold text-slate-800">
                            {{ $delivery->assignedStaff->name ?? __('Unassigned') }}
                        </dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Delivery Address') }}</dt>
                        <dd class="text-right text-slate-700 max-w-xs">{{ $delivery->delivery_address }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Driver / Dispatch Notes') }}</dt>
                        <dd class="text-right text-slate-700 italic max-w-xs">{{ $delivery->notes ?? __('None') }}</dd>
                    </div>
                </dl>
            </x-admin.card>

            <!-- Dispatch Timeline -->
            <x-admin.card title="{{ __('Dispatch Audit & Timestamps') }}">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Dispatch Created At') }}</dt>
                        <dd class="font-mono text-slate-700">{{ $delivery->created_at->format('M d, Y - h:i A') }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Dispatched At (Out for Delivery)') }}</dt>
                        <dd class="font-mono text-slate-700">
                            {{ $delivery->dispatched_at ? $delivery->dispatched_at->format('M d, Y - h:i A') : __('Not yet dispatched') }}
                        </dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Delivered At (Receipt Confirmation)') }}</dt>
                        <dd class="font-mono text-slate-700">
                            {{ $delivery->delivered_at ? $delivery->delivered_at->format('M d, Y - h:i A') : __('Not yet delivered') }}
                        </dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-500">{{ __('Created By Staff') }}</dt>
                        <dd class="font-semibold text-slate-800">{{ $delivery->creator->name ?? __('System') }}</dd>
                    </div>
                </dl>
            </x-admin.card>
        </div>

        <!-- Ordered Products Summary -->
        <x-admin.card title="{{ __('Shop Order Line Items Breakdown') }}" description="{{ __('Products included in this delivery dispatch.') }}">
            <div class="overflow-x-auto -mx-6 -my-6">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Item #') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Product') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Unit') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Quantity') }}</th>
                            <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Unit Price') }}</th>
                            <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Line Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($delivery->shopOrder->items as $index => $item)
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

            <div class="mt-4 pt-4 border-t border-slate-100 flex justify-between items-center text-xs font-bold">
                <span class="text-slate-600">{{ __('Order Net Total Amount:') }}</span>
                <span class="text-indigo-600 font-mono text-base">₹{{ number_format($delivery->shopOrder->total_amount, 2) }}</span>
            </div>
        </x-admin.card>

        <!-- Payment Compatibility Placeholder Card -->
        <x-admin.card title="{{ __('Payment & Invoice Status') }}" description="{{ __('Recorded payments and collection ledger for this delivery.') }}">
            <div class="p-4 border-2 border-dashed border-slate-200 rounded-xl text-center">
                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <h5 class="text-xs font-bold text-slate-700 mb-1">{{ __('Payment Collection Ready') }}</h5>
                <p class="text-xxs text-slate-400 max-w-sm mx-auto">{{ __('The upcoming Payments module will connect to this delivery dispatch to log Cash-on-Delivery (COD) or credit payments.') }}</p>
            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
