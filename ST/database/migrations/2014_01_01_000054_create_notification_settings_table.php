<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('notification_settings', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('fan_id')->nullable()->unsigned();
            $table->integer('performer_id')->nullable()->unsigned();
            $table->integer('days');
            $table->string('type');
            $table->timestamps();
            $table->foreign('fan_id')->references('id')->on('fans')->onDelete('cascade');
            $table->foreign('performer_id')->references('id')->on('performers')->onDelete('cascade');
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
        Schema::dropIfExists('notification_settings');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
