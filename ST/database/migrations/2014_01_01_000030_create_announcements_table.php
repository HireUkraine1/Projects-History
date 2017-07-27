<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnnouncementsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (in_array('announcements', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        Schema::create('announcements', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('admin_id')->nullable()->unsigned()->index();
            $table->string('title', 300);
            $table->string('slug', 300)->index();
            $table->integer('performer_id')->unsigned()->index()->nullable();
            $table->text('text');
            $table->text('excerpt');
            $table->boolean('is_page')->index();
            $table->boolean('status')->index();
            $table->datetime('publish_date')->index();
            $table->text('note');
            $table->string('image_key', 400);
            $table->string('image_path', 5000);
            $table->timestamps();
            $table->foreign('performer_id')->references('id')->on('performers')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (in_array('announcements', explode('|', $_ENV['KEEP_ON_MIGRATE']))) return true;
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('announcements');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
