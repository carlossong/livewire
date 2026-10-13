<?php

namespace App\Livewire\Roles;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Formulário de Perfil')]
class RoleForm extends Component
{
    #[Locked]
    public ?int $roleId = null;

    public string $name = '';

    /**
     * Nomes das permissões selecionadas para o perfil.
     *
     * @var array<string>
     */
    public array $selectedPermissions = [];

    /**
     * Mapeamento de rótulos dos módulos para agrupamento visual.
     *
     * @var array<string, string>
     */
    protected array $moduleLabels = [
        'users' => 'Usuários',
        'roles' => 'Perfis & Permissões',
        'customers' => 'Clientes',
        'orders' => 'Vendas & Pedidos',
        'financial' => 'Financeiro',
        'invoices' => 'Financeiro',
        'products' => 'Estoque & Produtos',
        'stock' => 'Estoque & Produtos',
        'reports' => 'Relatórios',
    ];

    /**
     * Regras de validação do formulário.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->roleId),
            ],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['exists:permissions,name'],
        ];
    }

    /**
     * Mensagens amigáveis de validação em português.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.unique' => 'Já existe um perfil cadastrado com este nome.',
            'name.max' => 'O nome do perfil não pode exceder 255 caracteres.',
        ];
    }

    /**
     * Inicializa o componente para criação ou edição.
     */
    public function mount(?int $roleId = null): void
    {
        $this->roleId = $roleId;

        if ($this->roleId) {
            $role = Role::with('permissions')->findOrFail($this->roleId);

            $this->authorize('update', $role);

            $this->name = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        } else {
            $this->authorize('create', Role::class);
        }
    }

    /**
     * Agrupa as permissões por módulo funcional.
     *
     * @return array<string, array<int, Permission>>
     */
    #[Computed]
    public function groupedPermissions(): array
    {
        $permissions = Permission::orderBy('name')->get();
        $grouped = [];

        foreach ($permissions as $permission) {
            $prefix = explode('.', $permission->name)[0];
            $moduleName = $this->moduleLabels[$prefix] ?? ucfirst($prefix);

            if (! isset($grouped[$moduleName])) {
                $grouped[$moduleName] = [];
            }

            $grouped[$moduleName][] = $permission;
        }

        return $grouped;
    }

    /**
     * Alterna a seleção de todas as permissões de um módulo específico.
     *
     * @param  array<string>  $permissionNames
     */
    public function toggleModule(array $permissionNames): void
    {
        $allSelected = count(array_intersect($permissionNames, $this->selectedPermissions)) === count($permissionNames);

        if ($allSelected) {
            // Desmarca todas do módulo
            $this->selectedPermissions = array_values(
                array_diff($this->selectedPermissions, $permissionNames)
            );
        } else {
            // Marca todas do módulo
            $this->selectedPermissions = array_values(
                array_unique(array_merge($this->selectedPermissions, $permissionNames))
            );
        }
    }

    /**
     * Seleciona todas as permissões cadastradas no sistema.
     */
    public function selectAll(): void
    {
        $this->selectedPermissions = Permission::pluck('name')->toArray();
    }

    /**
     * Desmarca todas as permissões selecionadas.
     */
    public function deselectAll(): void
    {
        $this->selectedPermissions = [];
    }

    /**
     * Salva o perfil e sincroniza as permissões.
     */
    public function save(): mixed
    {
        $validated = $this->validate();

        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);

            $this->authorize('update', $role);

            // Regra de Integridade: Impedir renomeação do perfil 'Admin'
            if (strtolower($role->name) === 'admin' && strtolower($validated['name']) !== 'admin') {
                Flux::toast(variant: 'danger', text: 'O nome do perfil Administrador não pode ser alterado.');

                return null;
            }

            $role->update([
                'name' => $validated['name'],
            ]);

            $role->syncPermissions($this->selectedPermissions);

            Flux::toast(variant: 'success', text: "Perfil '{$role->name}' atualizado com sucesso!");
            session()->flash('success', "Perfil '{$role->name}' atualizado com sucesso!");
        } else {
            $this->authorize('create', Role::class);

            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($this->selectedPermissions);

            Flux::toast(variant: 'success', text: "Perfil '{$role->name}' criado com sucesso!");
            session()->flash('success', "Perfil '{$role->name}' criado com sucesso!");
        }

        return redirect()->route('roles.index');
    }

    /**
     * Renderiza o template do formulário.
     */
    public function render(): View
    {
        return view('livewire.roles.role-form');
    }
}
