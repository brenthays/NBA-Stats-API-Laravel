<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ConferencesTableSeeder::class,
            TeamsTableSeeder::class,
            PossessionPlayerSeeder::class,
        ]);

        Cache::clear();
    }
}
