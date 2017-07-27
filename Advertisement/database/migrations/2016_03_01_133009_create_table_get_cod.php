<?php

use Illuminate\Database\Migrations\Migration;

class CreateTableGetCod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('get_cod', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('phone');
            $table->string('cod');
            $table->string('token');
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
        Schema::dropIfExists('get_cod');
    }


}
