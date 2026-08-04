<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Milk Collection Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('milk-collections.edit', $milkCollection) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit Record') }}
                </a>
                <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="border-b border-gray-100 pb-6 mb-6 flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ __('Collection Summary') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Recorded for ') }} <strong>{{ $milkCollection->farmer->name }}</strong></p>
                        </div>
                        <div>
                            @if($milkCollection->shift === 'morning')
                                <span class="px-3 py-1.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 capitalize">
                                    {{ $milkCollection->shift }}
                                </span>
                            @else
                                <span class="px-3 py-1.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 capitalize">
                                    {{ $milkCollection->shift }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Farmer Name & Code') }}</span>
                                <span class="text-sm text-gray-800 font-semibold mt-0.5 block">
                                    {{ $milkCollection->farmer->name }} ({{ $milkCollection->farmer->farmer_code }})
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Village') }}</span>
                                <span class="text-sm text-gray-800 font-semibold mt-0.5 block">
                                    {{ $milkCollection->farmer->village->name }} ({{ $milkCollection->farmer->village->code }})
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Date') }}</span>
                                <span class="text-sm text-gray-800 font-semibold mt-0.5 block">
                                    {{ $milkCollection->collection_date->format('l, d F Y') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Shift') }}</span>
                                <span class="text-sm text-gray-800 font-semibold mt-0.5 block capitalize">
                                    {{ $milkCollection->shift }}
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-b border-gray-100 py-6 my-6 grid grid-cols-1 sm:grid-cols-3 gap-6 bg-gray-50 p-4 rounded-md">
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Milk Quantity') }}</span>
                                <span class="text-lg font-bold text-gray-800 block mt-0.5">
                                    {{ number_format($milkCollection->milk_quantity, 2) }} Liters
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Rate per Liter') }}</span>
                                <span class="text-lg font-bold text-gray-800 block mt-0.5">
                                    ₹ {{ number_format($milkCollection->rate, 2) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Total Amount') }}</span>
                                <span class="text-xl font-extrabold text-indigo-600 block mt-0.5">
                                    ₹ {{ number_format($milkCollection->amount, 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Fat %') }}</span>
                                <span class="text-sm text-gray-800 font-medium mt-0.5 block">
                                    {{ $milkCollection->fat ? number_format($milkCollection->fat, 2) . '%' : '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('SNF %') }}</span>
                                <span class="text-sm text-gray-800 font-medium mt-0.5 block">
                                    {{ $milkCollection->snf ? number_format($milkCollection->snf, 2) . '%' : '-' }}
                                </span>
                            </div>
                            <div class="md:col-span-2">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Notes / Remarks') }}</span>
                                <p class="text-sm text-gray-700 mt-1 bg-white p-3 rounded border border-gray-100 min-h-[60px] whitespace-pre-line">
                                    {{ $milkCollection->notes ?? __('No notes recorded.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex gap-4">
                        <a href="{{ route('milk-collections.edit', $milkCollection) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit Details') }}
                        </a>
                        <a href="{{ route('milk-collections.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
