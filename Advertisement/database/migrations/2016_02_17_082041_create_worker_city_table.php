<?php

use Illuminate\Database\Migrations\Migration;

class CreateWorkerCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_city', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('worker_id')->unsigned();
            $table->integer('city_id')->unsigned();
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
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
        Schema::dropIfExists('worker_city');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');


    }
}
