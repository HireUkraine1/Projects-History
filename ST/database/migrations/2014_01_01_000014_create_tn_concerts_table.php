<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTnConcertsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('tn_concerts', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('tn_concerts', function (Blueprint $table) {
            //
            $table->engine = 'InnoDB';

            $table->integer('id')->unique();
            $table->integer('concert_id')->nullable()->index()->unsigned();
            $table->integer('tn_parent_category_id')->nullable()->index()->unsigned(); //will add table later
            $table->integer('tn_child_category_id')->nullable()->index()->unsigned();
            $table->integer('tn_grandchild_category_id')->nullable()->index()->unsigned();
            $table->boolean('womens');
            $table->string('map_url', 1000);
            $table->string('interactive_map_url', 1000);
            $table->string('country', 5)->index();
            $table->string('state', 40)->index();
            $table->string('city', 100)->index();
            $table->timestamps();

            $table->foreign('concert_id')->references('id')->on('concerts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('tn_concerts', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('tn_concerts');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
