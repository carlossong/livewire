<?php

namespace App\Livewire\Users;

use App\Models\User;
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
     * Exclui um usuário do sistema.
     * Regra de Ouro: impede a exclusão da própria conta logada.
     */
    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'Você não pode excluir sua própria conta.');

            return;
        }

        $this->authorize('delete', $user);

        $userName = $user->name;
        $user->delete();

        session()->flash('success', "Usuário '{$userName}' excluído com sucesso!");
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
