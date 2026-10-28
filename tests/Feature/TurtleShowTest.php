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

    public function test_glossary_popovers_are_restored_for_all_five_turtles(): void
    {
        $cases = [
            ['Chelonia mydas', 'intro_description', 'Обитает в субтропических водах океана.', 'Субтропические воды'],
            ['Dermochelys coriacea', 'full_description', 'Роговых щитков нет.', 'Роговые щитки'],
            ['Chelydra serpentina', 'full_description', 'Черепаха с панцирем длиной до 35 см.', 'Панцирь'],
            ['Pyxis arachnoides', 'full_description', 'Передняя доля пластрона подвижна.', 'Пластрон'],
            ['Aldabrachelys gigantea', 'full_description', 'Длина карапакса до 120 см.', 'Карапакс'],
        ];

        foreach ($cases as [$scientificName, $field, $description, $title]) {
            $turtle = $this->createTurtle();
            $turtle->update([
                'scientific_name' => $scientificName,
                $field => $description,
            ]);

            $this->get(route('turtles.show', $turtle))
                ->assertOk()
                ->assertSee('data-bs-toggle="popover"', false)
                ->assertSee($title);
        }
    }

    public function test_glossary_keeps_database_text_html_escaped(): void
    {
        $turtle = $this->createTurtle();
        $turtle->update([
            'full_description' => '<script>alert(1)</script> панцирем',
        ]);

        $this->get(route('turtles.show', $turtle))
            ->assertOk()
            ->assertDontSee('<script>', false)
            ->assertSee('&lt;script&gt;', false)
            ->assertSee('data-bs-toggle="popover"', false);
    }
}
