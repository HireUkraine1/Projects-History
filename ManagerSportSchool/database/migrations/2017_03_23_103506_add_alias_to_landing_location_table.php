<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class AddAliasToLandingLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landing_locations', function (Blueprint $table) {
            $table->string('alias')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_locations', function (Blueprint $table) {
            $table->dropColumn('alias');
        });
    }
}
