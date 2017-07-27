<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (in_array('images', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create("images", function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('path', 5000)->index();
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
        if (in_array('images', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('images');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
