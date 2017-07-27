<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class CreateSchoolsportsectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_sportsections', function (Blueprint $table) {
            $table->integer('school_id')->unsigned();
            $table->integer('sportsections_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('sportsections_id')->references('id')->on('sport_section')->onDelete('cascade');
            $table->primary(['school_id', 'sportsections_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('school_sportsections');
    }
}
