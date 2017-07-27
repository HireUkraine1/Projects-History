<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('established')->nullable();
            $table->string('meeting_details')->nullable();
            $table->string('meeting_location')->nullable();
            $table->text('divisions')->nullable();
            $table->text('special_events')->nullable();
            $table->string('contact_details')->nullable();
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
        Schema::dropIfExists('clubs');
    }
}
