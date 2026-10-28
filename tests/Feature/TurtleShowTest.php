<?php

namespace Tests\Feature;

use App\Models\Turtle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurtleShowTest extends TestCase
{
    use RefreshDatabase;

    private function createTurtle(): Turtle
    {
        return Turtle::create([
            'name' => 'Каймановая черепаха',
            'scientific_name' => 'Chelydra serpentina',
            'category' => 'Пресноводная',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Введение о виде',
            'full_description' => 'Подробное описание каймановой черепахи',
            'image_path' => 'images/Common_Snapping_Turtle.jpg',
            'catalogued_at' => '2026-10-28',
        ]);
    }

    public function test_turtle_detail_page_displays_data_from_database(): void
    {
        $turtle = $this->createTurtle();

        $this->get(route('turtles.show', $turtle))
            ->assertOk()
            ->assertSee('Каймановая черепаха')
            ->assertSee('Chelydra serpentina')
            ->assertSee('Подробное описание каймановой черепахи')
            ->assertSee('Common_Snapping_Turtle.jpg');
    }

    public function test_catalog_contains_link_to_detail_page(): void
    {
        $turtle = $this->createTurtle();

        $this->get('/')
            ->assertOk()
            ->assertSee(route('turtles.show', $turtle));
    }

    public function test_missing_turtle_returns_404(): void
    {
        $this->get('/turtles/99999')->assertNotFound();
    }

    public function test_soft_deleted_turtle_returns_404(): void
    {
        $turtle = $this->createTurtle();
        $turtle->delete();

        $this->get(route('turtles.show', $turtle))->assertNotFound();
    }
}
