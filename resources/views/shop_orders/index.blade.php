<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Shop Orders Management') }}" description="{{ __('Manage retail shop sales dispatches, product item selections, and real-time inventory deductions.') }}">
            <x-slot name="actions">
                @can('create', App\Models\ShopOrder::class)
                    <a href="{{ route('shop-orders.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Create New Order') }}
                    </a>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ deleteModalOpen: false, orderToDelete: null, orderNumber: '' }" class="space-y-6">
        <!-- Flash Notification -->
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

        <!-- Filter Card -->
        <x-admin.card title="{{ __('Filter & Search Orders') }}">
            <form method="GET" action="{{ route('shop-orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <x-input-label for="search" :value="__('Search Order / Shop')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full text-xs" :value="request('search')" placeholder="{{ __('ORD-2026-0001, shop...') }}" />
                </div>

                <div>
                    <x-input-label for="shop_id" :value="__('Shop Outlet')" />
                    <select id="shop_id" name="shop_id" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Shops') }}</option>
                        @foreach($shops as $s)
                            <option value="{{ $s->id }}" {{ request('shop_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->shop_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="status" :value="__('Order Status')" />
                    <select id="status" name="status" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                        <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>{{ __('Preparing') }}</option>
                        <option value="dispatched" {{ request('status') === 'dispatched' ? 'selected' : '' }}>{{ __('Dispatched') }}</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="date" :value="__('Order Date')" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full text-xs" :value="request('date')" />
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="w-full py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition">
                        {{ __('Filter') }}
                    </button>
                    @if(request()->hasAny(['search', 'shop_id', 'status', 'date']))
                        <a href="{{ route('shop-orders.index') }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Orders Table Card -->
        <x-admin.card title="{{ __('Shop Orders Directory') }}" description="{{ __('Log of retail shop orders and dispatch states.') }}">
            @if($orders->isEmpty())
                <div class="text-center py-10 text-slate-400 text-xs">
                    {{ __('No shop orders found matching your search criteria.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Order No.') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Shop') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Items Summary') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Created By') }}</th>
                                <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($orders as $order)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="pl-6 py-3.5 whitespace-nowrap text-xs font-mono font-bold text-slate-700">
                                        <a href="{{ route('shop-orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-800">
                                        {{ $order->shop->name }}
                                        <span class="block text-3xs text-slate-400 font-mono">{{ $order->shop->shop_code }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600 font-mono">
                                        {{ $order->order_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-slate-600 max-w-xs truncate">
                                        <span class="font-bold text-slate-700">{{ $order->items->count() }} {{ __('item(s)') }}:</span>
                                        {{ $order->items->pluck('product_name')->join(', ') }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs font-bold text-slate-800 font-mono">
                                        ₹{{ number_format($order->total_amount, 2) }}
                                        @if($order->discount > 0)
                                            <span class="block text-3xs text-emerald-600 font-normal">-₹{{ number_format($order->discount, 2) }} disc</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs">
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
                                        <span class="px-2.5 py-1 inline-flex text-xxs font-bold rounded-full border uppercase tracking-wider {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        @if($order->stock_deducted)
                                            <span class="ml-1 px-1.5 py-0.5 text-3xs font-semibold rounded bg-emerald-100 text-emerald-800" title="{{ __('Stock Deducted') }}">
                                                ✓ {{ __('Stock Out') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                        {{ $order->creator->name ?? __('System') }}
                                    </td>
                                    <td class="pr-6 py-3.5 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                        <a href="{{ route('shop-orders.show', $order) }}" class="text-slate-600 hover:text-indigo-600 font-semibold">
                                            {{ __('View Details') }}
                                        </a>

                                        @can('delete', $order)
                                            <button type="button" 
                                                    @click="deleteModalOpen = true; orderToDelete = {{ $order->id }}; orderNumber = '{{ $order->order_number }}'"
                                                    class="text-rose-600 hover:text-rose-900 font-semibold">
                                                {{ __('Delete') }}
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Delete Confirmation Modal -->
        @can('manage-orders')
            <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="deleteModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 transition-opacity" 
                         @click="deleteModalOpen = false">
                        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div x-show="deleteModalOpen"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-slate-200">
                        
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-base font-bold text-slate-900" id="modal-title">
                                    {{ __('Delete Shop Order') }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-xs text-slate-500">
                                        {{ __('Are you sure you want to delete order') }} <strong x-text="orderNumber" class="text-slate-800"></strong>? {{ __('If stock was previously deducted, it will be automatically returned to inventory.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-3">
                            <form :action="'/shop-orders/' + orderToDelete" method="POST" class="inline-block w-full sm:w-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-xs font-semibold text-white hover:bg-red-700 focus:outline-none transition">
                                    {{ __('Confirm Delete') }}
                                </button>
                            </form>
                            <button type="button" @click="deleteModalOpen = false" class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-xl border border-slate-200 shadow-sm px-4 py-2 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none transition">
                                {{ __('Cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</x-admin-layout>
