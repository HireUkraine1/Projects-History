<?php

use App\Support\Database\Blueprint;
use App\Support\Database\Migration;

class CreateDomainsAliasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('domains_alias', function (Blueprint $table) {
            $table->model_columns();
            $table->string('domain_url', 255)->unique();
            $table->text('robotstxt')->nullable();
            $table->boolean('master')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('domains_alias');
    }
}
