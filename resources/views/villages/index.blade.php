<x-app-layout>
    <x-slot name="header">
        <x-admin.page-header 
            title="{{ __('Villages Management') }}" 
            description="{{ __('Register and manage milk collection center villages and regional sectors') }}">
            <x-slot name="actions">
                @can('create', App\Models\Village::class)
                    <a href="{{ route('villages.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005BAC] hover:bg-[#003B73] active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Add Village') }}
                    </a>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ deleteModalOpen: false, deleteActionUrl: '', villageName: '' }">
        <!-- Search & Filter Card -->
        <x-admin.card class="mb-6">
            <form method="GET" action="{{ route('villages.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-grow w-full">
                    <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Search Villages') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input id="search" name="search" type="text" value="{{ $search }}" placeholder="Search by village name or sector code..." 
                               class="w-full pl-10 pr-4 py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                    </div>
                </div>
                <div class="flex gap-2.5 w-full sm:w-auto shrink-0">
                    <x-primary-button type="submit" class="w-full sm:w-auto">
                        {{ __('Filter') }}
                    </x-primary-button>
                    @if($search)
                        <a href="{{ route('villages.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all w-full sm:w-auto">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Villages Table Card -->
        <x-admin.card>
            @if($villages->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p class="text-sm font-semibold text-slate-600">{{ __('No villages found matching your criteria.') }}</p>
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-200/80">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Sector Code') }}</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Village Name') }}</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Address / Region') }}</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($villages as $village)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-extrabold rounded-lg bg-blue-50 text-[#005BAC] border border-blue-100 uppercase tracking-wide">
                                            {{ $village->code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                        {{ $village->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">
                                        {{ $village->address ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($village->status)
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ● {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                ○ {{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-3">
                                        <a href="{{ route('villages.show', $village) }}" class="text-[#005BAC] hover:text-[#003B73] font-bold">{{ __('View') }}</a>
                                        @can('update', $village)
                                            <a href="{{ route('villages.edit', $village) }}" class="text-amber-600 hover:text-amber-800 font-bold">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $village)
                                            <button 
                                                type="button" 
                                                @click="deleteModalOpen = true; deleteActionUrl = '{{ route('villages.destroy', $village) }}'; villageName = '{{ addslashes($village->name) }}'" 
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
                    {{ $villages->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Delete Confirmation Modal -->
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
                                {{ __('Delete Village') }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ __('Are you sure you want to delete village') }} <strong x-text="villageName" class="text-slate-800"></strong>? {{ __('This action cannot be undone.') }}
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
