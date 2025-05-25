<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeePayRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_pay_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('staff_id')->nullable(false);
            $table->float('basic_salary')->nullable(false);
            $table->string('basic_salary_currency')->nullable(false);
            $table->date('payment_date')->nullable(true);
            $table->enum('status', ['PENDING', 'NOPAY', 'PAID'])->nullable(false);
            $table->json('deductions')->nullable(true);
            $table->float('reimbursement')->nullable(true);
            $table->string('reimbursement_currency')->nullable(true);
            $table->text('reimbursement_info')->nullable(true);
            $table->unsignedInteger('pay_days')->nullable(true);
            $table->json('allowances')->nullable(true);
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
        Schema::dropIfExists('employee_pay_runs');
    }
}
