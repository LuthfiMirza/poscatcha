<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('cashier');
        Role::findOrCreate('buyer');

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $ariz = User::updateOrCreate(
            ['email' => 'ariz@gmail.com'],
            [
                'name' => 'ariz',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $ariz->syncRoles(['cashier']);

        $koko = User::updateOrCreate(
            ['email' => 'koko@gmail.com'],
            [
                'name' => 'koko',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $koko->syncRoles(['cashier']);
    }
}
