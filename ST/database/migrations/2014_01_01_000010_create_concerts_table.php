<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConcertsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (in_array('concerts', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('concerts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 500);
            $table->dateTime('date')->index();
            $table->unsignedInteger('location_id')->nullable()->unsigned()->index();
            $table->unsignedInteger('venue_id')->nullable()->index()->unsigned();
            $table->string('location_slug')->index();
            $table->string('slug')->index()->nullable();
            $table->timestamps();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('concerts', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('concerts');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
