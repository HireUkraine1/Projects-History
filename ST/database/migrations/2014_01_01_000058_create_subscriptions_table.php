<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('fan_id')->unsigned();
            $table->integer('plan_id')->unsigned();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('cancel_date');
            $table->dateTime('next_billing_date');
            $table->string('summary', 500);
            $table->string('description', 1000);
            $table->timestamps();

            $table->foreign('fan_id')->references('id')->on('fans')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
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
        Schema::dropIfExists('subscriptions');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
