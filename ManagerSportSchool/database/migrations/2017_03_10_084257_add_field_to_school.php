<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class AddFieldToSchool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('trading_name')->nullable()->after('name');
            $table->string('mobile')->after('phone');
            $table->string('street_mailing')->nullable()->after('postal');
            $table->string('postal_mailing')->nullable()->after('postal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('trading_name');
            $table->dropColumn('mobile');
            $table->dropColumn('street_mailing');
            $table->dropColumn('postal_mailing');
        });
    }
}
