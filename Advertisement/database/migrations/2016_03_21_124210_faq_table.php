<?php

use Illuminate\Database\Migrations\Migration;

class FaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('question')->unique();
            $table->text('answer');
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
        Schema::dropIfExists('faq');
    }
}
