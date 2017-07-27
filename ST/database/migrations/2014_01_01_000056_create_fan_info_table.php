<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFanInfoTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('fan_info', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('fan_id')->unsigned();
            $table->string('stripe_id', 255);
            $table->string('legal_name', 255);
            $table->string('email', 500);
            $table->string('phone', 100);
            $table->string('cell_phone', 100);
            $table->dateTime('birthday', 255);
            $table->string('address', 255);
            $table->string('address_2', 255);
            $table->string('country', 255);
            $table->string('state', 255);
            $table->string('city', 255);
            $table->string('zip', 255);
            $table->smallInteger('status');

            $table->timestamps();

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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('fan_info');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
