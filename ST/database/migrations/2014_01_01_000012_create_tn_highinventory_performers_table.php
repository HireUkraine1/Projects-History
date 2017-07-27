<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTnHighinventoryPerformersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (in_array('tn_highinventory_performers', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;

        Schema::create('tn_highinventory_performers', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('performer_id')->nullable()->unsigned();
            $table->integer('percent');
            $table->timestamps();

            $table->foreign('performer_id')->references('id')->on('performers')->onDelete('cascade');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('tn_highinventory_performers', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('tn_highinventory_performers');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
