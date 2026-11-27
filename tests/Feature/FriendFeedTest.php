<?php

namespace Tests\Feature;

use App\Models\Turtle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendFeedTest extends TestCase
{
    use RefreshDatabase;

    private function turtle(User $owner, string $name, string $createdAt, string $cataloguedAt = '2026-11-01'): Turtle
    {
        $turtle = new Turtle([
            'name' => $name,
            'scientific_name' => 'Testudo '.$name,
            'category' => 'Морская',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Введение',
            'full_description' => 'Подробное описание',
            'catalogued_at' => $cataloguedAt,
        ]);
        $turtle->created_at = $createdAt;

        return $owner->turtles()->save($turtle);
    }

    public function test_feed_contains_only_active_friend_objects_ordered_by_creation_date(): void
    {
        $viewer = User::factory()->create();
        $first = User::factory()->create();
        $second = User::factory()->create();
        $stranger = User::factory()->create();
        $viewer->friends()->attach([$first->id, $second->id]);

        $new = $this->turtle($first, 'Новая запись друга', '2026-11-25 14:00:00', '2026-11-01');
        $old = $this->turtle($second, 'Старая запись друга', '2026-11-20 14:00:00', '2026-11-26');
        $this->turtle($viewer, 'Собственная запись', '2026-11-26 14:00:00');
        $this->turtle($stranger, 'Запись незнакомца', '2026-11-26 15:00:00');
        $deleted = $this->turtle($first, 'Удалённая запись друга', '2026-11-26 16:00:00');
        $deleted->delete();

        $this->actingAs($viewer)->get(route('feed.index'))
            ->assertOk()
            ->assertViewHas('turtles', fn ($turtles) => $turtles->modelKeys() === [$new->id, $old->id])
            ->assertSeeInOrder(['Новая запись друга', 'Старая запись друга'])
            ->assertSee('25.11.2026 14:00')
            ->assertDontSee('Собственная запись')
            ->assertDontSee('Запись незнакомца')
            ->assertDontSee('Удалённая запись друга');
    }

    public function test_equal_creation_dates_are_ordered_by_descending_id(): void
    {
        $viewer = User::factory()->create();
        $friend = User::factory()->create();
        $viewer->friends()->attach($friend->id);
        $first = $this->turtle($friend, 'Первая запись', '2026-11-25 14:00:00');
        $second = $this->turtle($friend, 'Вторая запись', '2026-11-25 14:00:00');

        $this->actingAs($viewer)->get(route('feed.index'))
            ->assertOk()
            ->assertViewHas('turtles', fn ($turtles) => $turtles->modelKeys() === [$second->id, $first->id]);
    }

    public function test_feed_changes_after_adding_and_removing_friend(): void
    {
        $viewer = User::factory()->create();
        $friend = User::factory()->create();
        $this->turtle($friend, 'Запись будущего друга', '2026-11-25 14:00:00');

        $this->actingAs($viewer)->get(route('feed.index'))
            ->assertOk()->assertSee('В ленте пока нет черепах друзей.')
            ->assertDontSee('Запись будущего друга');

        $this->post(route('friends.store', $friend))->assertRedirect(route('users.index'));
        $this->get(route('feed.index'))->assertOk()->assertSee('Запись будущего друга');

        $this->delete(route('friends.destroy', $friend))->assertRedirect(route('users.index'));
        $this->get(route('feed.index'))->assertOk()->assertDontSee('Запись будущего друга');
    }

    public function test_admin_feed_also_excludes_deleted_turtles(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        $friend = User::factory()->create();
        $admin->friends()->attach($friend->id);

        $turtle = $this->turtle($friend, 'Удалённый объект', '2026-11-25 14:00:00');
        $turtle->delete();

        $this->actingAs($admin)->get(route('feed.index'))
            ->assertOk()->assertDontSee('Удалённый объект');
    }

    public function test_guests_cannot_open_feed(): void
    {
        $this->get(route('feed.index'))->assertRedirect(route('login'));
    }
}
