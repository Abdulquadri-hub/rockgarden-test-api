<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReceiptAndInvoicePaymentInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('is_paid');
            $table->dropColumn('paid_by_user_id');
            $table->dropColumn('email');
            $table->renameColumn('payment_amount','total_amount_paid');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->renameColumn('paid_by', 'paid_by_user_id');
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
