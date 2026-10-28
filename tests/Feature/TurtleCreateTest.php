<?php

namespace Tests\Feature;

use App\Models\Turtle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TurtleCreateTest extends TestCase
{
    use RefreshDatabase;

    private function validData(): array
    {
        return [
            'name' => 'Греческая черепаха',
            'scientific_name' => 'Testudo graeca',
            'category' => 'Сухопутная',
            'short_description' => 'Краткое описание греческой черепахи.',
            'intro_description' => 'Вводное описание греческой черепахи.',
            'full_description' => 'Подробное описание греческой черепахи.',
            'catalogued_at' => '2026-10-28',
        ];
    }

    public function test_create_form_is_available(): void
    {
        $this->get(route('turtles.create'))
            ->assertOk()
            ->assertSee('Добавить черепаху')
            ->assertSee('name="scientific_name"', false)
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_turtle_can_be_created_and_viewed(): void
    {
        $response = $this->post(route('turtles.store'), $this->validData());

        $turtle = Turtle::where('scientific_name', 'Testudo graeca')->firstOrFail();

        $response->assertRedirect(route('turtles.show', $turtle))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('turtles', [
            'name' => 'Греческая черепаха',
            'scientific_name' => 'Testudo graeca',
        ]);

        $this->get(route('turtles.show', $turtle))
            ->assertOk()
            ->assertSee('Подробное описание греческой черепахи.');
    }

    public function test_required_fields_are_validated(): void
    {
        $this->from(route('turtles.create'))
            ->post(route('turtles.store'), [])
            ->assertRedirect(route('turtles.create'))
            ->assertSessionHasErrors([
                'name', 'scientific_name', 'category',
                'short_description', 'intro_description', 'full_description',
            ]);

        $this->assertDatabaseCount('turtles', 0);
    }

    public function test_scientific_name_cannot_be_duplicated(): void
    {
        Turtle::create($this->validData());

        $this->post(route('turtles.store'), $this->validData())
            ->assertSessionHasErrors('scientific_name');

        $this->assertDatabaseCount('turtles', 1);
    }

    public function test_valid_uploaded_image_is_saved_to_public_storage(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('Включите GD для тестирования обработки изображений.');
        }

        Storage::fake('public');

        // Используем реальный JPEG из репозитория; GD не требуется.
        $image = new UploadedFile(
            base_path('storage/app/public/images/Sea-turtle.jpg'),
            'example.jpg',
            'image/jpeg',
            null,
            true
        );

        $this->post(route('turtles.store'), array_merge($this->validData(), [
            'image' => $image,
        ]))->assertSessionHasNoErrors();

        $turtle = Turtle::where('scientific_name', 'Testudo graeca')->firstOrFail();
        $this->assertNotNull($turtle->image_path);
        Storage::disk('public')->assertExists($turtle->image_path);
    }

    public function test_non_image_file_is_rejected(): void
    {
        $this->post(route('turtles.store'), array_merge($this->validData(), [
            'image' => UploadedFile::fake()->create('notes.txt', 1, 'text/plain'),
        ]))->assertSessionHasErrors('image');

        $this->assertDatabaseCount('turtles', 0);
    }
}
