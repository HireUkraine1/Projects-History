<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class CreateAdvertisementsPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements_pages', function (Blueprint $table) {
            $table->integer('advertisement_id')->unsigned();
            $table->integer('page_id')->unsigned();
            $table->foreign('advertisement_id')->references('id')->on('advertisements');
            $table->foreign('page_id')->references('id')->on('pages');
            $table->primary(['advertisement_id', 'page_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('advertisements_pages');
    }
}
