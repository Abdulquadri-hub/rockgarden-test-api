<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vendor_id')->nullable(false);
            $table->string('order_no')->nullable(false);
            $table->string('reference')->nullable(true);
            $table->date('order_date')->nullable(false);
            $table->date('shipment_date')->nullable(true);
            $table->string('shipment_preference')->nullable(true);
            $table->float('discount')->nullable(true);
            $table->enum('status', ['PROCESSED', 'CANCELLED', 'PENDING']);
            $table->boolean('invoiced')->nullable(false);
            $table->float('payment')->nullable(true);
            $table->string('delivery_method')->nullable(true);
            $table->unsignedInteger('staff_id')->nullable(true);
            $table->unsignedInteger('client_id')->nullable(true);
            $table->string('uploaded_file')->nullable(true);
            $table->text('terms')->nullable(true);
            $table->text('notes')->nullable(true);
            $table->float('total')->nullable(false);
            $table->text('adjustment')->nullable(true);
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
        Schema::dropIfExists('purchase_orders');
    }
}
