<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Shop Orders') }}" description="{{ __('Manage retail shop sales dispatches, product item selections, and inventory allocations.') }}">
            <x-slot name="actions">
                @can('create', App\Models\ShopOrder::class)
                    <a href="{{ route('shop-orders.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005BAC] hover:bg-[#003B73] active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
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

        <!-- Filter Card -->
        <x-admin.card title="{{ __('Filter & Search Orders') }}">
            <form method="GET" action="{{ route('shop-orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Search Order / Shop') }}</label>
                    <input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="ORD-2026-0001, shop..." class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                </div>

                <div>
                    <label for="shop_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Shop Outlet') }}</label>
                    <select id="shop_id" name="shop_id" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Shops') }}</option>
                        @foreach($shops as $s)
                            <option value="{{ $s->id }}" {{ request('shop_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->shop_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Order Status') }}</label>
                    <select id="status" name="status" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="dispatched" {{ request('status') === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Order Date') }}</label>
                    <input id="date" name="date" type="date" value="{{ request('date') }}" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                </div>

                <div class="flex gap-2">
                    <x-primary-button type="submit" class="w-full justify-center">
                        {{ __('Filter') }}
                    </x-primary-button>
                    @if(request()->hasAny(['search', 'shop_id', 'status', 'date']))
                        <a href="{{ route('shop-orders.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Orders Table Card -->
        <x-admin.card title="{{ __('Shop Orders Directory') }}" description="{{ __('Log of retail shop orders and dispatch states.') }}">
            @if($orders->isEmpty())
                <div class="text-center py-12 text-slate-400 text-xs font-semibold">
                    {{ __('No shop orders found matching your search criteria.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-200/80">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Order No.') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Shop') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Items Summary') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Created By') }}</th>
                                <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($orders as $order)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('shop-orders.show', $order) }}" class="font-mono text-xs font-extrabold text-[#005BAC] hover:underline">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-xs font-bold text-slate-900">{{ $order->shop->name }}</div>
                                        <div class="text-[11px] font-semibold text-slate-400 font-mono">{{ $order->shop->shop_code }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-600 font-mono font-semibold">
                                        {{ $order->order_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-600 max-w-xs truncate">
                                        <span class="font-bold text-slate-900">{{ $order->items->count() }} {{ __('item(s)') }}:</span>
                                        <span class="text-slate-500 font-medium">{{ $order->items->pluck('product_name')->join(', ') }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-xs font-black text-slate-900 font-mono">₹{{ number_format($order->total_amount, 2) }}</div>
                                        @if($order->discount > 0)
                                            <div class="text-[10px] text-emerald-600 font-semibold font-mono">-₹{{ number_format($order->discount, 2) }} disc</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs">
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
                                        <span class="px-2.5 py-1 inline-flex text-[10px] font-extrabold rounded-full border uppercase tracking-wider {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        @if($order->stock_deducted)
                                            <span class="ml-1 px-1.5 py-0.5 text-[9px] font-extrabold rounded bg-emerald-100 text-emerald-800" title="{{ __('Stock Deducted') }}">
                                                ✓ {{ __('Stock Out') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-600 font-medium">
                                        {{ $order->creator->name ?? __('System') }}
                                    </td>
                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-3">
                                        <a href="{{ route('shop-orders.show', $order) }}" class="text-[#005BAC] hover:text-[#003B73] font-bold">{{ __('View Details') }}</a>

                                        @can('delete', $order)
                                            <button type="button" 
                                                    @click="deleteModalOpen = true; orderToDelete = {{ $order->id }}; orderNumber = '{{ $order->order_number }}'"
                                                    class="text-rose-600 hover:text-rose-800 font-bold">
                                                {{ __('Delete') }}
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Delete Confirmation Modal -->
        @can('manage-orders')
            <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <div x-show="deleteModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                         @click="deleteModalOpen = false">
                    </div>
                    
                    <div x-show="deleteModalOpen"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="relative inline-block bg-white rounded-2xl p-6 text-left overflow-hidden shadow-2xl transform transition-all max-w-md w-full border border-slate-200">
                        
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-rose-50 border border-rose-100 text-rose-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">
                                    {{ __('Delete Shop Order') }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ __('Are you sure you want to delete order') }} <strong x-text="orderNumber" class="text-slate-800"></strong>? {{ __('If stock was previously deducted, it will be automatically returned to inventory.') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-row-reverse gap-3">
                            <form :action="'/shop-orders/' + orderToDelete" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit">
                                    {{ __('Confirm Delete') }}
                                </x-danger-button>
                            </form>
                            <x-secondary-button @click="deleteModalOpen = false">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</x-admin-layout>
