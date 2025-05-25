<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStaffChartDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_charts', function (Blueprint $table) {
            $table->integer('dosage_afternoon')->nullable()->change();
            $table->integer('dosage_morning')->nullable()->change();
            $table->integer('dosage_evening')->nullable()->change();
            $table->boolean('is_afternoon_dose_administered')->nullable()->change();
            $table->boolean('is_evening_dose_administered')->nullable()->change();
            $table->boolean('is_morning_dose_administered')->nullable()->change();
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
