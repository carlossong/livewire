<?php

use App\Livewire\Roles\RoleForm;
use App\Livewire\Roles\RoleIndex;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Permissões de teste
    $permissions = [
        'users.view', 'users.create', 'users.edit', 'users.delete',
        'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
        'orders.view', 'orders.create', 'financial.view', 'financial.create',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    $this->adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    $this->adminRole->syncPermissions(Permission::all());

    $this->adminUser = User::factory()->create([
        'email' => 'admin-rbac@empresa.com',
    ]);
    $this->adminUser->assignRole($this->adminRole);
});

test('admin can access roles index page', function () {
    $this->actingAs($this->adminUser)
        ->get(route('roles.index'))
        ->assertOk()
        ->assertSee('Perfis de Acesso (RBAC)');
});

test('unauthorized user cannot access roles index', function () {
    $regularUser = User::factory()->create();

    $this->actingAs($regularUser)
        ->get(route('roles.index'))
        ->assertForbidden();
});

test('admin can access role create page', function () {
    $this->actingAs($this->adminUser)
        ->get(route('roles.create'))
        ->assertOk()
        ->assertSee('Criar Novo Perfil');
});

test('admin can create a new role with selected permissions', function () {
    Livewire::actingAs($this->adminUser)
        ->test(RoleForm::class)
        ->set('name', 'Supervisor de Vendas')
        ->set('selectedPermissions', ['orders.view', 'orders.create'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('roles.index'));

    $role = Role::where('name', 'Supervisor de Vendas')->first();
    expect($role)->not->toBeNull()
        ->and($role->hasPermissionTo('orders.view'))->toBeTrue()
        ->and($role->hasPermissionTo('orders.create'))->toBeTrue()
        ->and($role->hasPermissionTo('financial.view'))->toBeFalse();
});

test('admin can edit an existing role and sync permissions', function () {
    $role = Role::create(['name' => 'Financeiro Jr', 'guard_name' => 'web']);
    $role->syncPermissions(['financial.view']);

    Livewire::actingAs($this->adminUser)
        ->test(RoleForm::class, ['roleId' => $role->id])
        ->set('name', 'Analista Financeiro Pleno')
        ->set('selectedPermissions', ['financial.view', 'financial.create'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('roles.index'));

    $role->refresh();
    expect($role->name)->toBe('Analista Financeiro Pleno')
        ->and($role->hasPermissionTo('financial.create'))->toBeTrue();
});

test('admin cannot rename the admin system role', function () {
    Livewire::actingAs($this->adminUser)
        ->test(RoleForm::class, ['roleId' => $this->adminRole->id])
        ->set('name', 'Super Admin')
        ->call('save');

    $this->adminRole->refresh();
    expect($this->adminRole->name)->toBe('Admin');
});

test('admin can trigger flux delete modal confirmation for roles', function () {
    $customRole = Role::create(['name' => 'Temporario', 'guard_name' => 'web']);

    Livewire::actingAs($this->adminUser)
        ->test(RoleIndex::class)
        ->assertSet('showDeleteModal', false)
        ->call('confirmDelete', $customRole->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('roleToDeleteId', $customRole->id)
        ->assertSet('roleToDeleteName', 'Temporario');
});

test('admin can delete a role without users via flux confirmation modal', function () {
    $customRole = Role::create(['name' => 'Role Descartavel', 'guard_name' => 'web']);

    Livewire::actingAs($this->adminUser)
        ->test(RoleIndex::class)
        ->call('confirmDelete', $customRole->id)
        ->call('deleteRole')
        ->assertSet('showDeleteModal', false)
        ->assertSet('roleToDeleteId', null);

    expect(Role::find($customRole->id))->toBeNull();
});

test('admin cannot delete the admin system role', function () {
    Livewire::actingAs($this->adminUser)
        ->test(RoleIndex::class)
        ->call('confirmDelete', $this->adminRole->id)
        ->assertSet('showDeleteModal', false);

    expect(Role::find($this->adminRole->id))->not->toBeNull();
});

test('admin cannot delete a role with associated users', function () {
    $operatorRole = Role::create(['name' => 'Operador', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($operatorRole);

    Livewire::actingAs($this->adminUser)
        ->test(RoleIndex::class)
        ->call('confirmDelete', $operatorRole->id)
        ->assertSet('showDeleteModal', false);

    expect(Role::find($operatorRole->id))->not->toBeNull();
});
