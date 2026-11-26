<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Не пересоздаём users: на неё уже ссылается turtles.user_id.
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 32)->nullable();
            $table->unique('username');
        });

        // Назначаем username ранее зарегистрированным пользователям.
        DB::table('users')->select('id', 'name')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $base = Str::slug($user->name, '_');
                $base = preg_replace('/[^a-z0-9_]/', '', strtolower($base)) ?? '';

                if (strlen($base) < 3 || ! preg_match('/^[a-z]/', $base)) {
                    $base = 'user_'.$base;
                }

                $base = substr($base, 0, 32);
                $username = $base;
                $suffix = 2;

                // Одинаковые имена не должны создавать одинаковые ссылки.
                while (DB::table('users')->where('username', $username)->exists()) {
                    $ending = '_'.$suffix++;
                    $username = substr($base, 0, 32 - strlen($ending)).$ending;
                }

                DB::table('users')->where('id', $user->id)->update([
                    'username' => $username,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
