<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $this->get(route('buyer.register'))->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Role::firstOrCreate(['name' => 'buyer']);

        $response = $this->post(route('buyer.register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('buyer'));
        $response->assertRedirect(route('buyer.shop.index', absolute: false));
    }

    public function test_registration_request_cannot_choose_admin_or_cashier_role(): void
    {
        Role::firstOrCreate(['name' => 'buyer']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'cashier']);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('buyer'));
        $this->assertFalse(auth()->user()->hasAnyRole(['admin', 'cashier']));
    }
}
