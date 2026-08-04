<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Farmer Profile') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('farmers.edit', $farmer) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit Profile') }}
                </a>
                <a href="{{ route('farmers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Overview Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-100 pb-6 mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $farmer->name }}</h3>
                            <p class="text-sm text-indigo-600 font-semibold mt-1">{{ __('Farmer Code') }}: {{ $farmer->farmer_code }}</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex items-center gap-3">
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">{{ __('Status') }}:</span>
                            @if($farmer->status)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ __('Active') }}
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ __('Inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Personal Info -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide border-b border-gray-100 pb-2">{{ __('Personal Details') }}</h4>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Father Name') }}</span>
                                <span class="text-sm text-gray-800 font-medium">{{ $farmer->father_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Gender') }}</span>
                                <span class="text-sm text-gray-800 font-medium capitalize">{{ $farmer->gender ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Joining Date') }}</span>
                                <span class="text-sm text-gray-800 font-medium">{{ $farmer->joining_date ? $farmer->joining_date->format('M d, Y') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Contact & Address -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide border-b border-gray-100 pb-2">{{ __('Contact & Location') }}</h4>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Village') }}</span>
                                <span class="text-sm text-indigo-600 font-semibold">{{ $farmer->village->name }} ({{ $farmer->village->code }})</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Mobile') }}</span>
                                <span class="text-sm text-gray-800 font-medium">{{ $farmer->mobile }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Alternate Mobile') }}</span>
                                <span class="text-sm text-gray-800 font-medium">{{ $farmer->alternate_mobile ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Address') }}</span>
                                <span class="text-sm text-gray-700 whitespace-pre-line block mt-1 bg-gray-50 p-2.5 rounded border border-gray-100">{{ $farmer->address ?? '-' }}</span>
                            </div>
                        </div>

                        <!-- Bank Details -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide border-b border-gray-100 pb-2">{{ __('Bank / Payment Details') }}</h4>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Bank Name') }}</span>
                                <span class="text-sm text-gray-800 font-medium">{{ $farmer->bank_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('Account Number') }}</span>
                                <span class="text-sm text-gray-800 font-medium">{{ $farmer->account_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">{{ __('IFSC Code') }}</span>
                                <span class="text-sm text-gray-800 font-medium">{{ $farmer->ifsc_code ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Future Sections (Placeholders) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Milk Collection History Placeholder -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-indigo-50/50">
                    <div class="p-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                {{ __('Milk Collection History') }}
                            </h3>
                            <span class="px-2 py-0.5 text-xs font-semibold text-indigo-600 bg-indigo-50 rounded-full uppercase tracking-wider">
                                {{ __('Phase 3') }}
                            </span>
                        </div>
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm font-semibold text-gray-700">{{ __('No Milk Records Available') }}</p>
                            <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">{{ __('Milk collection entries, fat/SNF levels, and daily measurements will appear here after the collection module is completed.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment/Ledger History Placeholder -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-indigo-50/50">
                    <div class="p-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                {{ __('Payment / Ledger History') }}
                            </h3>
                            <span class="px-2 py-0.5 text-xs font-semibold text-indigo-600 bg-indigo-50 rounded-full uppercase tracking-wider">
                                {{ __('Phase 4') }}
                            </span>
                        </div>
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-sm font-semibold text-gray-700">{{ __('No Payment Transactions') }}</p>
                            <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">{{ __('Farmer payment slips, bank transactions, and ledger status statements will display here once the payments module is completed.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
