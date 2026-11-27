<?php

namespace App\Http\Controllers;

use App\Models\Turtle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(Request $request): View
    {
        // Подзапрос получает ID друзей через связь belongsToMany.
        $friendIds = $request->user()->friends()->getQuery()->select('users.id');

        // SoftDeletes автоматически исключает удалённых черепах.
        // Сортируем именно по дате создания, а не по дате добавления в каталог.
        $turtles = Turtle::query()
            ->with('user')
            ->whereIn('user_id', $friendIds)
            ->where('user_id', '!=', $request->user()->id)
            ->latest('created_at')
            ->orderByDesc('id')
            ->get();

        return view('turtles.index', [
            'turtles' => $turtles,
            'feedOnly' => true,
        ]);
    }
}
