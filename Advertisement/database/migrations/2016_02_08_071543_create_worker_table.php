<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('first_name');
            $table->string('sername')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('avatar_path')->nullable();
            $table->text('description')->nullable();
            $table->string('personal_site')->nullable();
            $table->boolean('pay')->nullable()->default(0);
            $table->boolean('show')->nullable()->default(0);
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('workers');

    }
}
