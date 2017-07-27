<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFanNotesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fan_notes', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('fan_id')->unsigned();
            $table->integer('admin_id')->unsigned();
            $table->string('action', 1000);
            $table->string('note', 1000);
            $table->timestamps();
            $table->foreign('fan_id')->references('id')->on('fans')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
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
        Schema::dropIfExists('fan_notes');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
