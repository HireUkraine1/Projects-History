<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 255);
            $table->string('code', 10);
            $table->string('summary', 2000);
            $table->integer('percent_off');
            $table->double('dollars_off', 6, 2);
            $table->integer('use_limit');
            $table->integer('use_count');
            $table->smallInteger('status');
            $table->date('start_date');
            $table->date('end_date');
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('coupons');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
