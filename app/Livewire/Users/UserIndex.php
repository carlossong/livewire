<?php

namespace App\Livewire\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Gerenciamento de Usuários')]
class UserIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public bool $showDeleteModal = false;

    public ?int $userToDeleteId = null;

    public string $userToDeleteName = '';

    /**
     * Reseta a paginação ao digitar na busca.
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
        $this->authorize('viewAny', User::class);
    }

    /**
     * Abre a modal de confirmação do Flux.
     */
    public function confirmDelete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            Flux::toast(variant: 'danger', text: 'Você não pode excluir sua própria conta.');

            return;
        }

        $this->userToDeleteId = $user->id;
        $this->userToDeleteName = $user->name;
        $this->showDeleteModal = true;
    }

    /**
     * Fecha a modal de confirmação de exclusão.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->userToDeleteId = null;
        $this->userToDeleteName = '';
    }

    /**
     * Exclui o usuário após confirmação na modal.
     * Regra de Ouro: impede a exclusão da própria conta logada.
     */
    public function deleteUser(): void
    {
        if (! $this->userToDeleteId) {
            return;
        }

        $user = User::findOrFail($this->userToDeleteId);

        if ($user->id === auth()->id()) {
            Flux::toast(variant: 'danger', text: 'Você não pode excluir sua própria conta.');
            $this->closeDeleteModal();

            return;
        }

        $this->authorize('delete', $user);

        $userName = $user->name;
        $user->delete();

        $this->closeDeleteModal();

        Flux::toast(variant: 'success', text: "Usuário '{$userName}' excluído com sucesso!");
        session()->flash('success', "Usuário '{$userName}' excluído com sucesso!");
    }

    /**
     * Mantém compatibilidade com chamadas diretas.
     */
    public function delete(int $id): void
    {
        $this->confirmDelete($id);
    }

    /**
     * Renderiza a listagem de usuários com busca e paginação.
     */
    public function render(): View
    {
        $users = User::with('roles')
            ->when(trim($this->search) !== '', function ($query) {
                $query->where(function ($q) {
                    $searchTerm = '%'.trim($this->search).'%';
                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm);
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.users.user-index', [
            'users' => $users,
        ]);
    }
}
