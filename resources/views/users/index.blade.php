<x-admin-layout>
    <x-slot name="header">
        <x-admin.page-header title="{{ __('User Management') }}" description="{{ __('Manage system accounts, staff credentials, and role access permissions.') }}">
            <x-slot name="actions">
                @can('create', App\Models\User::class)
                    <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        {{ __('Add New User') }}
                    </a>
                @endcan
            </x-slot>
        </x-admin.page-header>
    </x-slot>

    <div class="space-y-6" x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }">
        <!-- Search & Filters Card -->
        <x-admin.card title="{{ __('Search & Filter Accounts') }}">
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="search" :value="__('Search User')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1 block w-full text-xs" placeholder="Search by name or email..." :value="request('search')" />
                </div>

                <div>
                    <x-input-label for="role" :value="__('System Role')" />
                    <select id="role" name="role" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Roles') }}</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>{{ __('Manager') }}</option>
                        <option value="collection_staff" {{ request('role') == 'collection_staff' ? 'selected' : '' }}>{{ __('Collection Staff') }}</option>
                        <option value="center_staff" {{ request('role') == 'center_staff' ? 'selected' : '' }}>{{ __('Center Staff') }}</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="status" :value="__('Account Status')" />
                    <select id="status" name="status" class="mt-1 block w-full border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-xs">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition shadow-sm">
                        {{ __('Filter') }}
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('users.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Users Directory Table Card -->
        <x-admin.card title="{{ __('User Directory') }}">
            @if($users->isEmpty())
                <div class="text-center py-10 text-slate-400 text-xs">
                    {{ __('No user accounts found matching your query criteria.') }}
                </div>
            @else
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th scope="col" class="pl-6 py-3.5 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('User Account') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('System Role') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Status') }}</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xxs font-bold text-slate-450 uppercase">{{ __('Created Date') }}</th>
                                <th scope="col" class="pr-6 py-3.5 text-right text-xxs font-bold text-slate-450 uppercase">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="pl-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-600 text-xs">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                                    {{ $user->name }}
                                                    @if($user->id === auth()->id())
                                                        <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded text-3xs font-semibold">{{ __('You') }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-xxs text-slate-500 font-mono">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($user->isSuperAdmin())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                                {{ __('Super Admin') }}
                                            </span>
                                        @elseif($user->isManager())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ __('Manager') }}
                                            </span>
                                        @elseif($user->isCenterStaff())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ __('Center Staff') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ __('Collection Staff') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @can('update', $user)
                                            <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-bold transition hover:opacity-80 {{ $user->status ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->status ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                                    {{ $user->status ? __('Active') : __('Inactive') }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs font-bold {{ $user->status ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                                {{ $user->status ? __('Active') : __('Inactive') }}
                                            </span>
                                        @endcan
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-500 font-mono">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>

                                    <td class="pr-6 py-4 whitespace-nowrap text-right text-xs space-x-2">
                                        @can('view', $user)
                                            <a href="{{ route('users.show', $user) }}" class="inline-flex items-center p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition" title="{{ __('View Details') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                        @endcan

                                        @can('update', $user)
                                            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition" title="{{ __('Edit Account') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                        @endcan

                                        @can('delete', $user)
                                            <button type="button" @click="deleteModalOpen = true; deleteUrl = '{{ route('users.destroy', $user) }}'; deleteName = '{{ $user->name }}'" class="inline-flex items-center p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="{{ __('Delete Account') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </x-admin.card>

        <!-- Delete Confirmation Modal -->
        <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/40" @click="deleteModalOpen = false"></div>
                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <h3 class="text-base font-bold text-slate-900">{{ __('Confirm Delete Account') }}</h3>
                    <p class="mt-2 text-xs text-slate-500">
                        {{ __('Are you sure you want to delete user account') }} <strong class="text-slate-800" x-text="deleteName"></strong>? {{ __('This action cannot be undone.') }}
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                            {{ __('Cancel') }}
                        </button>
                        <form :action="deleteUrl" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl transition shadow-sm">
                                {{ __('Delete User') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
