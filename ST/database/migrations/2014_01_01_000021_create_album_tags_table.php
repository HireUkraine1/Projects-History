<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlbumTagsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('album_tags', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('album_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('album_id')->nullable()->unsigned();
            $table->index('album_id');
            $table->integer('tag_id')->nullable()->unsigned();
            $table->index('tag_id');
            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('album_tags', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('album_tags');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
