<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\sportport\Facades\Schema;

class AddBanersFieldsToCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('search_form', ['-', 'SEARCH SCHOOL', 'SEARCH INSTRUCTORS COURSES'])->default('-');
            $table->enum('slogan', ['Enable', 'Disable'])->default('Disable');
            $table->string('baner_text')->nullable();
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
            $table->dropColumn('slogan');
            $table->dropColumn('baner_text');
        });
    }
}
