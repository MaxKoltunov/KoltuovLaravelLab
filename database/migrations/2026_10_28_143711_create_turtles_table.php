
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turtles', function (Blueprint $table) {
            $table->id();

            // Основная информация
            $table->string('name');
            $table->string('scientific_name');
            $table->string('category');

            // Описания
            $table->text('short_description');
            $table->text('intro_description');
            $table->text('full_description');

            // Изображение
            $table->string('image_path')->nullable();

            // Дата добавления в каталог
            $table->date('catalogued_at')->nullable();

            // Служебные даты
            $table->timestamps();

            // Мягкое удаление
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turtles');
    }
};
