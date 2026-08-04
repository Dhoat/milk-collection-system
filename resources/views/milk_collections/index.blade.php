<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Milk Collections') }}
            </h2>
            <a href="{{ route('milk-collections.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Record Collection') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ deleteModalOpen: false, deleteActionUrl: '', recordDetail: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button class="text-green-500 hover:text-green-700" onclick="this.parentElement.remove();">&times;</button>
                </div>
            @endif

            <!-- Search and Filter Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('milk-collections.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <!-- Search term -->
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="search" :value="__('Search Farmer')" />
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <x-text-input id="search" name="search" type="text" class="block w-full pl-10" :value="$search" placeholder="Search by name or code..." />
                            </div>
                        </div>

                        <!-- Village Filter -->
                        <div>
                            <x-input-label for="village_id" :value="__('Village')" />
                            <select id="village_id" name="village_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
                            <x-input-label for="date" :value="__('Collection Date')" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="$date" />
                        </div>

                        <!-- Shift Filter -->
                        <div>
                            <x-input-label for="shift" :value="__('Shift')" />
                            <select id="shift" name="shift" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('All Shifts') }}</option>
                                <option value="morning" {{ $shift === 'morning' ? 'selected' : '' }}>{{ __('Morning') }}</option>
                                <option value="evening" {{ $shift === 'evening' ? 'selected' : '' }}>{{ __('Evening') }}</option>
                            </select>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-span-1 md:col-span-5 flex justify-end gap-2">
                            <x-primary-button type="submit">
                                {{ __('Filter') }}
                            </x-primary-button>
                            @if($search || $villageId || $farmerId || $date || $shift)
                                <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Collections Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($collections->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            {{ __('No collection records found.') }}
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Date') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Shift') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Farmer') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Village') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Milk Qty (L)') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Fat %') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('SNF %') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Rate (₹)') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Amount (₹)') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($collections as $collection)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-semibold">
                                                {{ $collection->collection_date->format('d-m-Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($collection->shift === 'morning')
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 capitalize">
                                                        {{ $collection->shift }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 capitalize">
                                                        {{ $collection->shift }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                {{ $collection->farmer->name }} <span class="text-xs text-gray-400">({{ $collection->farmer->farmer_code }})</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $collection->farmer->village->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium">
                                                {{ number_format($collection->milk_quantity, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $collection->fat ? number_format($collection->fat, 2) : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $collection->snf ? number_format($collection->snf, 2) : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                {{ number_format($collection->rate, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 font-bold">
                                                {{ number_format($collection->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <a href="{{ route('milk-collections.show', $collection) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150">{{ __('View') }}</a>
                                                <a href="{{ route('milk-collections.edit', $collection) }}" class="text-yellow-600 hover:text-yellow-900 transition duration-150">{{ __('Edit') }}</a>
                                                <button 
                                                    type="button" 
                                                    @click="deleteModalOpen = true; deleteActionUrl = '{{ route('milk-collections.destroy', $collection) }}'; recordDetail = '{{ $collection->farmer->name }} on {{ $collection->collection_date->format('d-m-Y') }} ({{ ucfirst($collection->shift) }})'" 
                                                    class="text-red-600 hover:text-red-900 transition duration-150 focus:outline-none"
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
                        <div class="mt-6">
                            {{ $collections->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Alpine.js Delete Confirmation Modal -->
        <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
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
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <!-- Modal Content -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="deleteModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ms-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">
                                {{ __('Delete Collection Record') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    {{ __('Are you sure you want to delete the milk collection entry for') }} <strong x-text="recordDetail"></strong>? {{ __('This action cannot be undone.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <form :action="deleteActionUrl" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ms-3 sm:w-auto sm:text-sm">
                                {{ __('Delete') }}
                            </button>
                        </form>
                        <button type="button" @click="deleteModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
