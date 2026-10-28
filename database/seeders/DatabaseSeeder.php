<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Populate the catalog with its five example turtles.
     */
    public function run(): void
    {
        $this->call(TurtleSeeder::class);
    }
}
