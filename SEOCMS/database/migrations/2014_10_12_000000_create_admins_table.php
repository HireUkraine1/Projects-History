<?php

use App\Support\Database\Blueprint;
use App\Support\Database\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('admins', function (Blueprint $table) {
            $table->model_columns();
            $table->rememberToken();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('admins');
    }
}
