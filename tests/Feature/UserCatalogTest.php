<?php

namespace Tests\Feature;

use App\Models\Turtle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function data(string $name, string $scientificName): array
    {
        return [
            'name' => $name,
            'scientific_name' => $scientificName,
            'category' => 'Морская',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Введение',
            'full_description' => 'Подробное описание',
        ];
    }

    public function test_catalogs_only_show_turtles_of_selected_user(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $turtle = $first->turtles()->create($this->data('Первый вид', 'Testudo one'));
        $second->turtles()->create($this->data('Второй вид', 'Testudo two'));
        $deleted = $first->turtles()->create($this->data('Удалённый вид', 'Testudo deleted'));
        $deleted->delete();

        $this->assertTrue($turtle->user->is($first));
        $this->actingAs($first)->get(route('users.index'))
            ->assertOk()->assertSee(route('users.turtles', $second));
        $this->get(route('users.turtles', $first))
            ->assertOk()->assertSee('Первый вид')->assertDontSee('Второй вид')->assertDontSee('Удалённый вид');
        $this->get(route('users.turtles', $second))
            ->assertOk()->assertSee('Второй вид')->assertDontSee('Первый вид');
    }

    public function test_store_uses_authenticated_user_and_ignores_forged_owner(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();

        $this->actingAs($first)->post(route('turtles.store'), [
            ...$this->data('Новый вид', 'Testudo new'),
            'user_id' => $second->id,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('turtles', [
            'scientific_name' => 'Testudo new',
            'user_id' => $first->id,
        ]);
    }

    public function test_guests_cannot_create_or_browse_user_catalogs(): void
    {
        $user = User::factory()->create();
        $this->get(route('users.turtles', $user))->assertRedirect(route('login'));
        $this->get(route('turtles.create'))->assertRedirect(route('login'));
        $this->post(route('turtles.store'), $this->data('Новый вид', 'Testudo new'))
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('turtles', 0);
    }

    public function test_user_cannot_edit_or_delete_another_users_turtle(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $turtle = $owner->turtles()->create($this->data('Чужой вид', 'Testudo other'));

        $this->actingAs($other)->get(route('turtles.edit', $turtle))->assertForbidden();
        $this->put(route('turtles.update', $turtle), $this->data('Изменённый вид', 'Testudo other'))
            ->assertForbidden();
        $this->delete(route('turtles.destroy', $turtle))->assertForbidden();
        $this->assertDatabaseHas('turtles', [
            'id' => $turtle->id,
            'name' => 'Чужой вид',
            'deleted_at' => null,
        ]);
    }
}
