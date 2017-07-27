<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVideoWorkerLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_worker_links', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('worker_id')->unsigned();
            $table->string('link');
            $table->engine = 'InnoDB';
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
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
        Schema::dropIfExists('video_worker_links');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
