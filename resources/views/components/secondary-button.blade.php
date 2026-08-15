<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 border border-slate-200 rounded-xl font-bold text-xs text-slate-700 uppercase tracking-wider shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all duration-150 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
