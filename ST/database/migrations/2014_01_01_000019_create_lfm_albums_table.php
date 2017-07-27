<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLfmAlbumsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('lfm_albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('lfm_albums', function (Blueprint $table) {
            //
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('album_id')->nullable()->unsigned();
            $table->index('album_id');
            $table->date('release_date');
            $table->string('img_small', 300);
            $table->string('img_medium', 300);
            $table->string('img_large', 300);
            $table->integer('play_count');
            $table->integer('listeners');
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
        if (in_array('lfm_albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('lfm_albums');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
