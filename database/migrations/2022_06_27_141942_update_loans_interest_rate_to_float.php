<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoansInterestRateToFloat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->float('interest_rate')->nullable()->change();
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->string('shift_name')->nullable()->change();
        });

        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->renameColumn('designation_id', 'designation_name');
        });

        Schema::table('bonuses', function (Blueprint $table) {
            $table->renameColumn('designation_id', 'designation_name');
        });

        Schema::table('allowances', function (Blueprint $table) {
            $table->renameColumn('designation_id', 'designation_name');
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->renameColumn('designation_id', 'designation_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('float', function (Blueprint $table) {
            //
        });
    }
}
