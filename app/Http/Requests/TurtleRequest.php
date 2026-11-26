<?php

namespace App\Http\Requests;

use App\Models\Turtle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TurtleRequest extends FormRequest
{
    // Проверяем доступ до валидации и обработки загруженных файлов.
    public function authorize(): bool
    {
        $turtle = $this->route('turtle');

        return $turtle instanceof Turtle
            ? Gate::allows('turtles.update', $turtle)
            : Gate::allows('turtles.create');
    }

    public function rules(): array
    {
        $scientificName = Rule::unique('turtles', 'scientific_name')->withoutTrashed();

        // При редактировании собственное название не считается дубликатом.
        $turtle = $this->route('turtle');
        if ($turtle instanceof Turtle) {
            $scientificName->ignore($turtle->getKey());
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'scientific_name' => [
                'required', 'string', 'max:255',
                $scientificName,
            ],
            'category' => ['required', Rule::in(['Морская', 'Пресноводная', 'Сухопутная'])],
            'short_description' => ['required', 'string', 'max:2000'],
            'intro_description' => ['required', 'string', 'max:5000'],
            'full_description' => ['required', 'string', 'max:10000'],
            'catalogued_at' => ['nullable', 'date_format:Y-m-d'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'scientific_name.unique' => 'Черепаха с таким латинским названием уже существует.',
            'image.image' => 'Выберите файл изображения.',
            'image.mimes' => 'Допустимы только JPG, PNG и WebP.',
            'image.max' => 'Размер изображения не должен превышать 5 МБ.',
        ];
    }
}
