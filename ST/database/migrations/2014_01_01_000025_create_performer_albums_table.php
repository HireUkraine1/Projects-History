<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformerAlbumsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('performer_albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('performer_albums', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('performer_id')->unsigned()->nullable();
            $table->integer('album_id')->unsigned()->nullable();
            $table->foreign('performer_id')->references('id')->on('performers')->onDelete('cascade');
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
        if (in_array('performer_albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('performer_albums');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
