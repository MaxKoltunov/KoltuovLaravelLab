<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $turtles = $user->turtles()->with('user')->orderBy('id')->get();

        return view('turtles.index', [
            'turtles' => $turtles,
            'catalogUser' => $user,
        ]);
    }
}
