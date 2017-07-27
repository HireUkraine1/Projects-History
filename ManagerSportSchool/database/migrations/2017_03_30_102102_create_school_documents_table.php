<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class CreateSchoolDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('school_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('school_id')->unsigned();
            $table->integer('sport_section_id')->default(0);
            $table->string('name');
            $table->string('path');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('school_documents');
    }
}
