<?php

namespace App\Http\Controllers;

use App\Events\FriendshipAdded;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class FriendshipController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('friends.manage', $user);

        $currentUser = $request->user();

        DB::transaction(function () use ($currentUser, $user): void {
            $currentUser->friends()->syncWithoutDetaching([$user->id]);

            // Синхронный listener создаёт обратную связь в той же транзакции.
            // Событие вызывается и повторно, чтобы восстановить недостающую связь.
            FriendshipAdded::dispatch($currentUser, $user);
        });

        return redirect()->route('users.index')
            ->with('success', 'Пользователь добавлен в друзья.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('friends.manage', $user);

        $currentUser = $request->user();

        // При прекращении дружбы удаляем обе связи, сохраняя остальных друзей.
        DB::transaction(function () use ($currentUser, $user): void {
            $currentUser->friends()->detach($user->id);
            $user->friends()->detach($currentUser->id);
        });

        return redirect()->route('users.index')
            ->with('success', 'Пользователь удалён из друзей.');
    }
}
