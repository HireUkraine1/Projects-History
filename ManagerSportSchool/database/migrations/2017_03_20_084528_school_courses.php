<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class SchoolCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->integer('landing_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->integer('landing_locations_id')->unsigned();
            $table->string('name');
            $table->string('description', 1000)->nullable();
            $table->dateTime('date');
            $table->integer('quantity_lessons');
            $table->integer('quantity_places');
            $table->integer('busy_places')->default(0);
            $table->decimal('price', 12, 2);

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('landing_id')->references('id')->on('school_landings')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('landing_locations_id')->references('id')->on('landing_locations')->onDelete('cascade');
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
        Schema::drop('school_courses');
    }
}
