<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Turtle;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommentRelationsTest extends TestCase
{
    use RefreshDatabase;

    private function turtle(User $owner): Turtle
    {
        return $owner->turtles()->create([
            'name' => 'Зелёная черепаха',
            'scientific_name' => 'Chelonia mydas',
            'category' => 'Морская',
            'short_description' => 'Краткое описание',
            'intro_description' => 'Введение',
            'full_description' => 'Подробное описание',
        ]);
    }

    private function comment(Turtle $turtle, User $author): Comment
    {
        $comment = new Comment(['text' => 'Интересный вид!']);
        $comment->user()->associate($author);

        return $turtle->comments()->save($comment);
    }

    public function test_comments_are_related_to_turtle_and_author_independently_of_owner(): void
    {
        $owner = User::factory()->create();
        $author = User::factory()->create();
        $turtle = $this->turtle($owner);
        $comment = $this->comment($turtle, $author);

        $this->assertTrue($comment->turtle->is($turtle));
        $this->assertTrue($comment->user->is($author));
        $this->assertTrue($turtle->comments->contains($comment));
        $this->assertTrue($author->comments->contains($comment));
        $this->assertCount(0, $owner->comments);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'turtle_id' => $turtle->id,
            'user_id' => $author->id,
            'text' => 'Интересный вид!',
        ]);
    }

    public function test_comment_survives_soft_delete_and_restore_but_not_force_delete(): void
    {
        $owner = User::factory()->create();
        $turtle = $this->turtle($owner);
        $comment = $this->comment($turtle, $owner);

        $turtle->delete();
        $this->assertModelExists($comment);
        $this->assertTrue($comment->fresh()->turtle->trashed());

        $turtle->restore();
        $this->assertTrue($turtle->fresh()->comments->contains($comment));

        $turtle->delete();
        $turtle->forceDelete();
        $this->assertModelMissing($comment);
    }

    public function test_deleting_author_removes_comments_but_preserves_another_users_turtle(): void
    {
        $owner = User::factory()->create();
        $author = User::factory()->create();
        $turtle = $this->turtle($owner);
        $comment = $this->comment($turtle, $author);

        $author->delete();

        $this->assertModelMissing($comment);
        $this->assertModelExists($turtle);
    }

    public function test_foreign_keys_reject_nonexistent_turtle(): void
    {
        $author = User::factory()->create();

        $this->expectException(QueryException::class);

        DB::table('comments')->insert([
            'turtle_id' => 999999,
            'user_id' => $author->id,
            'text' => 'Некорректная ссылка',
        ]);
    }

    public function test_foreign_keys_reject_nonexistent_author(): void
    {
        $turtle = $this->turtle(User::factory()->create());

        $this->expectException(QueryException::class);

        DB::table('comments')->insert([
            'turtle_id' => $turtle->id,
            'user_id' => 999999,
            'text' => 'Некорректная ссылка',
        ]);
    }
}
