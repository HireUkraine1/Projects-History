<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class ChangeCategorySearchForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('search_form');

        });
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('search_form', ['-', 'Search Club'])->default('-');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('search_form');

        });
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('search_form', ['-', 'SEARCH SCHOOL', 'SEARCH INSTRUCTORS COURSES'])->default('-');
        });
    }
}
