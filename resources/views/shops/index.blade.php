<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Shops Directory') }}" description="{{ __('Manage retail shop outlets, owner contact details, locations, and credit limits.') }}">
            <x-slot name="actions">
                @can('create', App\Models\Shop::class)
                    <a href="{{ route('shops.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005BAC] hover:bg-[#003B73] active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
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
                    <span class="text-xs font-bold">{{ session('success') }}</span>
                </div>
                <button class="text-emerald-500 hover:text-emerald-700" onclick="this.parentElement.remove();">&times;</button>
            </div>
        @endif

        <!-- Filter Card -->
        <x-admin.card title="{{ __('Filter & Search Outlets') }}">
            <form method="GET" action="{{ route('shops.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Search Shop') }}</label>
                    <input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="Name, code, owner, phone..." class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                </div>

                <div>
                    <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Village / Location') }}</label>
                    <select id="village_id" name="village_id" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Locations') }}</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}" {{ request('village_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} ({{ $v->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Status') }}</label>
                    <select id="status" name="status" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <x-primary-button type="submit" class="w-full justify-center">
                        {{ __('Filter') }}
                    </x-primary-button>
                    @if(request()->hasAny(['search', 'village_id', 'status']))
                        <a href="{{ route('shops.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Shops Table Card -->
        <x-admin.card title="{{ __('Registered Shop Outlets') }}" description="{{ __('Retail shops receiving raw milk dispatch') }}">
            @if($shops->isEmpty())
                <div class="text-center py-12 text-slate-400 text-xs font-semibold">
                    {{ __('No shop outlets found matching your criteria.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-200/80">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Code') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Shop Name') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Owner & Phone') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Village / Area') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Credit Limit') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($shops as $shop)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200/80 font-mono text-xs font-bold text-[#005BAC]">{{ $shop->shop_code }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="{{ route('shops.show', $shop) }}" class="text-sm font-bold text-slate-900 hover:text-[#005BAC] transition-colors">
                                            {{ $shop->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-xs font-bold text-slate-800">{{ $shop->owner_name }}</div>
                                        <div class="text-[11px] font-semibold text-slate-400 font-mono">{{ $shop->phone }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($shop->village)
                                            <span class="text-xs font-bold text-slate-800 block">{{ $shop->village->name }}</span>
                                        @else
                                            <span class="text-xs text-slate-400 italic block">{{ __('N/A') }}</span>
                                        @endif
                                        @if($shop->area)
                                            <span class="text-[11px] text-slate-500 font-semibold block">{{ $shop->area }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-black text-slate-900 font-mono">
                                        ₹{{ number_format($shop->credit_limit, 2) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs">
                                        @if($shop->status)
                                            <span class="px-2.5 py-1 inline-flex text-[10px] font-extrabold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-[10px] font-extrabold rounded-full bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wider">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-3">
                                        <a href="{{ route('shops.show', $shop) }}" class="text-[#005BAC] hover:text-[#003B73] font-bold">{{ __('View') }}</a>
                                        @can('update', $shop)
                                            <a href="{{ route('shops.edit', $shop) }}" class="text-amber-600 hover:text-amber-800 font-bold">{{ __('Edit') }}</a>
                                            <form method="POST" action="{{ route('shops.toggle-status', $shop) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-slate-600 hover:text-slate-900 font-bold">
                                                    {{ $shop->status ? __('Deactivate') : __('Activate') }}
                                                </button>
                                            </form>
                                        @endcan
                                        @can('delete', $shop)
                                            <button type="button" 
                                                    @click="deleteModalOpen = true; shopToDelete = {{ $shop->id }}; shopName = '{{ addslashes($shop->name) }}'"
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
                    {{ $shops->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Delete Confirmation Modal -->
        @can('manage-shops')
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
                                    {{ __('Delete Shop Outlet') }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ __('Are you sure you want to delete shop') }} <strong x-text="shopName" class="text-slate-800"></strong>? {{ __('This action cannot be undone.') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-row-reverse gap-3">
                            <form :action="'/shops/' + shopToDelete" method="POST" class="inline">
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
