<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class ActivityCoutrseTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->unsigned();
            $table->string('name');
            $table->string('description', 1000)->nullable();
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
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
        Schema::drop('activity_courses');
    }
}
