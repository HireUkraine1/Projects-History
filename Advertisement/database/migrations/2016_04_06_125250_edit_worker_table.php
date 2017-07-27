<?php

use Illuminate\Database\Migrations\Migration;

class EditWorkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workers', function ($table) {
            $table->string('newId')->default(0);
            $table->string('position')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workers', function ($table) {
            $table->dropColumn('newId');
            $table->dropColumn('position');
        });
    }
}
