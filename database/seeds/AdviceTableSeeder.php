<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdviceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('advice')->insert([

            'title' => 'What can you see in My Dashboard?',
            'text' => '<p>The first graph has three columns:</p><p>1.      Credits this year: the running total of credits you have earned so far this year.</p><p>2.      Prognosis: expected total number of credits you will earn during your second year. The number is calculated using the number of credits you earned in Year 1 as well as the number of credits you earned in Year 2, Period 1. The formula for the prognosis was developed through regression analysis of historical data and has a significant predictive potential.</p><p>3.      Goal: this is your personal goal for the second year. It is initially set at your first year credit tempo. You can adjust it to a higher or lower tempo by clicking on the link ‘personalise’.</p><p>The second graph displays your progress over time. You can see the standard number of credits in the bars and two lines indicating your prognosis and the credits you have obtained so far.</p>',
            'is_published' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('advice')->insert([

            'title' => 'How to set your goal in My Dashbooard',
            'text' => '<p>My Dashboard &gt; click on Personalise to set your goal</p><p>First reflect on how many courses you plan to pass this year. Take some time to think this through. Do you have good study habits? Do you have other commitments outside of your studies? Do you manage your time well? You are more likely to meet your goal if it is realistic for your situation. </p><p>- If you set <b>your goal above what your prognosis</b> shows you know you are in for a challenge. You may have changed your approach to your studies or are planning to do so.</p><p>- If you set <b>your goal below what your prognosis</b> shows you have chosen a slower pace. You may have personal circumstances that require you to take fewer courses. Or you may have other commitments such as work or membership in a student association.</p>',
            'is_published' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('advice')->insert([

            'title' => 'My Dashboard preferred browsers',
            'text' => '<p>‘My Dashboard’ is best used with one of the following browsers:</p><p>- Chrome</p><p>- Edge</p><p>- Safari</p><p>- Internet Explorer</p>',
            'is_published' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
