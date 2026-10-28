<?php

namespace App\Http\Controllers;

use App\Models\Turtle;
use Illuminate\Http\Request;

class TurtleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Получаем все неудалённые записи из SQLite
        $turtles = Turtle::orderBy('id')->get();

        // Передаём данные в Blade-представление
        return view('welcome', compact('turtles'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Turtle $turtle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Turtle $turtle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Turtle $turtle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turtle $turtle)
    {
        //
    }
}
