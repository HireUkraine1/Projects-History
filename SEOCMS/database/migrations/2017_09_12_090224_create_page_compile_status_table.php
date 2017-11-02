<?php

use App\Support\Database\Blueprint;
use App\Support\Database\Migration;

class CreatePageCompileStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('page_compile', function (Blueprint $table) {
            $table->model_columns();

            $table->integer('page_id')->unsigned();
            $table->foreign('page_id')->references('id')->on('pagesheets')->onDelete('cascade');

            $table->integer('status')->default(0);

            $table->string('error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('page_compile_status');
    }
}
