<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

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

    // Защита операций над экземпляром модели независимо от контроллера.
    protected static function booted(): void
    {
        static::creating(function (Turtle $turtle): void {
            if (self::trustedConsole()) {
                return;
            }

            Gate::authorize('turtles.create');
            // Даже при прямом создании модели нельзя назначить другого владельца.
            if ($turtle->user_id === null) {
                $turtle->user_id = auth()->id();
            }
            abort_unless($turtle->user_id === auth()->id() && ! $turtle->trashed(), 403);
        });

        static::updating(function (Turtle $turtle): void {
            if (! self::trustedConsole()) {
                // Владелец не меняется через пользовательские операции.
                abort_if($turtle->isDirty('user_id'), 403);

                if ($turtle->isDirty('deleted_at')) {
                    // SoftDeletes::restore() вызывает save() после обнуления deleted_at.
                    // Мягкое удаление допустимо только через delete().
                    abort_unless($turtle->getRawOriginal('deleted_at') !== null && ! $turtle->trashed(), 403);
                    Gate::authorize('turtles.restore', $turtle);
                    abort_if(array_diff(array_keys($turtle->getDirty()), ['deleted_at', 'updated_at']) !== [], 403);
                } else {
                    Gate::authorize('turtles.update', $turtle);
                }
            }

            // Проверяем уникальность при восстановлении, даже если вызван save().
            if ($turtle->isDirty('deleted_at')
                && $turtle->getRawOriginal('deleted_at') !== null
                && ! $turtle->trashed()) {
                $turtle->ensureRestorable();
            }
        });

        static::deleting(function (Turtle $turtle): void {
            if (! self::trustedConsole()) {
                // Не доверяем изменённому в памяти владельцу или статусу удаления.
                abort_if($turtle->isDirty(['user_id', 'deleted_at']), 403);

                Gate::authorize(
                    $turtle->isForceDeleting() ? 'turtles.force-delete' : 'turtles.delete',
                    $turtle,
                );
            }
        });

        static::restoring(function (Turtle $turtle): void {
            if (! self::trustedConsole()) {
                Gate::authorize('turtles.restore', $turtle);
            }

            $turtle->ensureRestorable();
        });
    }

    // Tinker, сидеры и другие доверенные CLI-операции без веб-пользователя.
    // Для HTTP-запросов без пользователя действуют обычные Gates.
    private static function trustedConsole(): bool
    {
        return app()->runningInConsole() && auth()->guest();
    }

    private function ensureRestorable(): void
    {
        if (self::query()->where('scientific_name', $this->scientific_name)
            ->where('id', '!=', $this->getKey())->exists()) {
            throw ValidationException::withMessages([
                'restore' => 'В каталоге уже есть черепаха с таким латинским названием. Сначала устраните дубликат.',
            ]);
        }
    }

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
