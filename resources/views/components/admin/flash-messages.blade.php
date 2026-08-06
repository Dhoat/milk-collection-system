@if (session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-800 shadow-sm flex items-center justify-between" role="alert">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
        <button class="p-1 text-emerald-500 hover:text-emerald-700 rounded-lg hover:bg-emerald-100/50" @click="show = false">&times;</button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 shadow-sm flex items-center justify-between" role="alert">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-100 text-rose-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span class="text-xs font-semibold">{{ session('error') }}</span>
        </div>
        <button class="p-1 text-rose-500 hover:text-rose-700 rounded-lg hover:bg-rose-100/50" @click="show = false">&times;</button>
    </div>
@endif
