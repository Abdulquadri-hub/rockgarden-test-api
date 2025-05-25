<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeductionAllowancesAndBonuusdesignationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowances', function (Blueprint $table) {
            $table->unsignedBigInteger('designation_id')->nullable();
        });

        Schema::table('bonuses', function (Blueprint $table) {
            $table->unsignedBigInteger('designation_id')->nullable();
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->unsignedBigInteger('designation_id')->nullable();
        });

        Schema::table('taxes', function (Blueprint $table) {
            $table->unsignedBigInteger('designation_id')->nullable();
        });

        Schema::table('pay_runs', function (Blueprint $table) {
            $table->json('deductions')->nullable()->change();
            $table->json('allowances')->nullable()->change();
            $table->json('bonuses')->nullable();
            $table->json('taxes')->nullable();
            $table->float('basic_salary')->nullable();
        });

        Schema::table('pay_run_loans', function (Blueprint $table) {
            $table->drop();
        });

        Schema::table('pay_run_bonuses', function (Blueprint $table) {
            $table->drop();
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
