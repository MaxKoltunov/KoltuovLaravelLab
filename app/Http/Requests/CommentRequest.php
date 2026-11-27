<?php

namespace App\Http\Requests;

use App\Models\Turtle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $turtle = $this->route('turtle');

        return $turtle instanceof Turtle && Gate::allows('comments.create', $turtle);
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'text.required' => 'Введите текст комментария.',
            'text.string' => 'Комментарий должен быть текстом.',
            'text.max' => 'Комментарий не должен превышать 2000 символов.',
        ];
    }
}
