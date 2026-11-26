<?php

namespace App\Http\Controllers;

use App\Http\Requests\TurtleRequest;
use App\Models\Turtle;
use App\Services\TurtleImageService;
use Illuminate\Support\Facades\Gate;

class TurtleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Получаем все неудалённые записи из SQLite
        $turtles = Turtle::with('user')->orderBy('id')->get();

        // Передаём данные в Blade-представление
        return view('turtles.index', compact('turtles'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('turtles.create');

        return view('turtles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TurtleRequest $request, TurtleImageService $images)
    {
        Gate::authorize('turtles.create');

        // Правила валидации находятся в TurtleRequest.
        $validated = $request->validated();

        // Перед сохранением файл уменьшается на сервере. В БД остаётся только путь.
        unset($validated['image']);
        if ($request->hasFile('image')) {
            $validated['image_path'] = $images->store($request->file('image'));
        }

        // Владелец берётся из сессии, а не из данных формы.
        $turtle = $request->user()->turtles()->create($validated);

        return redirect()
            ->route('turtles.show', $turtle)
            ->with('success', 'Черепаха успешно добавлена в каталог.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Turtle $turtle)
    {
        // Удалённые записи доступны только администратору.
        if ($turtle->trashed()) {
            abort_unless(Gate::allows('turtles.view-deleted'), 404);
        }

        $turtle->load('user');

        return view('turtles.show', compact('turtle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Turtle $turtle)
    {
        Gate::authorize('turtles.update', $turtle);

        return view('turtles.edit', compact('turtle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TurtleRequest $request, Turtle $turtle, TurtleImageService $images)
    {
        Gate::authorize('turtles.update', $turtle);

        $validated = $request->validated();

        unset($validated['image']);

        if ($request->hasFile('image')) {
            // Новая фотография уменьшается перед сохранением, затем обновляется путь.
            // Старый файл пока оставляем: исходные изображения могут
            // использоваться сидером, а запись может понадобиться восстановить.
            $validated['image_path'] = $images->store($request->file('image'));
        }

        // Без нового файла старое изображение остаётся неизменным.
        $turtle->update($validated);

        return redirect()
            ->route('turtles.show', $turtle)
            ->with('success', 'Данные черепахи успешно обновлены.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turtle $turtle)
    {
        Gate::authorize('turtles.delete', $turtle);

        // Мягкое удаление: Laravel заполняет deleted_at, но не уничтожает запись.
        // Загруженное изображение оставляем на диске для возможного восстановления.
        $turtle->delete();

        return redirect()
            ->route('turtles.index')
            ->with('success', 'Черепаха удалена из каталога.');
    }

    // Общая корзина администратора.
    public function trash()
    {
        Gate::authorize('turtles.view-deleted');

        $turtles = Turtle::onlyTrashed()->with('user')->orderBy('id')->get();

        return view('turtles.index', [
            'turtles' => $turtles,
            'trashOnly' => true,
        ]);
    }

    public function restore(Turtle $turtle)
    {
        Gate::authorize('turtles.restore', $turtle);

        $turtle->restore();

        return redirect()->route('turtles.show', $turtle)
            ->with('success', 'Черепаха восстановлена.');
    }

    public function forceDelete(Turtle $turtle)
    {
        Gate::authorize('turtles.force-delete', $turtle);

        // Удаляем строку без возможности восстановления. Общие файлы
        // изображений оставляем: они могут использоваться другими записями.
        $turtle->forceDelete();

        return redirect()->route('turtles.trash')
            ->with('success', 'Черепаха удалена окончательно.');
    }
}
