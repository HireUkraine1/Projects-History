<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkerTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('worker_id')->unsigned();
            $table->string('tag')->index();
            $table->timestamps();
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
        Schema::dropIfExists('workers_tags');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
