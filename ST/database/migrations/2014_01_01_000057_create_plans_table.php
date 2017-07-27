<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlansTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function ($table) {
            $table->engine = 'InnoDB';

            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->string('summary', 2000);
            $table->string('description', 5000);
            $table->double('cost', 6, 2);
            $table->string('type', 50);
            $table->boolean('promotional');
            $table->smallInteger('status');
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('plans');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
