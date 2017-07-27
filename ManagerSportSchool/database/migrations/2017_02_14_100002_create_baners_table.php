<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class CreateBanersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('shortcode')->nullable();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('baners');
    }
}
