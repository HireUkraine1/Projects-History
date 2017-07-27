<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class BanerTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baner', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->unsigned();
            $table->string('img_path');
            $table->string('link')->nullable()->default('#');
            $table->boolean('show')->nullable()->default(0);
            $table->engine = 'InnoDB';
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
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
        Schema::dropIfExists('baner');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
