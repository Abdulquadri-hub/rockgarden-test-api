<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDesignationIdToDesignationName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->string('designation_id')->nullable()->change();
        });

        Schema::table('bonuses', function (Blueprint $table) {
            $table->string('designation_id')->nullable()->change();
        });

        Schema::table('allowances', function (Blueprint $table) {
            $table->string('designation_id')->nullable()->change();
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->string('designation_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('designation_name', function (Blueprint $table) {
            //
        });
    }
}
