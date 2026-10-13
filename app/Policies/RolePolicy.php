<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode listar perfis.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determina se o usuário pode visualizar um perfil.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determina se o usuário pode criar perfis.
     */
    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    /**
     * Determina se o usuário pode atualizar um perfil.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('roles.edit');
    }

    /**
     * Determina se o usuário pode excluir um perfil.
     * Regras de Integridade:
     * 1. O perfil 'Admin' NUNCA pode ser excluído.
     * 2. Um perfil com usuários associados não pode ser excluído.
     */
    public function delete(User $user, Role $role): bool
    {
        if (strtolower($role->name) === 'admin') {
            return false;
        }

        if ($role->users()->count() > 0) {
            return false;
        }

        return $user->can('roles.delete');
    }
}
