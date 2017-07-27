<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTracksTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('tracks', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('tracks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('album_id')->nullable()->unsigned();
            $table->string('mbz_id', 200);
            $table->string('name', 500);
            $table->integer('duration');
            $table->integer('rank');
            $table->integer('listeners')->index();
            $table->integer('playcount')->index();
            $table->timestamps();
            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('tracks', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('tracks');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
