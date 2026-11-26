<?php

namespace Tests\Feature;

use App\Models\Turtle;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TurtleOwnershipMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function createTurtle(): Turtle
    {
        return Turtle::create([
            'name' => 'Зелёная черепаха',
            'scientific_name' => 'Chelonia mydas',
            'category' => 'Морская',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Введение',
            'full_description' => 'Подробное описание',
        ]);
    }

    public function test_turtle_can_exist_without_owner_and_be_assigned_to_user(): void
    {
        $turtle = $this->createTurtle();
        $user = User::factory()->create();

        $this->assertDatabaseHas('turtles', [
            'id' => $turtle->id,
            'user_id' => null,
        ]);

        DB::table('turtles')->where('id', $turtle->id)->update([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('turtles', [
            'id' => $turtle->id,
            'user_id' => $user->id,
        ]);

        $user->delete();

        $this->assertDatabaseHas('turtles', [
            'id' => $turtle->id,
            'user_id' => null,
        ]);
    }

    public function test_foreign_key_rejects_nonexistent_user(): void
    {
        $turtle = $this->createTurtle();

        $this->expectException(QueryException::class);

        DB::table('turtles')->where('id', $turtle->id)->update([
            'user_id' => 999999,
        ]);
    }
}
