<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBurbsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('burbs', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('parent_location_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->timestamps();

            $table->foreign('parent_location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
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
        Schema::dropIfExists('burbs');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
