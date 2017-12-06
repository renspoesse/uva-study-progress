<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mysql = "
        
        INSERT INTO `students` (`id`, `student_number`, `display_name`, `program_code`, `program_name`, `cohort`, `bsa_credits`, `bsa`, `second_year`, `second_year_b1_credits`, `second_year_b2_credits`, `second_year_b3_credits`, `second_year_b4_credits`, `second_year_b5_credits`, `second_year_b6_credits`, `second_year_b1_subjects`, `second_year_credits`, `second_year_credits_expected`, `second_year_credits_goal`, `dip_category`, `credits`, `gpa_current`, `graduation_date_expected`, `first_name`, `last_name`, `tussenvoegsel`, `initials`, `birth_date`, `birth_place`, `birth_country`, `gender`, `nationality`, `email_address`, `vooropleiding`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 10000000, 'Jan Janssen', 3003, 'B Fiscale economie', 2016, 42, 'PS', 2017, 6, NULL, NULL, NULL, NULL, NULL, 1, 6, 42, 42, '421', 48, '7.00', '2021-03-01', 'Jan', 'Janssen', '', 'M', '1978-12-09', 'Zwolle', 'NLD', 2, 'Nederland', 'janjanssen@example.com', 'Hbo', 1, '2017-12-04 18:58:54', '2017-12-05 11:27:44'),
(2, 20000000, 'Piet Pieters', 3001, 'B Economie en bedrijfskunde', 2016, 60, 'MX', 2017, 12, NULL, NULL, NULL, NULL, NULL, 2, 12, 58, 60, '602', 72, '8.00', '2019-08-31', 'Piet', 'Pieters', '', 'C', '1991-10-01', 'BLARICUM', 'NLD', 1, 'Nederland', 'pietpieters@example.com', 'Vwo', 1, '2017-12-04 18:58:54', '2017-12-05 11:27:44'),
(3, 30000000, 'Koen van Koenders', 3001, 'B Economie en bedrijfskunde', 2016, 48, 'PS', 2017, 12, NULL, NULL, NULL, NULL, NULL, 2, 12, 55, 48, '482', 60, '7.00', '2020-04-06', 'Koen', 'Koenders', 'van', 'K', '1955-02-06', 'Enschede', 'NLD', 1, 'Nederland', 'koenvankoenders@example.com', 'Vwo', 1, '2017-12-04 18:58:54', '2017-12-05 11:27:44');
        ";

        DB::statement($mysql);
    }
}
