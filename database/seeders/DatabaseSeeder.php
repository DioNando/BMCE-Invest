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
            CountrySeeder::class,
            RoleSeeder::class,
            RoomSeeder::class,
            UserSeeder::class,
            TimeSlotSeeder::class,
        ]);
    }
}
