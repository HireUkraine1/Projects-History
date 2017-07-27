<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class AddPreviewBannerToSchoolLandingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_landings', function (Blueprint $table) {
            $table->string('thumbnail')->nullable();
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
            $table->dropColumn('thumbnail');
        });
    }
}
