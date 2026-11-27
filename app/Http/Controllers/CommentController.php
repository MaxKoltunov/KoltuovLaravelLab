<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Turtle;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Turtle $turtle): RedirectResponse
    {
        // Валидация и проверка прав выполнены в CommentRequest.
        $comment = new Comment($request->validated());

        // Автор определяется сессией, объект — параметром маршрута.
        $comment->user()->associate($request->user());
        $turtle->comments()->save($comment);

        return redirect()
            ->to(route('turtles.show', $turtle).'#comments')
            ->with('success', 'Комментарий добавлен.');
    }
}
