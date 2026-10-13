<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Formulário de Usuário')]
class UserForm extends Component
{
    #[Locked]
    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Nomes das roles selecionadas para o usuário.
     *
     * @var array<string>
     */
    public array $selectedRoles = [];

    /**
     * Regras de validação dinâmicas do Livewire 3.
     * Necessárias pois $userId varia em tempo de execução (único ignorando ID e senha opcional na edição).
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'password' => [
                $this->userId ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'selectedRoles' => ['required', 'array', 'min:1'],
            'selectedRoles.*' => ['exists:roles,name'],
        ];
    }

    /**
     * Mensagens de validação customizadas em português.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'password.required' => 'A senha é obrigatória na criação de um usuário.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'selectedRoles.required' => 'Selecione ao menos um perfil de acesso (Role).',
            'selectedRoles.min' => 'Selecione ao menos um perfil de acesso (Role).',
        ];
    }

    /**
     * Inicializa o componente tanto no modo de criação quanto de edição.
     */
    public function mount(?int $userId = null): void
    {
        $this->userId = $userId;

        if ($this->userId) {
            $user = User::with('roles')->findOrFail($this->userId);

            // Autorização para edição
            $this->authorize('update', $user);

            $this->name = $user->name;
            $this->email = $user->email;
            $this->selectedRoles = $user->roles->pluck('name')->toArray();
        } else {
            // Autorização para criação
            $this->authorize('create', User::class);
        }
    }

    /**
     * Persiste as alterações no banco de dados.
     */
    public function save(): void
    {
        $this->validate();

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $this->authorize('update', $user);

            // Regra de Ouro: impedir que o usuário logado remova suas próprias roles
            if ($user->id === auth()->id()) {
                $currentRoles = $user->roles->pluck('name')->toArray();
                $removedRoles = array_diff($currentRoles, $this->selectedRoles);

                if (! empty($removedRoles)) {
                    $this->addError(
                        'selectedRoles',
                        'Por motivos de segurança, você não pode remover seus próprios perfis de acesso.'
                    );

                    return;
                }
            }

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            // Atualiza senha somente se informada
            if (! empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            }

            $user->update($userData);
            $user->syncRoles($this->selectedRoles);

            session()->flash('success', "Usuário '{$user->name}' atualizado com sucesso!");
        } else {
            $this->authorize('create', User::class);

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $user->syncRoles($this->selectedRoles);

            session()->flash('success', "Usuário '{$user->name}' criado com sucesso!");
        }

        $this->redirect(route('users.index'), navigate: true);
    }

    /**
     * Renderiza o formulário com a lista de perfis disponíveis.
     */
    public function render(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('livewire.users.user-form', [
            'roles' => $roles,
            'isEditing' => ! is_null($this->userId),
            'isSelf' => $this->userId === auth()->id(),
        ]);
    }
}
