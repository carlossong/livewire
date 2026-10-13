<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
    {{-- Cabeçalho da Página --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
                Gerenciamento de Usuários
            </h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                Gerencie contas de usuários, credenciais e perfis de acesso (RBAC).
            </p>
        </div>

        @can('create', App\Models\User::class)
            <div>
                <a href="{{ route('users.create') }}"
                   wire:navigate
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-colors bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Usuário
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

    {{-- Barra de Ferramentas / Busca --}}
    <div class="p-4 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-sm">
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-neutral-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Buscar por nome ou e-mail..."
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

    {{-- Tabela de Usuários --}}
    <div class="overflow-hidden bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800 text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800/60 text-neutral-700 dark:text-neutral-300">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 font-semibold">Nome</th>
                        <th scope="col" class="px-6 py-3.5 font-semibold">E-mail</th>
                        <th scope="col" class="px-6 py-3.5 font-semibold">Perfis (Roles)</th>
                        <th scope="col" class="px-6 py-3.5 font-semibold">Data de Criação</th>
                        <th scope="col" class="px-6 py-3.5 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 text-neutral-800 dark:text-neutral-200">
                    @forelse ($users as $user)
                        <tr wire:key="user-row-{{ $user->id }}" class="hover:bg-neutral-50/70 dark:hover:bg-neutral-800/40 transition-colors">
                            {{-- Nome --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-semibold text-xs uppercase">
                                        {{ method_exists($user, 'initials') ? $user->initials() : strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-neutral-900 dark:text-neutral-100 flex items-center gap-2">
                                            {{ $user->name }}
                                            @if ($user->id === auth()->id())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                                    Você
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- E-mail --}}
                            <td class="px-6 py-4 whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                                {{ $user->email }}
                            </td>

                            {{-- Roles --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $role->name === 'Admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800' }}">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                                            Sem perfil
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Data de Criação --}}
                            <td class="px-6 py-4 whitespace-nowrap text-neutral-500 dark:text-neutral-400">
                                {{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>

                            {{-- Ações --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right font-medium space-x-2">
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user->id) }}"
                                       wire:navigate
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:text-indigo-600 dark:hover:text-indigo-400 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 rounded-md transition-colors"
                                       title="Editar Usuário">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Editar
                                    </a>
                                @endcan

                                @can('delete', $user)
                                    <button type="button"
                                            wire:click="delete({{ $user->id }})"
                                            wire:confirm="Tem certeza de que deseja excluir o usuário '{{ $user->name }}'? Esta ação não pode ser desfeita."
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-rose-700 dark:text-rose-400 hover:text-white bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-600 dark:hover:bg-rose-600 rounded-md transition-colors disabled:opacity-50"
                                            title="Excluir Usuário">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Excluir
                                    </button>
                                @else
                                    @if ($user->id === auth()->id())
                                        <span class="inline-flex items-center px-2 py-1 text-xs text-neutral-400 dark:text-neutral-500 italic">
                                            (Auto-exclusão bloqueada)
                                        </span>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 mb-3 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="font-medium text-base text-neutral-700 dark:text-neutral-300">Nenhum usuário encontrado</p>
                                    <p class="text-sm text-neutral-400 mt-1">
                                        {{ $search ? 'Tente ajustar os termos da sua busca.' : 'Comece cadastrando um novo usuário no sistema.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/30">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
