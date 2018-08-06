<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {

            $table->increments('id');
            $table->string('student_number', 12);
            $table->string('display_name', 128);
            $table->integer('program_code');
            $table->string('program_name', 64);
            $table->integer('program_satisfaction')->nullable();
            $table->integer('cohort');
            $table->integer('year');

            $table->string('first_year_mentor', 128)->nullable();
            $table->integer('first_year_b1_credits')->nullable();
            $table->integer('first_year_b2_credits')->nullable();
            $table->integer('first_year_b3_credits')->nullable();
            $table->integer('first_year_b4_credits')->nullable();
            $table->integer('first_year_b5_credits')->nullable();
            $table->integer('first_year_b6_credits')->nullable();
            $table->integer('first_year_credits')->nullable();
            $table->integer('first_year_credits_goal')->nullable();

            $table->integer('wa1_credits')->nullable();
            $table->string('wa1', 8)->nullable();
            $table->integer('wa2_credits')->nullable();
            $table->string('wa2', 8)->nullable();
            $table->integer('wa3_credits')->nullable();
            $table->string('wa3', 8)->nullable();

            $table->integer('bsa_credits')->nullable();
            $table->string('bsa', 8)->nullable();

            $table->integer('second_year')->nullable();
            $table->integer('second_year_b1_credits')->nullable();
            $table->integer('second_year_b2_credits')->nullable();
            $table->integer('second_year_b3_credits')->nullable();
            $table->integer('second_year_b4_credits')->nullable();
            $table->integer('second_year_b5_credits')->nullable();
            $table->integer('second_year_b6_credits')->nullable();
            $table->integer('second_year_b1_subjects')->nullable();
            $table->integer('second_year_credits')->nullable();
            $table->integer('second_year_credits_expected')->nullable();
            $table->integer('second_year_credits_goal')->nullable();

            $table->string('dip_category', 16)->nullable();
            $table->integer('credits')->default(0);
            $table->decimal('gpa_current', 4, 2)->nullable();
            $table->date('graduation_date_expected')->nullable();

            $table->string('first_name', 128);
            $table->string('last_name', 128);
            $table->string('tussenvoegsel', 128);
            $table->string('initials', 128);
            $table->date('birth_date');
            $table->string('birth_place', 128);
            $table->string('birth_country', 128);
            $table->integer('gender');
            $table->string('nationality', 128);
            $table->string('email_address', 255);
            $table->string('vooropleiding', 128);

            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
