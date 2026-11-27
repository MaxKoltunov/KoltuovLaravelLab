<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Route model binding ищет пользователя по username, а не по id.
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    // Черепахи, добавленные этим пользователем.
    public function turtles(): HasMany
    {
        return $this->hasMany(Turtle::class);
    }

    // Комментарии, написанные пользователем.
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Друзья текущего пользователя (прямые связи).
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->withTimestamps();
    }

    // Входящие связи; при обоюдной дружбе соответствуют списку friends().
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    // Новый экземпляр модели тоже имеет обычную роль по умолчанию.
    protected $attributes = [
        'is_admin' => false,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Проверка административной роли.
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
}
