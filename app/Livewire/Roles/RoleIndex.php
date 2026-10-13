<?php

namespace App\Livewire\Roles;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Perfis de Acesso (RBAC)')]
class RoleIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public bool $showDeleteModal = false;

    public ?int $roleToDeleteId = null;

    public string $roleToDeleteName = '';

    /**
     * Reseta a paginação ao atualizar a busca.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Inicializa e valida autorização.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', Role::class);
    }

    /**
     * Abre a modal de confirmação do Flux para exclusão de um perfil.
     */
    public function confirmDelete(int $id): void
    {
        $role = Role::withCount('users')->findOrFail($id);

        if (strtolower($role->name) === 'admin') {
            Flux::toast(variant: 'danger', text: 'O perfil Administrador do sistema não pode ser excluído.');

            return;
        }

        if ($role->users_count > 0) {
            Flux::toast(
                variant: 'danger',
                text: "O perfil '{$role->name}' possui {$role->users_count} usuário(s) associado(s) e não pode ser excluído."
            );

            return;
        }

        $this->roleToDeleteId = (int) $role->id;
        $this->roleToDeleteName = $role->name;
        $this->showDeleteModal = true;
    }

    /**
     * Fecha a modal de confirmação.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->roleToDeleteId = null;
        $this->roleToDeleteName = '';
    }

    /**
     * Executa a exclusão da role após confirmação na modal.
     */
    public function deleteRole(): void
    {
        if (! $this->roleToDeleteId) {
            return;
        }

        $role = Role::withCount('users')->findOrFail($this->roleToDeleteId);

        if (strtolower($role->name) === 'admin') {
            Flux::toast(variant: 'danger', text: 'O perfil Administrador do sistema não pode ser excluído.');
            $this->closeDeleteModal();

            return;
        }

        if ($role->users_count > 0) {
            Flux::toast(
                variant: 'danger',
                text: "O perfil '{$role->name}' possui {$role->users_count} usuário(s) associado(s) e não pode ser excluído."
            );
            $this->closeDeleteModal();

            return;
        }

        $this->authorize('delete', $role);

        $roleName = $role->name;
        $role->delete();

        $this->closeDeleteModal();

        Flux::toast(variant: 'success', text: "Perfil '{$roleName}' excluído com sucesso!");
        session()->flash('success', "Perfil '{$roleName}' excluído com sucesso!");
    }

    /**
     * Renderiza a listagem de perfis com busca, contagem de usuários e permissões.
     */
    public function render(): View
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->with(['permissions' => function ($query) {
                $query->limit(8);
            }])
            ->when(trim($this->search) !== '', function ($query) {
                $searchTerm = '%'.trim($this->search).'%';
                $query->where('name', 'like', $searchTerm);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.roles.role-index', [
            'roles' => $roles,
        ]);
    }
}
