<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBonusAllowancesTaxDeductions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bonuses', function (Blueprint $table) {
            $table->dropUnique('bonuses_name_unique');
        });

        Schema::table('allowances', function (Blueprint $table) {
            $table->dropUnique('allowances_name_unique');
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->dropUnique('deductions_name_unique');
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
