<?php

use App\Support\Database\Blueprint;
use App\Support\Database\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('orders', function(Blueprint $table){
            $table->model_columns();
            $table->json('data');
            $table->boolean('imported')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('orders');
    }
}
