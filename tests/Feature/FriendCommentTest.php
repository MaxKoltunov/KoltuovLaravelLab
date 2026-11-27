<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Turtle;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendCommentTest extends TestCase
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

    private function comment(Turtle $turtle, User $author): Comment
    {
        $comment = new Comment(['text' => 'Комментарий '.$author->username]);
        $comment->user()->associate($author);

        return $turtle->comments()->save($comment);
    }

    private function assertHighlighted(string $html, Comment $comment, bool $expected): void
    {
        $document = new DOMDocument();
        $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $article = $xpath->query('//article[@id="comment-'.$comment->id.'"]')->item(0);

        $this->assertNotNull($article);
        $classes = explode(' ', $article->getAttribute('class'));
        $this->assertSame($expected, in_array('friend-comment', $classes, true));
        $this->assertSame($expected, in_array('bg-success-subtle', $classes, true));
        $this->assertSame($expected, $xpath->query('.//i[contains(@class, "fa-user-check")]', $article)->length > 0);
    }

    public function test_only_friends_comments_are_highlighted_on_own_and_other_objects(): void
    {
        $viewer = User::factory()->create();
        $friend = User::factory()->create();
        $stranger = User::factory()->create();
        $viewer->friends()->attach($friend->id);

        $objects = [
            $this->turtle($viewer),
            $this->turtle($stranger, 'Testudo graeca'),
        ];

        foreach ($objects as $turtle) {
            $friendComment = $this->comment($turtle, $friend);
            $strangerComment = $this->comment($turtle, $stranger);
            $ownComment = $this->comment($turtle, $viewer);

            $response = $this->actingAs($viewer)->get(route('turtles.show', $turtle));
            $response->assertOk();
            $html = $response->getContent();

            $this->assertHighlighted($html, $friendComment, true);
            $this->assertHighlighted($html, $strangerComment, false);
            $this->assertHighlighted($html, $ownComment, false);
        }
    }

    public function test_highlight_changes_after_adding_and_removing_friend(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create();
        $turtle = $this->turtle($author);
        $comment = $this->comment($turtle, $author);

        $response = $this->actingAs($viewer)->get(route('turtles.show', $turtle))->assertOk();
        $this->assertHighlighted($response->getContent(), $comment, false);

        $this->post(route('friends.store', $author))->assertRedirect(route('users.index'));
        $response = $this->get(route('turtles.show', $turtle))->assertOk();
        $this->assertHighlighted($response->getContent(), $comment, true);

        $this->delete(route('friends.destroy', $author))->assertRedirect(route('users.index'));
        $response = $this->get(route('turtles.show', $turtle))->assertOk();
        $this->assertHighlighted($response->getContent(), $comment, false);
    }

    public function test_guests_do_not_see_friend_highlights(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create();
        $viewer->friends()->attach($author->id);
        $turtle = $this->turtle($viewer);
        $comment = $this->comment($turtle, $author);

        $response = $this->get(route('turtles.show', $turtle))->assertOk();
        $this->assertHighlighted($response->getContent(), $comment, false);
    }

    public function test_highlighting_depends_on_current_viewer(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $author = User::factory()->create();
        $first->friends()->attach($author->id);
        $turtle = $this->turtle($second);
        $comment = $this->comment($turtle, $author);

        $response = $this->actingAs($first)->get(route('turtles.show', $turtle))->assertOk();
        $this->assertHighlighted($response->getContent(), $comment, true);

        $response = $this->actingAs($second)->get(route('turtles.show', $turtle))->assertOk();
        $this->assertHighlighted($response->getContent(), $comment, false);
    }
}
