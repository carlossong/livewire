<div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
    {{-- Cabeçalho e Navegação --}}
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <a href="{{ route('users.index') }}"
                   wire:navigate
                   class="inline-flex items-center text-sm font-medium text-neutral-500 hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-neutral-200 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Voltar para Usuários
                </a>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
                {{ $isEditing ? 'Editar Usuário: ' . $name : 'Novo Usuário' }}
            </h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                {{ $isEditing ? 'Atualize as credenciais, informações e perfis deste usuário.' : 'Preencha os campos abaixo para criar um novo usuário no sistema.' }}
            </p>
        </div>
    </div>

    {{-- Aviso de Edição do Próprio Usuário --}}
    @if ($isSelf)
        <div class="flex items-start p-4 text-sm text-amber-800 rounded-xl bg-amber-50 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800" role="alert">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
                <span class="font-semibold">Atenção:</span> Você está editando sua própria conta. Por segurança, você não pode remover seus perfis atuais de acesso.
            </div>
        </div>
    @endif

    {{-- Formulário Principal --}}
    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-sm p-6 sm:p-8">
        <form wire:submit="save" class="space-y-6">
            {{-- Linha 1: Nome e E-mail --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                {{-- Nome --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Nome Completo <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           wire:model="name"
                           class="mt-1.5 block w-full rounded-lg border text-sm py-2 px-3 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 {{ $errors->has('name') ? 'border-rose-500 focus:ring-rose-500 focus:border-rose-500' : 'border-neutral-200 dark:border-neutral-700 focus:ring-indigo-500 focus:border-indigo-500' }} focus:outline-none focus:ring-2 transition-colors"
                           placeholder="Ex: Ana Silva">
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- E-mail --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Endereço de E-mail <span class="text-rose-500">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           wire:model="email"
                           class="mt-1.5 block w-full rounded-lg border text-sm py-2 px-3 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500 focus:border-rose-500' : 'border-neutral-200 dark:border-neutral-700 focus:ring-indigo-500 focus:border-indigo-500' }} focus:outline-none focus:ring-2 transition-colors"
                           placeholder="usuario@exemplo.com">
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Linha 2: Senha e Confirmação --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 pt-2 border-t border-neutral-100 dark:border-neutral-800">
                {{-- Senha --}}
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Senha
                            @if (! $isEditing)
                                <span class="text-rose-500">*</span>
                            @endif
                        </label>
                        @if ($isEditing)
                            <span class="text-xs text-neutral-400 dark:text-neutral-500">Opcional na edição</span>
                        @endif
                    </div>
                    <input type="password"
                           id="password"
                           wire:model="password"
                           class="mt-1.5 block w-full rounded-lg border text-sm py-2 px-3 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500 focus:border-rose-500' : 'border-neutral-200 dark:border-neutral-700 focus:ring-indigo-500 focus:border-indigo-500' }} focus:outline-none focus:ring-2 transition-colors"
                           placeholder="{{ $isEditing ? 'Deixe em branco para manter' : 'Mínimo de 8 caracteres' }}">
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmação de Senha --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Confirmar Senha
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           wire:model="password_confirmation"
                           class="mt-1.5 block w-full rounded-lg border text-sm py-2 px-3 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 border-neutral-200 dark:border-neutral-700 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none focus:ring-2 transition-colors"
                           placeholder="Repita a senha">
                </div>
            </div>

            {{-- Linha 3: Perfis de Acesso (Roles) --}}
            <div class="pt-2 border-t border-neutral-100 dark:border-neutral-800">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Perfis de Acesso (Roles) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-xs text-neutral-400 dark:text-neutral-500">Selecione ao menos um</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @forelse ($roles as $role)
                        <label wire:key="role-checkbox-{{ $role->id }}"
                               class="relative flex items-center p-3.5 rounded-lg border cursor-pointer transition-colors {{ in_array($role->name, $selectedRoles) ? 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/20' : 'border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/40 hover:bg-neutral-100/50 dark:hover:bg-neutral-800/80' }}">
                            <input type="checkbox"
                                   value="{{ $role->name }}"
                                   wire:model="selectedRoles"
                                   class="w-4 h-4 text-indigo-600 bg-neutral-100 border-neutral-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-neutral-900 focus:ring-2 dark:bg-neutral-700 dark:border-neutral-600">
                            <span class="ml-3 text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $role->name }}
                            </span>
                        </label>
                    @empty
                        <div class="col-span-full p-4 text-sm text-neutral-500 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                            Nenhuma role cadastrada no banco de dados. Execute o Seeder de roles.
                        </div>
                    @endforelse
                </div>

                @error('selectedRoles')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Rodapé / Ações --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-neutral-100 dark:border-neutral-800">
                <a href="{{ route('users.index') }}"
                   wire:navigate
                   class="px-4 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-500 transition-colors">
                    Cancelar
                </a>

                <button type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900 disabled:opacity-50 transition-colors">
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ $isEditing ? 'Salvar Alterações' : 'Criar Usuário' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
