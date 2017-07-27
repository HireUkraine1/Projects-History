<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class AddLandingActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_activities', function (Blueprint $table) {
            $table->integer('landing_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->foreign('landing_id')->references('id')->on('school_landings')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->primary(['landing_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('landing_activities');
    }
}
