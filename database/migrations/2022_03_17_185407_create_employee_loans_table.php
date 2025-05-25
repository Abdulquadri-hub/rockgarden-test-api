<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('staff_id');
            $table->string('name')->nullable(true);
            $table->string('currency')->nullable(false);
            $table->float('amount')->nullable(false);
            $table->date('disbursement_date')->nullable(true);
            $table->date('repayment_start_date')->nullable(true);
            $table->float('installment_amount')->nullable(false);
            $table->text('reason')->nullable(true);
            $table->enum('state', ['PENDING', 'APPROVED', 'DISAPPROVED', 'CLOSED'])->nullable(false);
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
        Schema::dropIfExists('employee_loans');
    }
}
