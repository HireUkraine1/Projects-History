<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class AddEnableOnSchoolLandingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_landings', function (Blueprint $table) {
            $table->integer('active')->default(0);
            $table->dropColumn('gallery');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_landings', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
}
