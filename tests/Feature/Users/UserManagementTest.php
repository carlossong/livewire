<?php

use App\Livewire\Users\UserIndex;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Permissões
    $permissions = ['users.view', 'users.create', 'users.edit', 'users.delete'];
    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    $this->adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    $this->adminRole->syncPermissions(Permission::all());

    $this->adminUser = User::factory()->create([
        'email' => 'test-admin@empresa.com',
    ]);
    $this->adminUser->assignRole($this->adminRole);
});

test('admin can access users index page', function () {
    $this->actingAs($this->adminUser)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('Gerenciamento de Usuários');
});

test('unauthorized user cannot access users index', function () {
    $regularUser = User::factory()->create();

    $this->actingAs($regularUser)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('admin can trigger flux delete modal confirmation', function () {
    $targetUser = User::factory()->create(['name' => 'Alvo de Exclusão']);

    Livewire::actingAs($this->adminUser)
        ->test(UserIndex::class)
        ->assertSet('showDeleteModal', false)
        ->call('confirmDelete', $targetUser->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('userToDeleteId', $targetUser->id)
        ->assertSet('userToDeleteName', 'Alvo de Exclusão');
});

test('admin can delete a user after confirming in flux modal', function () {
    $targetUser = User::factory()->create(['name' => 'Usuario Para Deletar']);

    Livewire::actingAs($this->adminUser)
        ->test(UserIndex::class)
        ->call('confirmDelete', $targetUser->id)
        ->call('deleteUser')
        ->assertSet('showDeleteModal', false)
        ->assertSet('userToDeleteId', null);

    expect(User::find($targetUser->id))->toBeNull();
});

test('user cannot delete self via flux confirmation modal', function () {
    Livewire::actingAs($this->adminUser)
        ->test(UserIndex::class)
        ->call('confirmDelete', $this->adminUser->id)
        ->assertSet('showDeleteModal', false);

    expect(User::find($this->adminUser->id))->not->toBeNull();
});
