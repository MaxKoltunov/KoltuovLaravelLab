<?php

namespace App\Http\Controllers;

use App\Models\Turtle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        return view('turtles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Серверная валидация обязательна, даже если форма проверяется браузером.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'scientific_name' => [
                'required', 'string', 'max:255',
                Rule::unique('turtles', 'scientific_name')->withoutTrashed(),
            ],
            'category' => ['required', Rule::in(['Морская', 'Пресноводная', 'Сухопутная'])],
            'short_description' => ['required', 'string', 'max:2000'],
            'intro_description' => ['required', 'string', 'max:5000'],
            'full_description' => ['required', 'string', 'max:10000'],
            'catalogued_at' => ['nullable', 'date_format:Y-m-d'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'scientific_name.unique' => 'Черепаха с таким латинским названием уже существует.',
            'image.image' => 'Выберите файл изображения.',
            'image.mimes' => 'Допустимы только JPG, PNG и WebP.',
            'image.max' => 'Размер изображения не должен превышать 5 МБ.',
        ]);

        // Изображения хранятся в storage/app/public/images.
        // В базе сохраняется только относительный путь к файлу.
        unset($validated['image']);
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('images', 'public');
        }

        $turtle = Turtle::create($validated);

        return redirect()
            ->route('turtles.show', $turtle)
            ->with('success', 'Черепаха успешно добавлена в каталог.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Turtle $turtle)
    {
        // Route model binding автоматически найдёт черепаху по ID.
        // Удалённые через SoftDeletes записи вернут 404.
        return view('turtles.show', compact('turtle'));
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
