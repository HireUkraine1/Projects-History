<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlbumMembersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('album_members', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('album_members', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('album_id')->nullable()->unsigned();
            $table->index('album_id');
            $table->string('name', 500);
            $table->string('stage_name', 500);
            $table->string('gender', 20);
            $table->string('type', 20);
            $table->timestamps();
            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('album_members', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('album_members');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
