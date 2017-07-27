<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('notifications', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('fan_id')->nullable()->unsigned();
            $table->integer('days');
            $table->foreign('fan_id')->references('id')->on('fans')->onDelete('cascade');
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
        Schema::dropIfExists('notifications');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
