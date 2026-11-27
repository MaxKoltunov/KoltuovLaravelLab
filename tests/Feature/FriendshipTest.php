<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FriendshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_multiple_friends_without_duplicates(): void
    {
        $user = User::factory()->create();
        $first = User::factory()->create();
        $second = User::factory()->create();

        $this->actingAs($user)->post(route('friends.store', $first))
            ->assertRedirect(route('users.index'));
        $this->post(route('friends.store', $second))->assertRedirect(route('users.index'));
        $this->post(route('friends.store', $first))->assertRedirect(route('users.index'));

        $this->assertDatabaseCount('friendships', 2);
        $this->assertTrue($user->friends->contains($first));
        $this->assertTrue($user->friends->contains($second));
        $this->assertTrue($first->followers->contains($user));
    }

    public function test_user_can_remove_one_friend_without_removing_others(): void
    {
        $user = User::factory()->create();
        $first = User::factory()->create();
        $second = User::factory()->create();
        $user->friends()->attach([$first->id, $second->id]);

        $this->actingAs($user)->delete(route('friends.destroy', $first))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('friendships', ['user_id' => $user->id, 'friend_id' => $first->id]);
        $this->assertDatabaseHas('friendships', ['user_id' => $user->id, 'friend_id' => $second->id]);
    }

    public function test_friends_page_shows_only_current_users_friends(): void
    {
        $user = User::factory()->create();
        $friend = User::factory()->create(['name' => 'Selected Friend']);
        $stranger = User::factory()->create(['name' => 'Unrelated Person']);
        $user->friends()->attach($friend->id);

        $this->actingAs($user)->get(route('friends.index'))
            ->assertOk()->assertSee('Selected Friend')->assertDontSee('Unrelated Person')
            ->assertSee('Удалить из друзей');

        $this->get(route('users.index'))->assertOk()
            ->assertSee('Добавить в друзья')->assertSee('Удалить из друзей');
    }

    public function test_self_friendship_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('friends.store', $user))->assertForbidden();
        $this->delete(route('friends.destroy', $user))->assertForbidden();
        $this->get(route('users.index'))->assertOk()->assertDontSee('Добавить в друзья');

        $this->assertDatabaseCount('friendships', 0);
    }

    public function test_guests_cannot_manage_friendships(): void
    {
        $user = User::factory()->create();

        $this->get(route('friends.index'))->assertRedirect(route('login'));
        $this->post(route('friends.store', $user))->assertRedirect(route('login'));
        $this->delete(route('friends.destroy', $user))->assertRedirect(route('login'));
        $this->assertDatabaseCount('friendships', 0);
    }

    public function test_request_cannot_forge_source_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $friend = User::factory()->create();

        $this->actingAs($user)->post(route('friends.store', $friend), [
            'user_id' => $other->id,
            'friend_id' => $other->id,
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('friendships', ['user_id' => $user->id, 'friend_id' => $friend->id]);
        $this->assertDatabaseMissing('friendships', ['user_id' => $other->id]);
    }

    public function test_deleting_user_removes_incoming_and_outgoing_friendships(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $third = User::factory()->create();
        $first->friends()->attach($second->id);
        $third->friends()->attach($first->id);
        $second->friends()->attach($third->id);

        $first->delete();

        $this->assertDatabaseCount('friendships', 1);
        $this->assertDatabaseHas('friendships', ['user_id' => $second->id, 'friend_id' => $third->id]);
    }

    public function test_database_rejects_duplicate_friendship(): void
    {
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $user->friends()->attach($friend->id);

        $this->expectException(QueryException::class);

        DB::table('friendships')->insert(['user_id' => $user->id, 'friend_id' => $friend->id]);
    }
}
