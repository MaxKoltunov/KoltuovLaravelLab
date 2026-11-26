<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    // Владелец записи в каталоге.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Преобразование типов данных
    protected function casts(): array
    {
        return [
            'catalogued_at' => 'date',
            'user_id' => 'integer',
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
