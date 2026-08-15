<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Delivery & Dispatches') }}" description="{{ __('Manage retail shop order dispatches, driver assignments, and delivery receipts.') }}">
            <x-slot name="actions">
                @can('create', App\Models\Delivery::class)
                    <a href="{{ route('deliveries.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005BAC] hover:bg-[#003B73] active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('New Delivery Dispatch') }}
                    </a>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ deleteModalOpen: false, deliveryToDelete: null, deliveryNumber: '' }" class="space-y-6">
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

        @if ($errors->has('status') || $errors->has('shop_order_id'))
            <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-xl shadow-sm flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-bold">{{ $errors->first('status') ?: $errors->first('shop_order_id') }}</span>
                </div>
                <button class="text-rose-500 hover:text-rose-700" onclick="this.parentElement.remove();">&times;</button>
            </div>
        @endif

        <!-- Filter Card -->
        <x-admin.card title="{{ __('Filter & Search Dispatches') }}">
            <form method="GET" action="{{ route('deliveries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Search Delivery / Order / Shop') }}</label>
                    <input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="DEL-2026-0001, ORD-..., shop" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Delivery Status') }}</label>
                    <select id="status" name="status" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
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
                    <label for="assigned_to" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Assigned Delivery Staff') }}</label>
                    <select id="assigned_to" name="assigned_to" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Staff') }}</option>
                        @foreach($staffUsers as $staff)
                            <option value="{{ $staff->id }}" {{ request('assigned_to') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <x-primary-button type="submit" class="w-full justify-center">
                        {{ __('Filter') }}
                    </x-primary-button>
                    @if(request()->hasAny(['search', 'status', 'shop_id', 'assigned_to', 'date']))
                        <a href="{{ route('deliveries.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Deliveries Table Card -->
        <x-admin.card title="{{ __('Dispatches Directory') }}" description="{{ __('Log of retail order deliveries and route assignments.') }}">
            @if($deliveries->isEmpty())
                <div class="text-center py-12 text-slate-400 text-xs font-semibold">
                    {{ __('No delivery dispatches found matching your search criteria.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-200/80">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Delivery No.') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Order No.') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Shop') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Delivery Date') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Assigned Staff') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($deliveries as $delivery)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap font-mono text-xs font-extrabold text-slate-900">
                                        <a href="{{ route('deliveries.show', $delivery) }}" class="text-[#005BAC] hover:underline">
                                            {{ $delivery->delivery_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap font-mono text-xs text-slate-600 font-bold">
                                        @if($delivery->shopOrder)
                                            <a href="{{ route('shop-orders.show', $delivery->shopOrder) }}" class="text-[#005BAC] hover:underline">
                                                {{ $delivery->shopOrder->order_number }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-xs font-bold text-slate-900">{{ $delivery->shop->name }}</div>
                                        <div class="text-[11px] font-semibold text-slate-400 font-mono">{{ $delivery->shop->shop_code }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-mono font-semibold text-slate-700">
                                        {{ $delivery->delivery_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-700">
                                        @if($delivery->assignedStaff)
                                            <span class="inline-flex items-center gap-1 font-bold text-slate-800">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                {{ $delivery->assignedStaff->name }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-extrabold border border-amber-200 uppercase tracking-wider">{{ __('Unassigned') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'assigned' => 'bg-blue-50 text-[#005BAC] border-blue-200',
                                                'out_for_delivery' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            ];
                                            $statusLabels = [
                                                'pending' => __('Pending'),
                                                'assigned' => __('Assigned'),
                                                'out_for_delivery' => __('Out for Delivery'),
                                                'delivered' => __('Delivered'),
                                                'failed' => __('Failed'),
                                                'cancelled' => __('Cancelled'),
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 inline-flex text-[10px] font-extrabold rounded-full border uppercase tracking-wider {{ $statusClasses[$delivery->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $statusLabels[$delivery->status] ?? ucfirst($delivery->status) }}
                                        </span>
                                    </td>
                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-3">
                                        <a href="{{ route('deliveries.show', $delivery) }}" class="text-[#005BAC] hover:text-[#003B73] font-bold">{{ __('View Details') }}</a>

                                        @can('delete', $delivery)
                                            <button type="button" 
                                                    @click="deleteModalOpen = true; deliveryToDelete = {{ $delivery->id }}; deliveryNumber = '{{ $delivery->delivery_number }}'"
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
                    {{ $deliveries->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Delete Confirmation Modal -->
        @can('manage-deliveries')
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
                                    {{ __('Delete Delivery Dispatch') }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ __('Are you sure you want to delete delivery dispatch') }} <strong x-text="deliveryNumber" class="text-slate-800"></strong>?
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-row-reverse gap-3">
                            <form :action="'/deliveries/' + deliveryToDelete" method="POST" class="inline">
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
