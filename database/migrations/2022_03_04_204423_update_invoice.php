<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('invoices', function($table) {
            $table->dropColumn('service_application_id');
            $table->dropColumn('payment_description');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->text('payment_description')->nullable(true);
            $table->boolean('is_monthly_recurrent')->nullable(false);
            $table->date('next_charge_date')->nullable(true);
            $table->string('email')->nullable(true);
            $table->string('invoice_no')->nullable(true)->unique();
            $table->unsignedInteger('client_id')->nullable(true);
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
