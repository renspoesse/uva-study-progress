<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('news')->insert([
            'title' => 'Welcome to My Dashboard',
            'text' => '<p>We will keep you posted on updates for My Dashboard. Furthermore, the credits you obtain this year will be updated every period so you can easily see if you\'re on track to meet your goal.</p>',
            'is_published' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
