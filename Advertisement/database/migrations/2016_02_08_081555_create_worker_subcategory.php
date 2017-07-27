<?php

use Illuminate\Database\Migrations\Migration;

class CreateWorkerSubcategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('worker_subcategory', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('worker_id')->unsigned();
            $table->integer('sub_categories_id')->unsigned();
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->foreign('sub_categories_id')->references('id')->on('sub_categories')->onDelete('cascade');
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
        Schema::dropIfExists('worker_subcategory');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
