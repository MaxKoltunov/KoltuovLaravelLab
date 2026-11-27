<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            // Обрабатываем только записи, существовавшие до начала миграции.
            $lastId = DB::table('friendships')->max('id');

            if ($lastId === null) {
                return;
            }

            DB::table('friendships')
                ->where('id', '<=', $lastId)
                ->whereColumn('user_id', '!=', 'friend_id')
                ->chunkById(100, function ($friendships): void {
                    foreach ($friendships as $friendship) {
                        DB::table('friendships')->insertOrIgnore([
                            'user_id' => $friendship->friend_id,
                            'friend_id' => $friendship->user_id,
                            'created_at' => $friendship->created_at,
                            'updated_at' => $friendship->updated_at,
                        ]);
                    }
                });
        });
    }

    public function down(): void
    {
        // Миграция только дополняет данные. Нельзя отличить автоматически
        // созданные связи от существовавших ранее, поэтому не удаляем их.
    }
};
