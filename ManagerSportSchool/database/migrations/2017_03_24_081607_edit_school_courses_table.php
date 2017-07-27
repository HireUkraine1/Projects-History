<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class EditSchoolCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_courses', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->integer('activity_courses_id')->unsigned();
            $table->foreign('activity_courses_id')->references('id')->on('activity_courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_courses', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('description', 1000)->nullable();
            $table->dropColumn('activity_courses_id');
        });
    }
}
