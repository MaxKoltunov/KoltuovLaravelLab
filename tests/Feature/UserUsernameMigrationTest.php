<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserUsernameMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_accounts_get_unique_usernames_without_losing_ownership(): void
    {
        $first = User::factory()->create(['name' => 'amogus']);
        $second = User::factory()->create(['name' => 'amogus']);
        $numeric = User::factory()->create(['name' => '123']);
        $first->is_admin = true;
        $first->save();
        $turtle = $first->turtles()->create([
            'name' => 'Черепаха',
            'scientific_name' => 'Testudo migration',
            'category' => 'Морская',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Введение',
            'full_description' => 'Описание',
        ]);

        $migration = require database_path('migrations/2026_11_26_000002_add_username_to_users_table.php');
        $migration->down();
        $migration->up();

        $this->assertSame('amogus', $first->fresh()->username);
        $this->assertSame('amogus_2', $second->fresh()->username);
        $this->assertSame('user_123', $numeric->fresh()->username);
        $this->assertTrue($first->fresh()->isAdmin());
        $this->assertSame($first->id, $turtle->fresh()->user_id);
    }

    public function test_database_rejects_duplicate_username(): void
    {
        $first = User::factory()->create(['username' => 'unique_name']);
        $second = User::factory()->create();

        $this->expectException(QueryException::class);

        DB::table('users')->where('id', $second->id)->update([
            'username' => $first->username,
        ]);
    }
}
