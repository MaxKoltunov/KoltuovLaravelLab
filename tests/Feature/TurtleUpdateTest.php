<?php

namespace Tests\Feature;

use App\Models\Turtle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TurtleUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function createTurtle(array $changes = []): Turtle
    {
        return Turtle::create(array_merge([
            'name' => 'Зелёная черепаха',
            'scientific_name' => 'Chelonia mydas',
            'category' => 'Морская',
            'short_description' => 'Исходное краткое описание',
            'intro_description' => 'Исходное введение',
            'full_description' => 'Исходное подробное описание',
            'catalogued_at' => '2026-10-28',
            'image_path' => 'images/Sea-turtle.jpg',
        ], $changes));
    }

    private function validData(Turtle $turtle, array $changes = []): array
    {
        return array_merge([
            'name' => $turtle->name,
            'scientific_name' => $turtle->scientific_name,
            'category' => $turtle->category,
            'short_description' => $turtle->short_description,
            'intro_description' => $turtle->intro_description,
            'full_description' => $turtle->full_description,
            'catalogued_at' => '2026-10-28',
        ], $changes);
    }

    public function test_edit_page_shows_existing_values_and_file_input(): void
    {
        $turtle = $this->createTurtle();

        $this->get(route('turtles.edit', $turtle))
            ->assertOk()
            ->assertSee('Редактировать черепаху')
            ->assertSee('Исходное краткое описание')
            ->assertSee('Chelonia mydas')
            ->assertSee('name="image"', false)
            ->assertSee('name="_method" value="PUT"', false);

        $this->get(route('turtles.show', $turtle))
            ->assertOk()
            ->assertSee(route('turtles.edit', $turtle));
    }

    public function test_update_changes_existing_record_without_creating_another(): void
    {
        $turtle = $this->createTurtle();

        $this->put(route('turtles.update', $turtle), $this->validData($turtle, [
            'name' => 'Обновлённая зелёная черепаха',
            'category' => 'Сухопутная',
            'full_description' => 'Новое подробное описание',
        ]))
            ->assertRedirect(route('turtles.show', $turtle))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('turtles', 1);
        $this->assertDatabaseHas('turtles', [
            'id' => $turtle->id,
            'name' => 'Обновлённая зелёная черепаха',
            'category' => 'Сухопутная',
            'full_description' => 'Новое подробное описание',
            'image_path' => 'images/Sea-turtle.jpg',
        ]);
    }

    public function test_update_rejects_duplicate_scientific_name_of_another_turtle(): void
    {
        $turtle = $this->createTurtle();
        $other = $this->createTurtle([
            'name' => 'Кожистая черепаха',
            'scientific_name' => 'Dermochelys coriacea',
        ]);

        $this->from(route('turtles.edit', $turtle))
            ->put(route('turtles.update', $turtle), $this->validData($turtle, [
                'scientific_name' => $other->scientific_name,
            ]))
            ->assertRedirect(route('turtles.edit', $turtle))
            ->assertSessionHasErrors('scientific_name');

        $this->assertSame('Chelonia mydas', $turtle->fresh()->scientific_name);
    }

    public function test_update_rejects_missing_required_fields(): void
    {
        $turtle = $this->createTurtle();

        $this->put(route('turtles.update', $turtle), [])
            ->assertSessionHasErrors([
                'name', 'scientific_name', 'category',
                'short_description', 'intro_description', 'full_description',
            ]);

        $this->assertSame('Зелёная черепаха', $turtle->fresh()->name);
    }

    public function test_uploaded_image_replaces_image_path_in_database(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('Включите GD для тестирования обработки изображений.');
        }

        $turtle = $this->createTurtle();
        Storage::fake('public');

        $image = new UploadedFile(
            base_path('storage/app/public/images/Sea-turtle.jpg'),
            'new-turtle.jpg',
            'image/jpeg',
            null,
            true
        );

        $this->post(route('turtles.update', $turtle), array_merge(
            $this->validData($turtle),
            ['_method' => 'PUT', 'image' => $image]
        ))->assertRedirect(route('turtles.show', $turtle))
            ->assertSessionHasNoErrors();

        $newImagePath = $turtle->fresh()->image_path;
        $this->assertNotSame('images/Sea-turtle.jpg', $newImagePath);
        Storage::disk('public')->assertExists($newImagePath);
    }

    public function test_soft_deleted_turtle_cannot_be_edited(): void
    {
        $turtle = $this->createTurtle();
        $turtle->delete();

        $this->get(route('turtles.edit', $turtle))->assertNotFound();
        $this->put(route('turtles.update', $turtle), $this->validData($turtle))
            ->assertNotFound();
    }
}
