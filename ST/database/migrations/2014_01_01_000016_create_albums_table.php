<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlbumsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('albums', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id')->unsigned();
            $table->integer('rank');
            $table->string('title', 500);
            $table->string('performer', 500);
            $table->string('mbz_id', 500)->index();
            $table->dateTime('release_date')->index();
            $table->integer('play_count');
            $table->integer('listeners');
            $table->integer('track_count')->default(-1);
            $table->string('slug', 5000)->index();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('albums', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('albums');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
