<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSaleOrdesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('sale_orders', function (Blueprint $table) {
             $table->dropColumn('vendor_id');
             $table->dropColumn('reference');
             $table->dropColumn('shipment_date');
             $table->dropColumn('shipment_preference');
             $table->dropColumn('discount');
             $table->dropColumn('status');
             $table->dropColumn('payment');
             $table->dropColumn('delivery_method');
             $table->dropColumn('uploaded_file');
             $table->dropColumn('terms');
             $table->dropColumn('notes');
             $table->dropColumn('shipping_charges');
             $table->dropColumn('adjustment');
             $table->renameColumn('created_by_staff_id', 'created_by_user_id');
             $table->unsignedInteger('item_id')->nullable();
             $table->string('item_unit')->nullable();
             $table->string('item_name')->nullable();
             $table->integer('item_count')->nullable();
             $table->string('item_currency')->nullable();
             $table->float('price_per_unit')->nullable();
             $table->dateTime('order_date')->nullable()->change();
             $table->string('order_details')->nullable();
             $table->string('invoice_no')->nullable();
             $table->string('invoiced')->nullable()->change();
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
