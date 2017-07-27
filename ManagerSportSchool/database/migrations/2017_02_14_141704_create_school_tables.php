<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class CreateSchoolTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('business_number')->nullable();
            $table->string('street')->nullable();
            $table->string('address_line')->nullable();
            $table->string('city');
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('postal')->nullable();
            $table->string('phone');
            $table->string('fax')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->integer('insurance');
            $table->date('insurance_start_date')->nullable();
            $table->string('insurance_annual_revenue')->nullable();
            $table->integer('insurance_incidents')->nullable();
            $table->integer('approve')->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('school_categories', function (Blueprint $table) {
            $table->integer('school_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->primary(['school_id', 'category_id']);
        });

        Schema::create('business_structures', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        Schema::create('school_business', function (Blueprint $table) {
            $table->integer('school_id')->unsigned();
            $table->integer('business_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('business_structures')->onDelete('cascade');
            $table->primary(['school_id', 'business_id']);
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->string('name');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

        });

        Schema::create('school_activities', function (Blueprint $table) {
            $table->integer('school_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->primary(['school_id', 'activity_id']);
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
        Schema::drop('schools');
        Schema::drop('school_categories');
        Schema::drop('business_structures');
        Schema::drop('school_business');
        Schema::drop('activities');
        Schema::drop('school_activities');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
