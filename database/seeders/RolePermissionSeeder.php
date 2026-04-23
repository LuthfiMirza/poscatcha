<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        Permission::create(['name' => 'cashier-dashboard']);
        Permission::create(['name' => 'sell-products']);
        Permission::create(['name' => 'delete-selled-products']);

        Permission::create(['name' => 'add-products']);
        Permission::create(['name' => 'edit-products']);
        Permission::create(['name' => 'delete-products']);
        Permission::create(['name' => 'view-products']);

        Permission::create(['name' => 'admin-dashboard']);
        Permission::create(['name' => 'create-cashiers']);
        Permission::create(['name' => 'edit-cashiers']);
        Permission::create(['name' => 'delete-cashiers']);
        Permission::create(['name' => 'view-cashiers']);

        Role::create(['name' => 'cashier']);
        Role::create(['name' => 'admin']);

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
