<?php

use App\Support\Database\Blueprint;
use App\Support\Database\Migration;

class CreatePagesheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('pagesheets', function (Blueprint $table) {
            $table->model_columns();
            $table->string('url')->unique();
            $table->string('h1');
            $table->string('title');
            $table->string('description');
            $table->string('keywords');
            $table->boolean('active')->default(true);

            $table->integer('template_id')->unsigned();
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');

            $table->double('sitemappriority', 1, 1);
            $table->text('criticalcss')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('pagesheets');
    }
}
