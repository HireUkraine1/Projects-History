<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConcertPerformersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('concert_performers', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('concert_performers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('concert_id')->index()->unsigned()->nullable();
            $table->unsignedInteger('performer_id')->index()->unsigned()->nullable();
            $table->foreign('concert_id')->references('id')->on('concerts')->onDelete('cascade');
            $table->foreign('performer_id')->references('id')->on('performers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('concert_performers', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('concert_performers');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
