<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderTicketsTable extends Migration
{

    public function up()
    {

        Schema::create('order_tickets', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('ticket_request_id', 255);
            $table->string('website_name', 100);
            $table->string('order_type');
            $table->dateTime('order_at');
            $table->string('city', 200);
            $table->string('state', 25);
            $table->string('zip', 20);
            $table->string('country', 30);
            $table->integer('concert_id');//->unsigned();
            $table->string('concert', 500);
            $table->dateTime('concert_date');
            $table->string('concert_category', 50);
            $table->string('concert_sub_category', 100);
            $table->string('venue', 200);
            $table->integer('venue_id')->unsigned();
            $table->integer('ticket_quantity');
            $table->decimal('total_unconverted', 5, 2);
            $table->decimal('revenue', 5, 2);
            $table->decimal('markup_unconverted', 5, 2);
            $table->integer('site_number');
            $table->boolean('phone_order');
            $table->boolean('is_mobile');
            $table->string('shipping_description', 50);
            $table->string('rule_name_internal');
            $table->decimal('website_base_price', 5, 2);
            $table->string('ticket_provider', 5);
            $table->timestamps();
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });

    }


    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('order_tickets');

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }

}
		