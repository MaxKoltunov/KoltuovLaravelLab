<?php

namespace Tests\Feature;

use App\Models\Turtle;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurtlePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private function data(string $name = 'Чужая черепаха', string $scientificName = 'Testudo example'): array
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

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_admin_can_edit_soft_delete_restore_and_force_delete_any_turtle(): void
    {
        $owner = User::factory()->create();
        $turtle = $owner->turtles()->create($this->data());

        $this->actingAs($this->admin());
        $this->get(route('turtles.edit', $turtle))->assertOk();
        $this->put(route('turtles.update', $turtle), $this->data('Изменённая черепаха'))
            ->assertRedirect(route('turtles.show', $turtle));
        $this->assertDatabaseHas('turtles', ['id' => $turtle->id, 'user_id' => $owner->id, 'name' => 'Изменённая черепаха']);

        $this->delete(route('turtles.destroy', $turtle))->assertRedirect(route('turtles.index'));
        $this->assertSoftDeleted($turtle);

        $this->get(route('users.turtles', $owner))->assertOk()->assertSee('Изменённая черепаха')->assertSee('Удалена');
        $this->get(route('turtles.trash'))->assertOk()->assertSee('Изменённая черепаха');
        $this->get(route('turtles.show', $turtle))->assertOk()->assertSee('Восстановить')->assertSee('Удалить навсегда');

        $this->patch(route('turtles.restore', $turtle))->assertRedirect(route('turtles.show', $turtle));
        $this->assertNotSoftDeleted($turtle);

        $this->delete(route('turtles.force-delete', $turtle))->assertForbidden();
        $this->delete(route('turtles.destroy', $turtle))->assertRedirect(route('turtles.index'));
        $this->delete(route('turtles.force-delete', $turtle))->assertRedirect(route('turtles.trash'));
        $this->assertDatabaseMissing('turtles', ['id' => $turtle->id]);
    }

    public function test_ordinary_users_cannot_manage_trash_or_see_deleted_objects(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $turtle = $owner->turtles()->create($this->data());

        $this->actingAs($other)->get(route('turtles.show', $turtle))
            ->assertOk()->assertDontSee('Редактировать')->assertDontSee('Удалить черепаху');
        $this->actingAs($owner)->delete(route('turtles.destroy', $turtle))->assertRedirect(route('turtles.index'));
        $this->get(route('turtles.trash'))->assertForbidden();
        $this->get(route('turtles.show', $turtle))->assertNotFound();
        $this->get(route('users.turtles', $owner))->assertOk()->assertDontSee($turtle->name);
        $this->patch(route('turtles.restore', $turtle))->assertForbidden();
        $this->delete(route('turtles.force-delete', $turtle))->assertForbidden();
        $this->assertSoftDeleted($turtle);
    }

    public function test_model_event_blocks_direct_update_by_non_owner(): void
    {
        $owner = User::factory()->create();
        $turtle = $owner->turtles()->create($this->data());
        $this->actingAs(User::factory()->create());

        $this->expectException(AuthorizationException::class);

        $turtle->update(['name' => 'Запрещённое изменение']);
    }

    public function test_model_event_blocks_direct_delete_by_non_owner(): void
    {
        $owner = User::factory()->create();
        $turtle = $owner->turtles()->create($this->data());
        $this->actingAs(User::factory()->create());

        $this->expectException(AuthorizationException::class);

        $turtle->delete();
    }

    public function test_model_event_blocks_direct_restore_by_owner(): void
    {
        $owner = User::factory()->create();
        $turtle = $owner->turtles()->create($this->data());
        $turtle->delete();
        $this->actingAs($owner);

        $this->expectException(AuthorizationException::class);

        $turtle->restore();
    }

    public function test_model_event_blocks_direct_force_delete_by_owner(): void
    {
        $owner = User::factory()->create();
        $turtle = $owner->turtles()->create($this->data());
        $turtle->delete();
        $this->actingAs($owner);

        $this->expectException(AuthorizationException::class);

        $turtle->forceDelete();
    }

    public function test_restore_rejects_duplicate_scientific_name(): void
    {
        $owner = User::factory()->create();
        $turtle = $owner->turtles()->create($this->data());
        $turtle->delete();
        $owner->turtles()->create($this->data('Новая запись'));

        $this->actingAs($this->admin())->from(route('turtles.show', $turtle))
            ->patch(route('turtles.restore', $turtle))->assertSessionHasErrors('restore');

        $this->assertSoftDeleted($turtle);
    }
}
