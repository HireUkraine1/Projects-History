<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCityTextTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_text', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('location_id')->nullable()->index()->unsigned();
            $table->text('text');
            $table->boolean('custom');
            $table->dateTime('expire');

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
        Schema::dropIfExists('city_text');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
