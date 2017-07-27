<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePerformersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('performers', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;

        Schema::create('performers', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('tn_id')->unique();
            $table->integer('genre_id')->unsigned()->nullable();
            $table->string('mbz_id', 100)->index();
            $table->string('name', 500)->index();
            $table->string('short_name')->nullable();
            $table->string('country')->nullable();
            $table->string('disambiguation')->nullable();
            $table->string('formed')->nullable();
            $table->string('ended')->nullable();
            $table->string('gender')->nullable();
            $table->string('type')->nullable();
            $table->text('bio')->nullable();
            $table->text('bio_summary')->nullable();
            $table->integer('listeners');
            $table->integer('plays');
            $table->string('slug', 500)->index();
            $table->smallInteger('status');
            $table->timestamps();

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
        if (in_array('performers', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('performers');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
