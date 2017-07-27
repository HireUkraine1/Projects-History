<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class ChangeSchoolImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('school_images');
        Schema::create('landing_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('landing_id')->unsigned();
            $table->string('image');
            $table->foreign('landing_id')->references('id')->on('school_landings')->onDelete('cascade');
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
        Schema::drop('landing_images');
        Schema::create('school_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->string('image');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });
    }


}
