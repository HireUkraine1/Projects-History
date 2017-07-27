<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('locations', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('city', 50)->nullable()->index();
            $table->string('state', 4)->nullable()->index();
            $table->string('country', 2)->nullable()->index();
            $table->string('state_full', 100)->nullable();
            $table->integer('event_count')->nullable()->index();
            $table->string('slug', 200)->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('locations', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('locations');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
