<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class DeleteSchoolActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('school_activities');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('school_activities', function (Blueprint $table) {
            $table->integer('school_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->primary(['school_id', 'activity_id']);
        });
    }
}
