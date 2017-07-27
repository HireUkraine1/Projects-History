<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlbumGenresTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('album_genres', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('album_genres', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('album_id')->index()->nullable();
            $table->unsignedInteger('genre_id')->index()->nullable();
            $table->foreign('album_id')->references('id')->on('albums');
            $table->foreign('genre_id')->references('id')->on('genres');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('album_genres', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('album_genres');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
