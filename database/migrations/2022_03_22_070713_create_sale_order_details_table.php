<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('stock_id')->nullable(false);
            $table->unsignedInteger('order_id')->nullable(false);
            $table->unsignedInteger('item_id')->nullable(true);
            $table->unsignedInteger('group_item_id')->nullable();
            $table->float('quantity')->nullable(false);
            $table->float('discount')->nullable(false);
            $table->float('amount')->nullable(false);
            $table->unsignedInteger('tax_id')->nullable(true);
            $table->string('currency')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_order_details');
    }
}
