<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    // Автор и черепаха назначаются явно на сервере, а не из формы.
    protected $fillable = [
        'text',
    ];

    public function turtle(): BelongsTo
    {
        // Связь сохраняется и при мягком удалении черепахи.
        return $this->belongsTo(Turtle::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
