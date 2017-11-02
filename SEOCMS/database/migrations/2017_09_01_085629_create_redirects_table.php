<?php

use App\Support\Database\Blueprint;
use App\Support\Database\Migration;

class CreateRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('redirects', function (Blueprint $table) {
            $table->model_columns();
            $table->string('oldurl', 255)->unique();
            $table->string('newurl', 255);
            $table->integer('coderedirect');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('redirects');
    }
}
