<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Turtle extends Model
{
    use SoftDeletes;

    // Поля, разрешённые для массового заполнения
    protected $fillable = [
        'name',
        'scientific_name',
        'category',
        'short_description',
        'intro_description',
        'full_description',
        'image_path',
        'catalogued_at',
    ];

    // Преобразование типов данных
    protected function casts(): array
    {
        return [
            'catalogued_at' => 'date',
        ];
    }

    // Мутатор для даты добавления в каталог
    protected function cataloguedAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value === null || $value === ''
                ? null
                : Carbon::parse($value)->toDateString(),
        );
    }
}
