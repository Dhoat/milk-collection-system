<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Milk Intake & Receivings') }}" description="{{ __('Verify village milk collection batches received at the Main Processing Center.') }}">
            <x-slot name="actions">
                @can('create', App\Models\MilkReceiving::class)
                    <a href="{{ route('milk-receivings.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005BAC] hover:bg-[#003B73] active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Record Intake') }}
                    </a>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div x-data="{ deleteModalOpen: false, deleteActionUrl: '', recordTitle: '' }">
        <!-- Filter Card -->
        <x-admin.card class="mb-6">
            <form method="GET" action="{{ route('milk-receivings.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-5 items-end">
                <!-- Date Filter -->
                <div>
                    <label for="date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Date') }}</label>
                    <input id="date" name="date" type="date" value="{{ request('date') }}" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all" />
                </div>

                <!-- Village Filter -->
                <div>
                    <label for="village_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Village Center') }}</label>
                    <select id="village_id" name="village_id" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
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
                    <label for="shift" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Shift') }}</label>
                    <select id="shift" name="shift" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Shifts') }}</option>
                        <option value="morning" {{ request('shift') === 'morning' ? 'selected' : '' }}>{{ __('Morning') }}</option>
                        <option value="evening" {{ request('shift') === 'evening' ? 'selected' : '' }}>{{ __('Evening') }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Status') }}</label>
                    <select id="status" name="status" class="w-full py-2.5 text-sm border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm transition-all">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>{{ __('Received') }}</option>
                        <option value="discrepancy" {{ request('status') === 'discrepancy' ? 'selected' : '' }}>{{ __('Discrepancy') }}</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <x-primary-button type="submit" class="w-full justify-center">
                        {{ __('Apply') }}
                    </x-primary-button>
                    @if(request()->anyFilled(['date', 'village_id', 'shift', 'status']))
                        <a href="{{ route('milk-receivings.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Receivings Table Card -->
        <x-admin.card>
            @if($receivings->isEmpty())
                <div class="text-center py-12 text-slate-400 font-semibold text-xs">
                    {{ __('No milk receiving records found.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-200/80">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="pl-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Date & Shift') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Village') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Expected Qty') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Received Qty') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Variance') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Verified By') }}</th>
                                <th scope="col" class="pr-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($receivings as $rec)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900">{{ $rec->receiving_date->format('d-m-Y') }}</span>
                                            @if($rec->shift === 'morning')
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-md bg-amber-50 text-amber-800 border border-amber-200">☀️ Morn</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-md bg-blue-50 text-blue-800 border border-blue-200">🌙 Eve</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-xs font-bold text-slate-900 block">{{ $rec->village->name }}</span>
                                        <span class="text-[11px] font-semibold text-slate-400 font-mono">{{ $rec->village->code }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-bold text-slate-700">
                                        {{ number_format($rec->expected_quantity, 2) }} L
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-extrabold text-[#005BAC]">
                                        {{ number_format($rec->received_quantity, 2) }} L
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-extrabold">
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
                                            <span class="px-2.5 py-1 inline-flex text-[10px] font-extrabold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                                ● Received
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-[10px] font-extrabold rounded-full bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider">
                                                ⚠ Discrepancy
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs font-semibold text-slate-700">
                                        {{ $rec->verifier->name }}
                                    </td>
                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-3">
                                        <a href="{{ route('milk-receivings.show', $rec) }}" class="text-[#005BAC] hover:text-[#003B73] font-bold">{{ __('View') }}</a>
                                        @can('update', $rec)
                                            <a href="{{ route('milk-receivings.edit', $rec) }}" class="text-amber-600 hover:text-amber-800 font-bold">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $rec)
                                            <button 
                                                type="button" 
                                                @click="deleteModalOpen = true; deleteActionUrl = '{{ route('milk-receivings.destroy', $rec) }}'; recordTitle = '{{ addslashes($rec->village->name) }} ({{ $rec->receiving_date->format('M d') }} - {{ ucfirst($rec->shift) }})'" 
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
                    {{ $receivings->links() }}
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
                        <div class="shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-rose-50 border border-rose-100 text-rose-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                {{ __('Delete Intake Record') }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ __('Are you sure you want to delete the receiving entry for') }} <strong class="text-slate-800" x-text="recordTitle"></strong>? {{ __('This action cannot be undone.') }}
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
</x-admin-layout>
