<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class CreateLandingLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('landing_id')->unsigned();
            $table->string('country');
            $table->string('address');
            $table->string('latitude');
            $table->string('longitude');
            $table->foreign('landing_id')->references('id')->on('school_landings')->onDelete('cascade');
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
        Schema::drop('landing_locations');
    }
}
