<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpar cache de permissões e roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Mapeamento de Permissões por Módulo da PME
        $modulesPermissions = [
            'Usuários' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
            ],
            'Clientes' => [
                'customers.view',
                'customers.create',
                'customers.edit',
                'customers.delete',
            ],
            'Vendas' => [
                'orders.view',
                'orders.create',
                'orders.edit',
                'orders.cancel',
                'orders.approve_discount',
            ],
            'Financeiro' => [
                'financial.view',
                'financial.create',
                'financial.edit',
                'invoices.generate',
            ],
            'Estoque' => [
                'products.view',
                'products.create',
                'products.edit',
                'stock.adjust',
            ],
            'Relatórios' => [
                'reports.sales',
                'reports.financial',
                'reports.stock',
            ],
            'Perfis' => [
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
            ],
        ];

        foreach ($modulesPermissions as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => 'web']
                );
            }
        }

        // 3. Criar as Roles para PME
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'Gerente', 'guard_name' => 'web']);
        $financial = Role::firstOrCreate(['name' => 'Financeiro', 'guard_name' => 'web']);
        $sales = Role::firstOrCreate(['name' => 'Vendas', 'guard_name' => 'web']);
        $inventory = Role::firstOrCreate(['name' => 'Estoque', 'guard_name' => 'web']);
        $support = Role::firstOrCreate(['name' => 'Atendimento', 'guard_name' => 'web']);

        // 4. Atribuir Permissões às Roles

        // Admin: Todas as permissões do sistema
        $admin->syncPermissions(Permission::all());

        // Gerente: Visão transversal, aprovações e relatórios
        $manager->syncPermissions([
            'users.view',
            'customers.view',
            'customers.create',
            'customers.edit',
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.cancel',
            'orders.approve_discount',
            'financial.view',
            'products.view',
            'stock.adjust',
            'reports.sales',
            'reports.financial',
            'reports.stock',
        ]);

        // Financeiro: Módulo financeiro, emissão de NFe e relatórios
        $financial->syncPermissions([
            'financial.view',
            'financial.create',
            'financial.edit',
            'invoices.generate',
            'orders.view',
            'reports.financial',
        ]);

        // Vendas: Gestão de clientes, pedidos e catálogo
        $sales->syncPermissions([
            'customers.view',
            'customers.create',
            'customers.edit',
            'orders.view',
            'orders.create',
            'products.view',
            'reports.sales',
        ]);

        // Estoque: Gestão de produtos e movimentação de inventário
        $inventory->syncPermissions([
            'products.view',
            'products.create',
            'products.edit',
            'stock.adjust',
            'reports.stock',
        ]);

        // Atendimento: Consulta básica de clientes e pedidos
        $support->syncPermissions([
            'customers.view',
            'orders.view',
            'products.view',
        ]);

        // 5. Garantir que o Administrador padrão existe e possui a Role Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($admin);
    }
}
