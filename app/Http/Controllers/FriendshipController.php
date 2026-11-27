<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FriendshipController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('friends.manage', $user);

        // Не удаляем других друзей и не создаём повторную связь.
        $request->user()->friends()->syncWithoutDetaching([$user->id]);

        return redirect()->route('users.index')
            ->with('success', 'Пользователь добавлен в друзья.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('friends.manage', $user);

        // Удаляется только связь текущего пользователя с выбранным другом.
        $request->user()->friends()->detach($user->id);

        return redirect()->route('users.index')
            ->with('success', 'Пользователь удалён из друзей.');
    }
}
