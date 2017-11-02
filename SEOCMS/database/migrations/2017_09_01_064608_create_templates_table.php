<?php

use App\Support\Database\Blueprint;
use App\Support\Database\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('templates', function (Blueprint $table) {
            $table->model_columns();
            $table->string('virtualroot')->unique();
            $table->string('name');
            $table->text('body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('templates');
    }
}
