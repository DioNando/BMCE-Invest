<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            RoomSeeder::class,
            UserSeeder::class,
            MeetingSeeder::class,
        ]);
    }
}
