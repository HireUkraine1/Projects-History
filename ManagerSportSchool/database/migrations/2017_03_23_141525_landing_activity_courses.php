<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class LandingActivityCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_activity_courses', function (Blueprint $table) {
            $table->integer('landing_id')->unsigned();
            $table->integer('activity_course_id')->unsigned();
            $table->foreign('landing_id')->references('id')->on('school_landings')->onDelete('cascade');
            $table->foreign('activity_course_id')->references('id')->on('activity_courses')->onDelete('cascade');
            $table->primary(['landing_id', 'activity_course_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('landing_activity_courses');
    }
}
