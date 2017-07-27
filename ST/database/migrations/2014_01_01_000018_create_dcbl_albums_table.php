<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDcblAlbumsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('dcbl_albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('dcbl_albums', function (Blueprint $table) {
            //
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('album_id')->nullable()->unsigned();
            $table->index('album_id');
            $table->string('title', 500);
            $table->string('artist_literal', 500);
            $table->boolean('unofficial');
            $table->integer('duration');
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
        if (in_array('dcbl_albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('dcbl_albums');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
