<?php

use Illuminate\Database\Migrations\Migration;

class WorkerNetworks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('networks', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('worker_id')->unsigned();
            $table->string('type');
            $table->string('link');
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
        Schema::dropIfExists('networks');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
