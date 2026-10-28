<?php

namespace Tests\Feature;

use App\Models\Turtle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurtleDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function createTurtle(): Turtle
    {
        return Turtle::create([
            'name' => 'Тестовая черепаха',
            'scientific_name' => 'Testudo test',
            'category' => 'Сухопутная',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Вводное описание',
            'full_description' => 'Подробное описание',
            'image_path' => 'images/Sea-turtle.jpg',
        ]);
    }

    public function test_detail_page_has_delete_form_with_csrf_and_method_spoofing(): void
    {
        $turtle = $this->createTurtle();

        $this->get(route('turtles.show', $turtle))
            ->assertOk()
            ->assertSee(route('turtles.destroy', $turtle))
            ->assertSee('name="_method" value="DELETE"', false)
            ->assertSee('Удалить черепаху');
    }

    public function test_delete_soft_deletes_turtle_and_redirects_to_catalog(): void
    {
        $turtle = $this->createTurtle();

        $this->delete(route('turtles.destroy', $turtle))
            ->assertRedirect(route('turtles.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted($turtle);
        $this->assertDatabaseHas('turtles', [
            'id' => $turtle->id,
            'image_path' => 'images/Sea-turtle.jpg',
        ]);

        $this->get('/')->assertOk()->assertDontSee('Тестовая черепаха');
        $this->get(route('turtles.show', $turtle))->assertNotFound();
    }

    public function test_get_request_cannot_delete_turtle(): void
    {
        $turtle = $this->createTurtle();

        $this->get(route('turtles.destroy', $turtle))
            ->assertOk(); // GET /turtles/{turtle} is the show route, not destroy.

        $this->assertDatabaseHas('turtles', [
            'id' => $turtle->id,
            'deleted_at' => null,
        ]);
    }
}
