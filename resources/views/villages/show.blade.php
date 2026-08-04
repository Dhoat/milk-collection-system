<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Village Details') }}
            </h2>
            <a href="{{ route('villages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $village->name }}</h3>
                        <p class="text-sm text-indigo-600 font-semibold mt-1">{{ __('Village Code') }}: {{ $village->code }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Status') }}</h4>
                            <p class="mt-1 text-sm">
                                @if($village->status)
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Created At') }}</h4>
                            <p class="mt-1 text-sm text-gray-800">{{ $village->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Address') }}</h4>
                            <p class="mt-1 text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-4 rounded-md border border-gray-100">
                                {{ $village->address ?? __('No address specified.') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 flex items-center gap-4">
                        <a href="{{ route('villages.edit', $village) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit Village') }}
                        </a>
                        <a href="{{ route('villages.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
