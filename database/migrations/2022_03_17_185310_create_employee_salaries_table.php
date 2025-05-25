<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('designation_id')->nullable(false);
            $table->unsignedInteger('pay_day_of_month')->nullable(false);
            $table->float('basic_salary')->nullable(false);
            $table->string('currency')->nullable(false);
            $table->json('allowances')->nullable(true);
            $table->json('taxes')->nullable(true);
            $table->json('deductions')->nullable(true);
            $table->enum('pay_days_per_week', ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'UN'])->nullable(true);
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
        Schema::dropIfExists('employee_salaries');
    }
}
