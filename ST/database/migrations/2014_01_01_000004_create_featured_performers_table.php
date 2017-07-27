<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeaturedPerformersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('featured_performers', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('featured_performers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->unsignedInteger('performer_id')->index()->nullable();
            $table->integer('geo')->index();
            $table->integer('side')->index();
            $table->integer('home')->index();
            $table->timestamps();

            $table->foreign('performer_id')->references('id')->on('performers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('featured_performers', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('featured_performers');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
