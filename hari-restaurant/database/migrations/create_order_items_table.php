<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('OrderItemID');
            $table->unsignedBigInteger('ReservationID');
            $table->unsignedBigInteger('ItemID');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('ReservationID')->references('ReservationID')->on('reservation');
            $table->foreign('ItemID')->references('ItemID')->on('menu_items');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
} 