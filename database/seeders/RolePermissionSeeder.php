<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'cashier-dashboard']);
        Permission::firstOrCreate(['name' => 'sell-products']);
        Permission::firstOrCreate(['name' => 'delete-selled-products']);

        Permission::firstOrCreate(['name' => 'add-products']);
        Permission::firstOrCreate(['name' => 'edit-products']);
        Permission::firstOrCreate(['name' => 'delete-products']);
        Permission::firstOrCreate(['name' => 'view-products']);

        Permission::firstOrCreate(['name' => 'admin-dashboard']);
        Permission::firstOrCreate(['name' => 'create-cashiers']);
        Permission::firstOrCreate(['name' => 'edit-cashiers']);
        Permission::firstOrCreate(['name' => 'delete-cashiers']);
        Permission::firstOrCreate(['name' => 'view-cashiers']);

        Role::firstOrCreate(['name' => 'cashier']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'buyer']);

        $roleAdmin = Role::findByName('admin');
        $roleAdmin->givePermissionTo('admin-dashboard');
        $roleAdmin->givePermissionTo('create-cashiers');
        $roleAdmin->givePermissionTo('edit-cashiers');
        $roleAdmin->givePermissionTo('delete-cashiers');
        $roleAdmin->givePermissionTo('view-cashiers');

        $roleCashier = Role::findByName('cashier');
        $roleCashier->givePermissionTo('cashier-dashboard');
        $roleCashier->givePermissionTo('sell-products');
        $roleCashier->givePermissionTo('delete-selled-products');
        $roleCashier->givePermissionTo('add-products');
        $roleCashier->givePermissionTo('edit-products');
        $roleCashier->givePermissionTo('delete-products');
        $roleCashier->givePermissionTo('view-products');
    }
}
