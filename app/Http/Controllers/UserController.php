<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::withCount('turtles')->orderBy('id')->get();
        $friendIds = $request->user()->friends()->pluck('users.id')->all();

        return view('users.index', [
            'users' => $users,
            'friendIds' => $friendIds,
            'friendsOnly' => false,
        ]);
    }

    public function friends(Request $request): View
    {
        $users = $request->user()->friends()->withCount('turtles')->orderBy('users.id')->get();

        return view('users.index', [
            'users' => $users,
            'friendIds' => $users->modelKeys(),
            'friendsOnly' => true,
        ]);
    }

    public function turtles(User $user): View
    {
        $query = $user->turtles()->with('user')->orderBy('id');

        if (Gate::allows('turtles.view-deleted')) {
            $query->withTrashed();
        }

        $turtles = $query->get();

        return view('turtles.index', [
            'turtles' => $turtles,
            'catalogUser' => $user,
        ]);
    }
}
