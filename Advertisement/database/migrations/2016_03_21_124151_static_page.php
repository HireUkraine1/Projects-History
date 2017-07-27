<?php

use Illuminate\Database\Migrations\Migration;

class StaticPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_pages', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('header');
            $table->text('content');
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
        Schema::dropIfExists('static_pages');
    }
}
