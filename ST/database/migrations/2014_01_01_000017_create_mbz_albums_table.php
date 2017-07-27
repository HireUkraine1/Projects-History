<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMbzAlbumsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('mbz_albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('mbz_albums', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 50); //bogus id
            // $table->string('mbz_id')->index();
            $table->integer('album_id')->nullable()->unsigned();
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
        if (in_array('mbz_albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('mbz_albums');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
