<div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
    {{-- Cabeçalho do Formulário --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400 mb-1">
                <a href="{{ route('roles.index') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    Perfis de Acesso
                </a>
                <span>/</span>
                <span class="text-neutral-900 dark:text-neutral-100 font-medium">
                    {{ $roleId ? 'Editar Perfil' : 'Novo Perfil' }}
                </span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
                {{ $roleId ? "Editar Perfil: {$name}" : 'Criar Novo Perfil' }}
            </h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                Configure o nome do perfil e as permissões de acesso concedidas a ele.
            </p>
        </div>

        <div>
            <a href="{{ route('roles.index') }}"
               wire:navigate
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-200 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar à Lista
            </a>
        </div>
    </div>

    {{-- Formulário --}}
    <form wire:submit="save" class="space-y-6">
        {{-- Card: Informações Gerais do Perfil --}}
        <div class="p-6 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-sm space-y-4">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-100 border-b border-neutral-200 dark:border-neutral-800 pb-3">
                Identificação do Perfil
            </h2>

            <div class="max-w-md space-y-2">
                <flux:input
                    wire:model="name"
                    label="Nome do Perfil"
                    placeholder="Ex: Gerente Comercial, Analista Fiscal..."
                    required
                    :disabled="$roleId && strtolower($name) === 'admin'"
                />
                @if ($roleId && strtolower($name) === 'admin')
                    <p class="text-xs text-amber-600 dark:text-amber-400">
                        O perfil Administrador do sistema não pode ter seu nome alterado.
                    </p>
                @endif
            </div>
        </div>

        {{-- Card: Permissões de Acesso por Módulo --}}
        <div class="p-6 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-neutral-200 dark:border-neutral-800 pb-4">
                <div>
                    <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                        Permissões do Perfil
                    </h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                        Selecione quais recursos e ações os usuários com este perfil terão acesso.
                    </p>
                </div>

                {{-- Ações Rápidas de Seleção --}}
                <div class="flex items-center gap-2">
                    <button type="button"
                            wire:click="selectAll"
                            class="px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:text-indigo-300 dark:hover:bg-indigo-900/60 rounded-md transition-colors">
                        Selecionar Todas
                    </button>
                    <button type="button"
                            wire:click="deselectAll"
                            class="px-3 py-1.5 text-xs font-medium text-neutral-600 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700 rounded-md transition-colors">
                        Desmarcar Todas
                    </button>
                </div>
            </div>

            {{-- Grid de Módulos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($this->groupedPermissions as $moduleName => $permissions)
                    @php
                        $permissionNames = collect($permissions)->pluck('name')->toArray();
                        $moduleSelectedCount = count(array_intersect($permissionNames, $selectedPermissions));
                        $isFullySelected = $moduleSelectedCount === count($permissionNames);
                    @endphp
                    <div wire:key="module-{{ Str::slug($moduleName) }}" class="border border-neutral-200 dark:border-neutral-800 rounded-lg p-4 bg-neutral-50/50 dark:bg-neutral-800/30 space-y-3">
                        <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700/60 pb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-sm text-neutral-900 dark:text-neutral-100">
                                    {{ $moduleName }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $moduleSelectedCount > 0 ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 font-semibold' : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400' }}">
                                    {{ $moduleSelectedCount }}/{{ count($permissionNames) }}
                                </span>
                            </div>

                            <button type="button"
                                    wire:click="toggleModule({{ json_encode($permissionNames) }})"
                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                {{ $isFullySelected ? 'Desmarcar' : 'Marcar todas' }}
                            </button>
                        </div>

                        {{-- Lista de Permissões do Módulo --}}
                        <div class="space-y-2 pt-1">
                            @foreach ($permissions as $permission)
                                <label wire:key="perm-cb-{{ $permission->id }}" class="flex items-start gap-2.5 cursor-pointer group">
                                    <input type="checkbox"
                                           value="{{ $permission->name }}"
                                           wire:model="selectedPermissions"
                                           class="mt-0.5 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800">
                                    <div class="text-xs">
                                        <span class="font-mono text-neutral-800 dark:text-neutral-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $permission->name }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Ações Finais do Formulário --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('roles.index') }}"
               wire:navigate
               class="px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors">
                Cancelar
            </a>

            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">
                    {{ $roleId ? 'Atualizar Perfil' : 'Salvar Perfil' }}
                </span>
                <span wire:loading wire:target="save">
                    Salvando...
                </span>
            </flux:button>
        </div>
    </form>
</div>
