<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_not_administrators_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->fresh()->is_admin);

        // Проверяем именно значение по умолчанию в базе без Eloquent.
        $id = DB::table('users')->insertGetId([
            'name' => 'Database User',
            'email' => 'db@example.com',
            'password' => $user->password,
        ]);

        $this->assertFalse(User::findOrFail($id)->isAdmin());
    }

    public function test_admin_role_can_be_assigned_explicitly(): void
    {
        $user = User::factory()->create();

        $user->is_admin = true;
        $user->save();

        $this->assertTrue($user->fresh()->isAdmin());
    }

    public function test_admin_flag_is_not_mass_assignable(): void
    {
        $user = new User();

        $this->assertFalse($user->isFillable('is_admin'));
    }

    public function test_registration_cannot_grant_admin_role(): void
    {
        $this->post(route('register'), [
            'name' => 'Ordinary User',
            'email' => 'ordinary@example.com',
            'password' => 'test-password',
            'password_confirmation' => 'test-password',
            'is_admin' => true,
        ])->assertSessionHasNoErrors();

        $user = User::where('email', 'ordinary@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->isAdmin());
    }
}
