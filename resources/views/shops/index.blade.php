<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Shops Management') }}" description="{{ __('Manage retail shop outlets, owner contact details, locations, and credit boundaries.') }}">
            <x-slot name="actions">
                @can('create', App\Models\Shop::class)
                    <a href="{{ route('shops.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Register New Shop') }}
                    </a>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ deleteModalOpen: false, shopToDelete: null, shopName: '' }" class="space-y-6">
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

        <!-- Filter Card -->
        <x-admin.card title="{{ __('Filter & Search Shops') }}">
            <form method="GET" action="{{ route('shops.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="search" :value="__('Search Shop')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full text-xs" :value="request('search')" placeholder="{{ __('Name, code, owner, phone...') }}" />
                </div>

                <div>
                    <x-input-label for="village_id" :value="__('Village / Location')" />
                    <select id="village_id" name="village_id" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Villages / Locations') }}</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}" {{ request('village_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} ({{ $v->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="w-full py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition">
                        {{ __('Filter') }}
                    </button>
                    @if(request()->hasAny(['search', 'village_id', 'status']))
                        <a href="{{ route('shops.index') }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Shops Table Card -->
        <x-admin.card title="{{ __('Shops Directory') }}" description="{{ __('Comprehensive list of retail shops receiving center dispatch.') }}">
            @if($shops->isEmpty())
                <div class="text-center py-10 text-slate-400 text-xs">
                    {{ __('No shop outlets found matching your criteria.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Code') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Shop Name') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Owner & Phone') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Village / Area') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Credit Limit') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($shops as $shop)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="pl-6 py-3.5 whitespace-nowrap text-xs font-mono font-bold text-slate-700">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200">{{ $shop->shop_code }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-800">
                                        <a href="{{ route('shops.show', $shop) }}" class="hover:text-indigo-600 transition">
                                            {{ $shop->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                        <div class="font-medium text-slate-800">{{ $shop->owner_name }}</div>
                                        <div class="text-3xs text-slate-400 font-mono">{{ $shop->phone }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                        @if($shop->village)
                                            <span class="font-medium text-slate-700">{{ $shop->village->name }}</span>
                                        @else
                                            <span class="text-slate-400 italic">{{ __('N/A') }}</span>
                                        @endif
                                        @if($shop->area)
                                            <span class="block text-3xs text-slate-400">{{ $shop->area }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-700 font-mono">
                                        ₹{{ number_format($shop->credit_limit, 2) }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-xs">
                                        @if($shop->status)
                                            <span class="px-2.5 py-1 inline-flex text-xxs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wider">
                                                {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-xxs font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wider">
                                                {{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pr-6 py-3.5 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                        <!-- View -->
                                        <a href="{{ route('shops.show', $shop) }}" class="text-slate-600 hover:text-indigo-600 font-semibold" title="{{ __('View Details') }}">
                                            {{ __('View') }}
                                        </a>

                                        <!-- Edit -->
                                        @can('update', $shop)
                                            <a href="{{ route('shops.edit', $shop) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold" title="{{ __('Edit') }}">
                                                {{ __('Edit') }}
                                            </a>

                                            <!-- Toggle Status -->
                                            <form method="POST" action="{{ route('shops.toggle-status', $shop) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-amber-600 hover:text-amber-800 font-semibold" title="{{ $shop->status ? __('Deactivate') : __('Activate') }}">
                                                    {{ $shop->status ? __('Deactivate') : __('Activate') }}
                                                </button>
                                            </form>
                                        @endcan

                                        <!-- Delete -->
                                        @can('delete', $shop)
                                            <button type="button" 
                                                    @click="deleteModalOpen = true; shopToDelete = {{ $shop->id }}; shopName = '{{ addslashes($shop->name) }}'"
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
                    {{ $shops->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Delete Confirmation Modal -->
        @can('manage-shops')
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
                         aria-hidden="true"
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
                                    {{ __('Delete Shop') }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-xs text-slate-500">
                                        {{ __('Are you sure you want to delete shop') }} <strong x-text="shopName" class="text-slate-800"></strong>? {{ __('This action cannot be undone.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-3">
                            <form :action="'/shops/' + shopToDelete" method="POST" class="inline-block w-full sm:w-auto">
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
