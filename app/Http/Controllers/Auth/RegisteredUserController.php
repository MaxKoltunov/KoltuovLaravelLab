<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-z][a-z0-9_]*\z/', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'regex:/@[^@\s]+\.[^@.\s]+\z/u', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'username.regex' => 'Имя пользователя должно начинаться с латинской буквы и содержать только строчные латинские буквы, цифры и подчёркивания.',
            'username.unique' => 'Это имя пользователя уже занято.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.regex' => 'Введите полный адрес электронной почты, например name@gmail.com.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('turtles.index', absolute: false));
    }
}
