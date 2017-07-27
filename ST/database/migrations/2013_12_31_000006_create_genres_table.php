<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGenresTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('genres', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('genres', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('genre', 20);
            $table->string('slug', 20);
            $table->integer('tn_parent_category_id');
            $table->integer('tn_child_category_id');
            $table->integer('tn_grandchild_category_id');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('genres', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('genres');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
