<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGmapVenuesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('gmap_venues', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('gmap_venues', function (Blueprint $table) {
            //
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('venue_id')->nullable()->unsigned();
            $table->decimal('lat', 10, 8);
            $table->decimal('lon', 10, 8);
            $table->text('embedded_map');
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
        if (in_array('gmap_venues', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('gmap_venues');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
