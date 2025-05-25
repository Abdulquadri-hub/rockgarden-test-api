<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->nullable();
            $table->integer('customer_user_id')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('payment_name')->nullable();
            $table->double('amount',16,2)->nullable();

            $table->string('currency')->nullable();
            $table->string('authorization_url')->nullable();
            $table->string('access_code')->nullable();
            $table->string('reference')->nullable();
            $table->string('status')->nullable();
            $table->string('gateway_response')->nullable();
            $table->tinyInteger('charge_attempted')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->tinyInteger('save_card_auth')->nullable();
            $table->tinyInteger('is_flutterwave')->nullable();
            $table->string('link')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}