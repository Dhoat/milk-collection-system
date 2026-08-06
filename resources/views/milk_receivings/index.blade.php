<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Milk Receivings') }}" description="{{ __('Manage and verify milk batches received at the Main Milk Center from villages.') }}">
            <x-slot name="actions">
                <a href="{{ route('milk-receivings.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-250 transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Record Intake') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ deleteModalOpen: false, deleteActionUrl: '', recordTitle: '' }">
        <!-- Filter Form Card -->
        <div class="mb-6 bg-white border border-slate-200/85 rounded-2xl shadow-sm p-5">
            <form method="GET" action="{{ route('milk-receivings.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-5 items-end">
                <!-- Date Filter -->
                <div>
                    <x-input-label for="date" :value="__('Filter by Date')" class="text-xs font-semibold text-slate-500 mb-1" />
                    <x-text-input id="date" name="date" type="date" class="block w-full py-1.5 text-xs rounded-xl border-slate-200" :value="request('date')" />
                </div>

                <!-- Village Filter -->
                <div>
                    <x-input-label for="village_id" :value="__('Filter by Village')" class="text-xs font-semibold text-slate-500 mb-1" />
                    <select id="village_id" name="village_id" class="block w-full py-1.5 px-3 text-xs bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                        <option value="">{{ __('All Villages') }}</option>
                        @foreach($villages as $village)
                            <option value="{{ $village->id }}" {{ request('village_id') == $village->id ? 'selected' : '' }}>
                                {{ $village->name }} ({{ $village->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Shift Filter -->
                <div>
                    <x-input-label for="shift" :value="__('Filter by Shift')" class="text-xs font-semibold text-slate-500 mb-1" />
                    <select id="shift" name="shift" class="block w-full py-1.5 px-3 text-xs bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                        <option value="">{{ __('All Shifts') }}</option>
                        <option value="morning" {{ request('shift') === 'morning' ? 'selected' : '' }}>{{ __('Morning') }}</option>
                        <option value="evening" {{ request('shift') === 'evening' ? 'selected' : '' }}>{{ __('Evening') }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <x-input-label for="status" :value="__('Filter by Status')" class="text-xs font-semibold text-slate-500 mb-1" />
                    <select id="status" name="status" class="block w-full py-1.5 px-3 text-xs bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>{{ __('Received') }}</option>
                        <option value="discrepancy" {{ request('status') === 'discrepancy' ? 'selected' : '' }}>{{ __('Discrepancy') }}</option>
                    </select>
                </div>

                <!-- Submit Action Buttons -->
                <div class="flex gap-2">
                    <x-primary-button type="submit" class="w-full justify-center py-2 text-xxs tracking-wider font-bold rounded-xl">
                        {{ __('Apply') }}
                    </x-primary-button>
                    @if(request()->anyFilled(['date', 'village_id', 'shift', 'status']))
                        <a href="{{ route('milk-receivings.index') }}" class="w-full text-center inline-flex items-center justify-center px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-250 rounded-xl font-semibold text-xxs text-slate-700 uppercase tracking-widest transition duration-150">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Receivings Table Card -->
        <x-admin.card>
            @if($receivings->isEmpty())
                <div class="text-center py-12 text-slate-450 text-xs">
                    {{ __('No milk receiving records found.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th scope="col" class="pl-6 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Village') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Shift') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Expected Qty') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Received Qty') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Variance') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Verified By') }}</th>
                                <th scope="col" class="pr-6 py-3 text-right text-xxs font-bold text-slate-450 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($receivings as $rec)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-700">
                                        {{ $rec->receiving_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-xs font-semibold text-slate-800">{{ $rec->village->name }}</div>
                                        <div class="text-3xs text-slate-400 font-medium">{{ $rec->village->code }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-650 capitalize">
                                        {{ $rec->shift }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-medium text-slate-550">
                                        {{ number_format($rec->expected_quantity, 2) }} L
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-semibold text-slate-800">
                                        {{ number_format($rec->received_quantity, 2) }} L
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-bold">
                                        @php
                                            $variance = $rec->quantity_variance;
                                            $variancePercent = $rec->quantity_variance_percent;
                                        @endphp
                                        @if($variance > 0.1)
                                            <span class="text-emerald-600">+{{ number_format($variance, 2) }} L (+{{ number_format($variancePercent, 1) }}%)</span>
                                        @elseif($variance < -0.1)
                                            <span class="text-rose-600">{{ number_format($variance, 2) }} L ({{ number_format($variancePercent, 1) }}%)</span>
                                        @else
                                            <span class="text-slate-400">0.00 L</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs">
                                        @if($rec->status === 'received')
                                            <span class="px-2 py-0.5 inline-flex text-3xs leading-5 font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">
                                                {{ __('Received') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 inline-flex text-3xs leading-5 font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-100 uppercase tracking-wide">
                                                {{ __('Discrepancy') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-550">
                                        {{ $rec->verifier->name }}
                                    </td>
                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-2.5">
                                        <a href="{{ route('milk-receivings.show', $rec) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150">{{ __('View') }}</a>
                                        <a href="{{ route('milk-receivings.edit', $rec) }}" class="text-yellow-600 hover:text-yellow-900 transition duration-150">{{ __('Edit') }}</a>
                                        <button 
                                            type="button" 
                                            @click="deleteModalOpen = true; deleteActionUrl = '{{ route('milk-receivings.destroy', $rec) }}'; recordTitle = '{{ addslashes($rec->village->name) }} ({{ $rec->receiving_date->format('M d') }} - {{ ucfirst($rec->shift) }})'" 
                                            class="text-rose-600 hover:text-rose-900 transition duration-150 focus:outline-none"
                                        >
                                            {{ __('Delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="mt-6 border-t border-slate-100 pt-4">
                    {{ $receivings->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Alpine.js Delete Confirmation Modal -->
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
                    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="deleteModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-slate-200/80">
                    
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ms-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-semibold text-slate-800" id="modal-title">
                                {{ __('Delete Intake Record') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-xs text-slate-500">
                                    {{ __('Are you sure you want to delete the receiving entry for') }} <strong class="text-slate-700 font-bold" x-text="recordTitle"></strong>? {{ __('This action cannot be undone and will remove the record from verification history.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
                        <form :action="deleteActionUrl" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-rose-600 text-xs font-semibold text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 sm:w-auto transition-colors">
                                {{ __('Delete Record') }}
                            </button>
                        </form>
                        <button type="button" @click="deleteModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto transition-colors">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
