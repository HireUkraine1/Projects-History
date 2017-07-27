<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class ChangePreviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('preview_school_landing');
        Schema::create('preview_school_landing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('landing_id')->unsigned();
            $table->string('banner')->nullable();
            $table->longText('about_us')->nullable();
            $table->longText('meet_team')->nullable();
            $table->longText('service_overview')->nullable();
            $table->longText('features')->nullable();
            $table->longText('location_features')->nullable();
            $table->longText('tourist_attributes')->nullable();
            $table->longText('accomodations')->nullable();
            $table->longText('thumbnail')->nullable();
            $table->string('token')->unique();
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
        Schema::drop('preview_school_landing');
        Schema::create('preview_school_landing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->string('banner')->nullable();
            $table->longText('about_us')->nullable();
            $table->longText('meet_team')->nullable();
            $table->longText('service_overview')->nullable();
            $table->longText('features')->nullable();
            $table->longText('location_features')->nullable();
            $table->longText('tourist_attributes')->nullable();
            $table->longText('accomodations')->nullable();
            $table->longText('thumbnail')->nullable();
            $table->string('token')->unique();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });
    }
}
