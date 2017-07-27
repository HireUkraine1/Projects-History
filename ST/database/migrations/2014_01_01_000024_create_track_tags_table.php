<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrackTagsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('track_tags', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('track_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('track_id')->nullable()->unsigned();
            $table->string('tag', 50);
            $table->string('slug', 20);
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('track_tags', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('track_tags');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');;
    }

}
