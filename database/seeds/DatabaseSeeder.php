<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsTableSeeder::class);
        $this->call(AdviceTableSeeder::class);
        $this->call(NewsTableSeeder::class);
        $this->call(StudentsTableSeeder::class);
    }
}
