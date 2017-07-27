<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVenuesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('venues', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('venues', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('geo_location_id')->nullable()->index();
            $table->string('name', 200);
            $table->string('slug', 200)->index();
            $table->timestamps();

            $table->foreign('geo_location_id')->references('id')->on('geo_locations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('venues', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('venues');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
