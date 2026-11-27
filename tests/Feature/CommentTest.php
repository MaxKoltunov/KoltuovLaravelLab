<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Turtle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private function turtle(User $owner, string $scientificName = 'Chelonia mydas'): Turtle
    {
        return $owner->turtles()->create([
            'name' => 'Зелёная черепаха',
            'scientific_name' => $scientificName,
            'category' => 'Морская',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Введение',
            'full_description' => 'Подробное описание',
        ]);
    }

    public function test_authenticated_users_can_comment_on_own_and_other_users_turtles(): void
    {
        $owner = User::factory()->create();
        $author = User::factory()->create();
        $turtle = $this->turtle($owner);

        $this->actingAs($owner)->post(route('comments.store', $turtle), [
            'text' => 'Комментарий владельца',
        ])->assertRedirect(route('turtles.show', $turtle).'#comments')->assertSessionHas('success');

        $this->actingAs($author)->post(route('comments.store', $turtle), [
            'text' => 'Комментарий другого пользователя',
        ])->assertRedirect(route('turtles.show', $turtle).'#comments');

        $this->assertDatabaseHas('comments', [
            'user_id' => $owner->id,
            'turtle_id' => $turtle->id,
            'text' => 'Комментарий владельца',
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $author->id,
            'turtle_id' => $turtle->id,
            'text' => 'Комментарий другого пользователя',
        ]);

        $this->get(route('turtles.show', $turtle))
            ->assertOk()
            ->assertSeeInOrder(['Комментарий владельца', 'Комментарий другого пользователя'])
            ->assertSee($author->name)
            ->assertSee('@'.$author->username)
            ->assertSee('Отправить комментарий');
    }

    public function test_guests_can_read_but_cannot_submit_comments(): void
    {
        $owner = User::factory()->create();
        $turtle = $this->turtle($owner);
        $comment = new Comment(['text' => 'Публичный комментарий']);
        $comment->user()->associate($owner);
        $turtle->comments()->save($comment);

        $this->get(route('turtles.show', $turtle))
            ->assertOk()
            ->assertSee('Публичный комментарий')
            ->assertDontSee('Отправить комментарий');

        $this->post(route('comments.store', $turtle), ['text' => 'Гость'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_author_and_turtle_cannot_be_forged_in_request(): void
    {
        $owner = User::factory()->create();
        $author = User::factory()->create();
        $turtle = $this->turtle($owner);
        $otherTurtle = $this->turtle($owner, 'Testudo graeca');

        $this->actingAs($author)->post(route('comments.store', $turtle), [
            'text' => 'Новый комментарий',
            'user_id' => $owner->id,
            'turtle_id' => $otherTurtle->id,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'user_id' => $author->id,
            'turtle_id' => $turtle->id,
            'text' => 'Новый комментарий',
        ]);
        $this->assertDatabaseCount('comments', 1);
    }

    public function test_comment_text_is_validated(): void
    {
        $owner = User::factory()->create();
        $turtle = $this->turtle($owner);
        $this->actingAs($owner);

        foreach (['', '   ', ['not text'], str_repeat('a', 2001)] as $text) {
            $this->from(route('turtles.show', $turtle))
                ->post(route('comments.store', $turtle), ['text' => $text])
                ->assertRedirect(route('turtles.show', $turtle))
                ->assertSessionHasErrors('text');
        }

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_html_is_escaped(): void
    {
        $owner = User::factory()->create();
        $turtle = $this->turtle($owner);
        $text = '<script>alert("xss")</script>';

        $this->actingAs($owner)->post(route('comments.store', $turtle), ['text' => $text])
            ->assertSessionHasNoErrors();

        $this->get(route('turtles.show', $turtle))
            ->assertOk()->assertSee($text)->assertDontSee($text, false);
    }

    public function test_deleted_turtle_does_not_accept_comments_even_from_admin(): void
    {
        $owner = User::factory()->create();
        $turtle = $this->turtle($owner);
        $turtle->delete();
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $this->actingAs($admin)->get(route('turtles.show', $turtle))
            ->assertOk()->assertDontSee('Отправить комментарий');

        $this->post(route('comments.store', $turtle), ['text' => 'Нельзя добавить'])
            ->assertNotFound();

        $this->assertDatabaseCount('comments', 0);
    }
}
