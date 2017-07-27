<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('admins', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('admins', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->smallInteger('role');
            $table->smallInteger('status');
            $table->string('display_name', 80);
            $table->string('username', 40);
            $table->string('password', 150);
            $table->string('remember_token', 40);
            $table->dateTime('last_login');
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
        if (in_array('admins', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('admins');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
