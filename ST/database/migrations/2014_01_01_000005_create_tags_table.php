<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('tags', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;

        Schema::create('tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('tag', 50);
            $table->string('slug', 50)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('tags', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('tags');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
