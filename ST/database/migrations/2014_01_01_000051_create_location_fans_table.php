<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocationFansTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_fans', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('fan_id')->nullable()->unsigned();
            $table->integer('location_id')->nullable()->unsigned();
            $table->foreign('fan_id')->references('id')->on('fans')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('location_fans');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
