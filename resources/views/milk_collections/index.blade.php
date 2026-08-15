<x-app-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ __('Daily Milk Collections') }}" 
            description="{{ __('Log and track daily farmer milk deposits, quality metrics (FAT/SNF), and calculated amounts') }}">
            <x-slot name="actions">
                @can('create', App\Models\MilkCollection::class)
                    <a href="{{ route('milk-collections.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005BAC] hover:bg-[#003B73] active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Record Collection') }}
                    </a>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ deleteModalOpen: false, deleteActionUrl: '', recordDetail: '' }">
        <!-- Search and Filter Card -->
        <x-admin.card class="mb-6">
            <form method="GET" action="{{ route('milk-collections.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <!-- Search term -->
                <div class="col-span-1 md:col-span-2">
                    <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Search Farmer') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input id="search" name="search" type="text" value="{{ $search }}" placeholder="Search farmer name or code..." 
                               class="w-full pl-10 pr-4 py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                    </div>
                </div>

                <!-- Village Filter -->
                <div>
                    <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Village') }}</label>
                    <select id="village_id" name="village_id" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Villages') }}</option>
                        @foreach($villages as $village)
                            <option value="{{ $village->id }}" {{ $villageId == $village->id ? 'selected' : '' }}>
                                {{ $village->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label for="date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Collection Date') }}</label>
                    <input id="date" name="date" type="date" value="{{ $date }}" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                </div>

                <!-- Shift Filter -->
                <div>
                    <label for="shift" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Shift') }}</label>
                    <select id="shift" name="shift" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Shifts') }}</option>
                        <option value="morning" {{ $shift === 'morning' ? 'selected' : '' }}>{{ __('Morning') }}</option>
                        <option value="evening" {{ $shift === 'evening' ? 'selected' : '' }}>{{ __('Evening') }}</option>
                    </select>
                </div>

                <!-- Submit Buttons -->
                <div class="col-span-1 md:col-span-5 flex justify-end gap-2.5 pt-2">
                    <x-primary-button type="submit">
                        {{ __('Filter Records') }}
                    </x-primary-button>
                    @if($search || $villageId || $farmerId || $date || $shift)
                        <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Collections Table Card -->
        <x-admin.card>
            @if($collections->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    <p class="text-sm font-semibold text-slate-600">{{ __('No milk collection entries found.') }}</p>
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-200/80">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Date & Shift') }}</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Farmer') }}</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Village') }}</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Quantity (L)') }}</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Fat %') }}</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('SNF %') }}</th>
                                @can('view-financials')
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Rate (₹)') }}</th>
                                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Amount (₹)') }}</th>
                                @endcan
                                <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($collections as $collection)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900">{{ $collection->collection_date->format('d-m-Y') }}</span>
                                            @if($collection->shift === 'morning')
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-md bg-amber-50 text-amber-800 border border-amber-200">
                                                    ☀️ Morning
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-md bg-blue-50 text-blue-800 border border-blue-200">
                                                    🌙 Evening
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-slate-900 block">{{ $collection->farmer->name }}</span>
                                        <span class="text-[11px] font-semibold text-slate-400 font-mono">{{ $collection->farmer->farmer_code }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-700">
                                        {{ $collection->farmer->village->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-[#005BAC]">
                                        {{ number_format($collection->milk_quantity, 2) }} L
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-800">
                                        {{ $collection->fat ? number_format($collection->fat, 2) . '%' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-800">
                                        {{ $collection->snf ? number_format($collection->snf, 2) . '%' : '-' }}
                                    </td>
                                    @can('view-financials')
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-700">
                                            ₹{{ number_format($collection->rate, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-emerald-600">
                                            ₹{{ number_format($collection->amount, 2) }}
                                        </td>
                                    @endcan
                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-3">
                                        <a href="{{ route('milk-collections.show', $collection) }}" class="text-[#005BAC] hover:text-[#003B73] font-bold">{{ __('View') }}</a>
                                        @can('update', $collection)
                                            <a href="{{ route('milk-collections.edit', $collection) }}" class="text-amber-600 hover:text-amber-800 font-bold">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $collection)
                                            <button 
                                                type="button" 
                                                @click="deleteModalOpen = true; deleteActionUrl = '{{ route('milk-collections.destroy', $collection) }}'; recordDetail = '{{ $collection->farmer->name }} on {{ $collection->collection_date->format('d-m-Y') }} ({{ ucfirst($collection->shift) }})'" 
                                                class="text-rose-600 hover:text-rose-800 font-bold focus:outline-none"
                                            >
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
                    {{ $collections->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Delete Modal -->
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
                        <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-rose-50 text-rose-600 border border-rose-100">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                {{ __('Delete Collection Record') }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ __('Are you sure you want to delete collection record for') }} <strong x-text="recordDetail" class="text-slate-800"></strong>? {{ __('This action cannot be undone.') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-row-reverse gap-3">
                        <form :action="deleteActionUrl" method="POST" class="inline">
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
    </div>
</x-app-layout>
