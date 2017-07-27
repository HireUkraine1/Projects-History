<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class CreateAdvertisementsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements_categories', function (Blueprint $table) {
            $table->integer('advertisement_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->foreign('advertisement_id')->references('id')->on('advertisements');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->primary(['advertisement_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('advertisements_categories');
    }
}
