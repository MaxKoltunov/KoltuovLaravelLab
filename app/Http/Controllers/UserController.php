<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount('turtles')->orderBy('id')->get();

        return view('users.index', compact('users'));
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
