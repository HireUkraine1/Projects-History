<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('status', ['PUBLISHED', 'DRAFT'])->default('PUBLISHED');
            $table->enum('search_form', ['-', 'SEARCH SCHOOL', 'SEARCH INSTRUCTORS COURSES'])->default('-');
            $table->enum('slogan', ['Disable', 'Enable'])->default('Disable');
            $table->string('baner_image')->nullable();
            $table->string('baner_text')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages');
    }
}
