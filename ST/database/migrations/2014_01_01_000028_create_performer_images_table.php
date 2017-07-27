<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformerImagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('performer_images', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('performer_images', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('performer_id')->nullable()->index()->unsigned();
            $table->integer('image_id')->nullable()->index()->unsigned();
            $table->string('type', 10)->index();
            $table->string('size', 10);

            $table->foreign('performer_id')->references('id')->on('performers')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('performer_images', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('performer_images');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
