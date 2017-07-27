<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTnVenuesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('tn_venues', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('tn_venues', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->index()->primary();
            $table->unsignedInteger('venue_id')->nullable()->index();
            $table->integer('capacity');
            $table->string('child_rules', 1000);
            $table->string('street_1', 300);
            $table->string('street_2', 100);
            $table->string('city', 200)->index();
            $table->string('state', 20)->index();
            $table->string('zip', 20)->index();
            $table->string('country', 30);
            $table->string('phone', 20);
            $table->text('directions');
            $table->text('notes');
            $table->integer('number_of_configurations');
            $table->string('parking', 50);
            $table->string('public_transportation', 100);
            $table->string('rules', 100);
            $table->string('url', 200);
            $table->string('willcall', 20);
            $table->timestamps();

            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('tn_venues', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('tn_venues');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
