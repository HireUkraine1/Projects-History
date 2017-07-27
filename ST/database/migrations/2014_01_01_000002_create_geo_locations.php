<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGeoLocations extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('geo_locations', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('geo_locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('country', 4);
            $table->string('state', 3)->index()->nullable();
            $table->string('state_full', 50)->nullable();
            $table->string('county', 50)->nullable();
            $table->string('time_zone', 100)->nullable();
            $table->integer('population');
            $table->string('city', 100)->index()->nullable();
            $table->string('slug', 500)->index()->nullable();
            $table->string('zip', 20)->index()->nullable();
            $table->double('lat', 15, 8)->index()->nullable();
            $table->double('long', 15, 8)->index()->nullable();
            $table->string('phone_code', 80)->nullable();
            $table->string('metro_code', 80)->nullable();
            $table->integer('start_ip')->unsigned()->index();
            $table->integer('end_ip')->unsigned()->index();
            $table->unsignedInteger('location_id')->index()->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('geo_locations', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('geo_locations');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
