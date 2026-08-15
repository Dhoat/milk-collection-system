@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-sm bg-white text-slate-900 text-sm py-2.5 px-3.5 transition-all duration-150 placeholder:text-slate-400 disabled:bg-slate-50 disabled:text-slate-500']) }}>
