<?php

namespace App\Listeners;

use App\Events\FriendshipAdded;

class CreateReciprocalFriendship
{
    // Laravel 12 обнаруживает этот listener автоматически по типу аргумента handle().
    public function handle(FriendshipAdded $event): void
    {
        // Защита от некорректного события, даже если оно создано не контроллером.
        if ($event->user->is($event->friend)) {
            return;
        }

        // Обратная связь не порождает новое событие FriendshipAdded.
        // syncWithoutDetaching не дублирует существующую связь.
        $event->friend->friends()->syncWithoutDetaching([$event->user->id]);
    }
}
