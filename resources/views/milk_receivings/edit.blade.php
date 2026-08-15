<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('Edit Milk Receiving Record') }}" description="{{ __('Update center intake measurements and quality test results for a received village batch.') }}">
            <x-slot name="actions">
                <a href="{{ route('milk-receivings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition-all">
                    ← {{ __('Back to List') }}
                </a>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Main Form Container Card (Full Screen / Full Width) -->
        <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs p-6 lg:p-8 space-y-8">
            <form method="POST" action="{{ route('milk-receivings.update', $milkReceiving) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Session & Source -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-[#005BAC] flex items-center justify-center font-bold shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Intake Session & Source') }}</h3>
                            <p class="text-xs text-slate-400 font-medium">{{ __('Specify dispatch date, shift, and source village collection center') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Village Source -->
                        <div>
                            <label for="village_id" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('VILLAGE SOURCE') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="village_id" name="village_id" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                @foreach($villages as $village)
                                    <option value="{{ $village->id }}" {{ old('village_id', $milkReceiving->village_id) == $village->id ? 'selected' : '' }}>
                                        {{ $village->name }} ({{ $village->code }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('village_id')" class="mt-1.5" />
                        </div>

                        <!-- Receiving Date -->
                        <div>
                            <label for="receiving_date" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('RECEIVING DATE') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="receiving_date" name="receiving_date" type="date" value="{{ old('receiving_date', $milkReceiving->receiving_date->format('Y-m-d')) }}" required
                                   class="w-full py-2.5 px-3.5 text-xs font-mono border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs transition-all" />
                            <x-input-error :messages="$errors->get('receiving_date')" class="mt-1.5" />
                        </div>

                        <!-- Shift -->
                        <div>
                            <label for="shift" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('SHIFT') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="shift" name="shift" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" required>
                                <option value="morning" {{ old('shift', $milkReceiving->shift) === 'morning' ? 'selected' : '' }}>☀️ Morning Shift</option>
                                <option value="evening" {{ old('shift', $milkReceiving->shift) === 'evening' ? 'selected' : '' }}>🌙 Evening Shift</option>
                            </select>
                            <x-input-error :messages="$errors->get('shift')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Received Batch Measurements -->
                <div x-data="{ open: true }" class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-6 shadow-2xs">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#005BAC] text-white flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 01-1.023-.547M19.428 15.428A2 2 0 0121 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 011.572-1.952m14.856 0A6 6 0 0017 12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v7a6 6 0 00-2.428 3.428M12 3v9"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ __('Received Batch Measurements') }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ __('Quantity measured at central plant and tested FAT/SNF quality indicators') }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-[#005BAC] hover:text-[#003B73]">
                            <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="space-y-6 pt-2">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Received Quantity -->
                            <div>
                                <label for="received_quantity" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('RECEIVED QUANTITY (LITERS)') }} <span class="text-rose-500">*</span>
                                </label>
                                <input id="received_quantity" name="received_quantity" type="number" step="0.01" min="0" value="{{ old('received_quantity', $milkReceiving->received_quantity) }}" required
                                       class="w-full py-2.5 px-3.5 text-base font-extrabold border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-900" />
                                <x-input-error :messages="$errors->get('received_quantity')" class="mt-1.5" />
                            </div>

                            <!-- Received Fat -->
                            <div>
                                <label for="received_fat" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('TESTED FAT (%)') }}
                                </label>
                                <input id="received_fat" name="received_fat" type="number" step="0.01" min="0" max="100" value="{{ old('received_fat', $milkReceiving->received_fat) }}" placeholder="e.g. 4.50"
                                       class="w-full py-2.5 px-3.5 text-xs font-mono border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white" />
                                <x-input-error :messages="$errors->get('received_fat')" class="mt-1.5" />
                            </div>

                            <!-- Received SNF -->
                            <div>
                                <label for="received_snf" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    {{ __('TESTED SNF (%)') }}
                                </label>
                                <input id="received_snf" name="received_snf" type="number" step="0.01" min="0" max="100" value="{{ old('received_snf', $milkReceiving->received_snf) }}" placeholder="e.g. 8.50"
                                       class="w-full py-2.5 px-3.5 text-xs font-mono border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white" />
                                <x-input-error :messages="$errors->get('received_snf')" class="mt-1.5" />
                            </div>
                        </div>

                        <!-- Verification Notes -->
                        <div>
                            <label for="notes" class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('VERIFICATION NOTES / DISCREPANCY REMARKS') }}
                            </label>
                            <textarea id="notes" name="notes" rows="3" class="w-full py-2.5 px-3.5 text-xs font-medium border border-slate-200 focus:border-[#005BAC] focus:ring-2 focus:ring-[#005BAC]/20 rounded-xl shadow-2xs bg-white text-slate-800" placeholder="Optional notes regarding quality, tanker condition, or volume discrepancy...">{{ old('notes', $milkReceiving->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('Update Intake Record') }}
                    </button>
                    <a href="{{ route('milk-receivings.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ✕ {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
