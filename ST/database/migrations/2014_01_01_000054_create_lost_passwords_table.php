<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLostPasswordsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('lost_passwords', function ($table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('fan_id')->nullable()->unsigned();
            $table->string('hash', 500);
            $table->dateTime('created_at');

            $table->foreign('fan_id')->references('id')->on('fans')->onDelete('cascade');


        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('lost_passwords');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
