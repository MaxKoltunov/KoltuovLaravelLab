<?php

namespace Tests\Feature;

use App\Models\Turtle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurtleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_creates_five_turtles(): void
    {
        $this->seed();

        $this->assertSame(5, Turtle::count());
        $this->assertDatabaseHas('turtles', [
            'scientific_name' => 'Chelydra serpentina',
        ]);
    }

    public function test_repeat_seeding_preserves_user_edits_and_deleted_records(): void
    {
        $this->seed();

        $edited = Turtle::where('scientific_name', 'Chelonia mydas')->firstOrFail();
        $edited->update(['name' => 'Изменённое пользователем название']);

        $deleted = Turtle::where('scientific_name', 'Chelydra serpentina')->firstOrFail();
        $deleted->delete();

        $this->seed();

        $this->assertSame(4, Turtle::count());
        $this->assertSame(5, Turtle::withTrashed()->count());
        $this->assertSame('Изменённое пользователем название', $edited->fresh()->name);
        $this->assertNotNull(
            Turtle::withTrashed()->findOrFail($deleted->id)->deleted_at
        );
    }
}
