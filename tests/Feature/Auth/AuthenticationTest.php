<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_auth_pages(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_user_can_register(): void
    {
        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'test-password',
            'password_confirmation' => 'test-password',
        ])->assertRedirect(route('turtles.index', absolute: false));

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('test-password', $user->password));
    }

    public function test_registration_rejects_email_without_domain_suffix(): void
    {
        foreach (['123@gmail', '123@gmail.', 'not-an-email'] as $email) {
            $this->post(route('register'), [
                'name' => 'Invalid Email',
                'email' => $email,
                'password' => 'test-password',
                'password_confirmation' => 'test-password',
            ])->assertSessionHasErrors('email');

            $this->assertGuest();
        }

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        $user = User::factory()->create();

        $this->post(route('register'), [
            'name' => 'Another User',
            'email' => $user->email,
            'password' => 'test-password',
            'password_confirmation' => 'test-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('test-password'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'test-password',
        ])->assertRedirect(route('turtles.index', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_invalid_password_does_not_authenticate(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('test-password'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
