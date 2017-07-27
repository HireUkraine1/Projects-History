<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFansTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fans', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email', 500)->index();
            $table->string('password', 150);
            $table->string('name', 300);
            $table->smallInteger('status');
            $table->string('hash_link', 1000);
            $table->dateTime('last_login');
            $table->smallInteger('dont_bother');
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('fans');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
