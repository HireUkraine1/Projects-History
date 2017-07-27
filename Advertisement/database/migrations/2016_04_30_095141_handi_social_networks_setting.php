<?php

use Illuminate\Database\Migrations\Migration;

class HandiSocialNetworksSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_network_setting', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type');
            $table->string('link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('social_network_setting');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
