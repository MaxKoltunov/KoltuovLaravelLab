<?php

use App\Http\Controllers\TurtleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Общий каталог остаётся доступен посетителям.
Route::get('/', [TurtleController::class, 'index']);

Route::resource('turtles', TurtleController::class)
    ->only(['index']);

Route::middleware('auth')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/turtles', [UserController::class, 'turtles'])->name('users.turtles');

    // Статический /turtles/create должен быть объявлен до /turtles/{turtle}.
    Route::resource('turtles', TurtleController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});

Route::resource('turtles', TurtleController::class)
    ->only(['show']);

require __DIR__.'/auth.php';
