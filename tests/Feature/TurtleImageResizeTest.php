<?php

namespace Tests\Feature;

use App\Models\Turtle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TurtleImageResizeTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $extra = []): array
    {
        return array_merge([
            'name' => 'Тестовая черепаха',
            'scientific_name' => 'Testudo resize',
            'category' => 'Сухопутная',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Вводное описание',
            'full_description' => 'Подробное описание',
        ], $extra);
    }

    public function test_large_image_is_resized_without_distortion_on_create(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('Для обработки изображений требуется расширение GD.');
        }

        Storage::fake('public');

        $this->post(route('turtles.store'), $this->payload([
            'image' => UploadedFile::fake()->image('large.png', 2400, 1800),
        ]))->assertSessionHasNoErrors();

        $turtle = Turtle::where('scientific_name', 'Testudo resize')->firstOrFail();
        Storage::disk('public')->assertExists($turtle->image_path);

        $filePath = Storage::disk('public')->path($turtle->image_path);
        [$width, $height] = getimagesize($filePath);

        $this->assertSame(1200, $width);
        $this->assertSame(900, $height);
        $this->assertStringEndsWith('.png', $turtle->image_path);
    }

    public function test_small_image_is_not_upscaled(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('Для обработки изображений требуется расширение GD.');
        }

        Storage::fake('public');

        $this->post(route('turtles.store'), $this->payload([
            'image' => UploadedFile::fake()->image('small.png', 300, 200),
        ]))->assertSessionHasNoErrors();

        $turtle = Turtle::where('scientific_name', 'Testudo resize')->firstOrFail();
        [$width, $height] = getimagesize(Storage::disk('public')->path($turtle->image_path));

        $this->assertSame(300, $width);
        $this->assertSame(200, $height);
    }

    public function test_uploaded_image_is_resized_on_update(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('Для обработки изображений требуется расширение GD.');
        }

        Storage::fake('public');

        $turtle = Turtle::create($this->payload([
            'image_path' => 'images/old-image.jpg',
        ]));

        $this->post(route('turtles.update', $turtle), array_merge(
            $this->payload(),
            [
                '_method' => 'PUT',
                'image' => UploadedFile::fake()->image('large.png', 2400, 1600),
            ]
        ))->assertSessionHasNoErrors();

        $turtle->refresh();
        Storage::disk('public')->assertExists($turtle->image_path);
        [$width, $height] = getimagesize(Storage::disk('public')->path($turtle->image_path));

        $this->assertSame(1200, $width);
        $this->assertSame(800, $height);
        $this->assertNotSame('images/old-image.jpg', $turtle->image_path);
    }
}
