<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurtleController;

// Главная страница
Route::get('/', [TurtleController::class, 'index']);

// Маршрут списка черепах
Route::resource('turtles', TurtleController::class)
    ->only(['index', 'create', 'store', 'show']);
