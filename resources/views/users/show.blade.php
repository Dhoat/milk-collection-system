<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('User Account Details') }}" description="{{ __('Comprehensive view of user account attributes and authorization settings.') }}">
            <x-slot name="actions">
                <div class="flex gap-2">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all">
                        ← {{ __('Back to Directory') }}
                    </a>
                    @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005BAC] hover:bg-[#003B73] text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            {{ __('Edit User') }}
                        </a>
                    @endcan
                </div>
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main User Info -->
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card title="{{ __('Account Profile') }}">
                <div class="flex items-start gap-4 pb-6 border-b border-slate-100">
                    <div class="w-16 h-16 rounded-2xl bg-[#005BAC]/10 border border-[#005BAC]/20 text-[#005BAC] flex items-center justify-center text-2xl font-black">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="px-2 py-0.5 bg-blue-50 text-[#005BAC] border border-blue-200 rounded-md text-[10px] font-extrabold uppercase tracking-wider">{{ __('Logged-in Account') }}</span>
                            @endif
                        </h2>
                        <p class="text-xs text-slate-500 font-mono mt-0.5 font-semibold">{{ $user->email }}</p>
                        <div class="mt-3 flex items-center gap-2">
                            @if($user->isSuperAdmin())
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ __('Super Admin') }}
                                </span>
                            @elseif($user->isManager())
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ __('Manager') }}
                                </span>
                            @elseif($user->isCenterStaff())
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ __('Center Staff') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-[#005BAC] border border-blue-200">
                                    {{ __('Collection Staff') }}
                                </span>
                            @endif

                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold {{ $user->status ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->status ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ $user->status ? __('Active Account') : __('Disabled Account') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">{{ __('User ID') }}</span>
                        <span class="text-xs font-mono font-bold text-slate-900 mt-1 block">#{{ $user->id }}</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Member Since') }}</span>
                        <span class="text-xs font-semibold text-slate-900 mt-1 block">{{ $user->created_at->format('F d, Y \a\t h:i A') }}</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Last Updated') }}</span>
                        <span class="text-xs font-semibold text-slate-900 mt-1 block">{{ $user->updated_at->format('F d, Y \a\t h:i A') }}</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">{{ __('Email Verification') }}</span>
                        <span class="text-xs font-semibold text-slate-900 mt-1 block">
                            @if($user->email_verified_at)
                                <span class="text-emerald-600 font-extrabold">✓ {{ __('Verified') }}</span> ({{ $user->email_verified_at->format('M d, Y') }})
                            @else
                                <span class="text-slate-400 font-medium">{{ __('Unverified') }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <!-- Role Permissions Overview -->
        <div>
            <x-admin.card title="{{ __('Role Capability Summary') }}">
                <div class="space-y-3 text-xs">
                    @if($user->isSuperAdmin())
                        <div class="p-4 bg-purple-50/70 border border-purple-100 rounded-2xl text-purple-900 space-y-1">
                            <h4 class="font-bold text-xs uppercase tracking-wider">{{ __('Super Administrator Access') }}</h4>
                            <p class="text-xs text-purple-700 leading-relaxed font-medium">
                                {{ __('Unrestricted full system control: manage users, configure system settings, view all financial reports, oversee collections, center receivings, stock, and orders.') }}
                            </p>
                        </div>
                    @elseif($user->isManager())
                        <div class="p-4 bg-indigo-50/70 border border-indigo-100 rounded-2xl text-indigo-900 space-y-1">
                            <h4 class="font-bold text-xs uppercase tracking-wider">{{ __('Managerial Access') }}</h4>
                            <p class="text-xs text-indigo-700 leading-relaxed font-medium">
                                {{ __('Operational supervisor access: view daily and monthly reports, manage village centers, farmers, milk receiving reconciliations, retail stock, and customer orders.') }}
                            </p>
                        </div>
                    @elseif($user->isCenterStaff())
                        <div class="p-4 bg-emerald-50/70 border border-emerald-100 rounded-2xl text-emerald-900 space-y-1">
                            <h4 class="font-bold text-xs uppercase tracking-wider">{{ __('Center Staff Access') }}</h4>
                            <p class="text-xs text-emerald-700 leading-relaxed font-medium">
                                {{ __('Main milk center operations: receive milk shipments from villages, record stock transactions, create shop orders, and manage vehicle dispatches.') }}
                            </p>
                        </div>
                    @else
                        <div class="p-4 bg-blue-50/70 border border-blue-100 rounded-2xl text-blue-900 space-y-1">
                            <h4 class="font-bold text-xs uppercase tracking-wider">{{ __('Collection Staff Access') }}</h4>
                            <p class="text-xs text-blue-700 leading-relaxed font-medium">
                                {{ __('Field operations: register village farmers and log daily morning/evening milk collection entries.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
