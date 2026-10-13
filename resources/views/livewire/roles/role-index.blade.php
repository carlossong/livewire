<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    {{-- Cabeçalho da Página --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
                Perfis de Acesso (RBAC)
            </h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                Gerencie os papéis do sistema e as permissões de acesso concedidas aos usuários.
            </p>
        </div>

        @can('create', Spatie\Permission\Models\Role::class)
            <div>
                <a href="{{ route('roles.create') }}"
                   wire:navigate
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-colors bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Perfil
                </a>
            </div>
        @endcan
    </div>

    {{-- Feedback de Mensagens (Flash Messages) --}}
    @if (session()->has('success'))
        <div class="flex items-center p-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800" role="alert">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="flex items-center p-4 text-sm text-rose-800 rounded-lg bg-rose-50 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800" role="alert">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Barra de Busca --}}
    <div class="p-4 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-sm">
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-neutral-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Buscar perfil pelo nome..."
                   class="w-full pl-10 pr-10 py-2 text-sm bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            @if ($search)
                <button wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Tabela de Perfis --}}
    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50/75 dark:bg-neutral-800/50 text-neutral-600 dark:text-neutral-300 text-xs uppercase font-semibold">
                        <th class="px-6 py-3.5">Perfil</th>
                        <th class="px-6 py-3.5">Usuários Vinculados</th>
                        <th class="px-6 py-3.5">Permissões</th>
                        <th class="px-6 py-3.5 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    @forelse ($roles as $role)
                        <tr wire:key="role-{{ $role->id }}" class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/50 transition-colors">
                            {{-- Nome do Perfil --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $role->name }}
                                    </span>
                                    @if (strtolower($role->name) === 'admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                            Sistema
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Usuários Vinculados --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $role->users_count > 0 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ $role->users_count }} {{ $role->users_count === 1 ? 'usuário' : 'usuários' }}
                                </span>
                            </td>

                            {{-- Permissões --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-1.5 max-w-md">
                                    @if ($role->permissions_count === 0)
                                        <span class="text-xs text-neutral-400">Nenhuma permissão atribuída</span>
                                    @else
                                        @foreach ($role->permissions as $permission)
                                            <span wire:key="perm-{{ $role->id }}-{{ $permission->id }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if ($role->permissions_count > count($role->permissions))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-neutral-200 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200">
                                                +{{ $role->permissions_count - count($role->permissions) }} mais
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            {{-- Ações --}}
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    @can('update', $role)
                                        <a href="{{ route('roles.edit', $role->id) }}"
                                           wire:navigate
                                           class="p-1.5 text-neutral-500 hover:text-indigo-600 dark:text-neutral-400 dark:hover:text-indigo-400 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                                           title="Editar Perfil">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    @endcan

                                    @if (strtolower($role->name) !== 'admin' && $role->users_count === 0)
                                        @can('delete', $role)
                                            <button type="button"
                                                    wire:click="confirmDelete({{ $role->id }})"
                                                    class="p-1.5 text-neutral-500 hover:text-rose-600 dark:text-neutral-400 dark:hover:text-rose-400 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                                                    title="Excluir Perfil">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endcan
                                    @elseif (strtolower($role->name) === 'admin')
                                        <span class="p-1.5 text-neutral-300 dark:text-neutral-600 cursor-not-allowed" title="Perfil protegido do sistema">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="p-1.5 text-neutral-300 dark:text-neutral-600 cursor-not-allowed" title="Não pode excluir: possui usuários vinculados">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-neutral-300 dark:text-neutral-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <p class="font-medium text-base text-neutral-700 dark:text-neutral-300">Nenhum perfil de acesso encontrado</p>
                                    <p class="text-sm text-neutral-400 mt-1">
                                        {{ $search ? 'Tente ajustar os termos da sua busca.' : 'Comece cadastrando um novo perfil de acesso.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if ($roles->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/30">
                {{ $roles->links() }}
            </div>
        @endif
    </div>

    {{-- Modal de Confirmação de Exclusão com Flux UI (Obrigatório) --}}
    <flux:modal
        name="delete-role-modal"
        class="max-w-md md:min-w-md"
        @close="closeDeleteModal"
        wire:model="showDeleteModal"
    >
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">Excluir Perfil de Acesso</flux:heading>
                <flux:subheading>
                    Tem certeza de que deseja excluir o perfil <strong class="text-neutral-900 dark:text-neutral-100 font-semibold">"{{ $roleToDeleteName }}"</strong>? Esta ação é irreversível.
                </flux:subheading>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button
                    variant="outline"
                    wire:click="closeDeleteModal"
                >
                    Cancelar
                </flux:button>

                <flux:button
                    variant="danger"
                    wire:click="deleteRole"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="deleteRole">Excluir Perfil</span>
                    <span wire:loading wire:target="deleteRole">Excluindo...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
