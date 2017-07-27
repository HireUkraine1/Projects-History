<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateErrorTableImportAlbum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_import_album', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('number_under_account');
            $table->integer('performer_id');
            $table->string('performer_name');
            $table->string('note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('error_import_album');
    }

}

