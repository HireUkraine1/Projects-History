<?php

use App\Support\Database\Blueprint;
use App\Support\Database\Migration;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('routes', function (Blueprint $table) {
            $table->model_columns();
            $table->string('slug', 255)->unique();
            $table->string('alias', 255)->nullable();
            $table->text('critical_css')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('routes');
    }
}
