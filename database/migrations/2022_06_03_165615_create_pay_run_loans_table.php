<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayRunLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_run_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pay_run_id')->nullable(false);
            $table->unsignedBigInteger('loan_id')->nullable(false);
            $table->unique(['pay_run_id', 'loan_id'], 'pay_run_loans_ids');
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
        Schema::dropIfExists('pay_run_loans');
    }
}
