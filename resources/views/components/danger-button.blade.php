<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wider shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-rose-500/30 transition-all duration-150 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
