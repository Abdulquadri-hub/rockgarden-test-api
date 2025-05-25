<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaffChartMissingFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('staff_charts', function (Blueprint $table) {
            $table->string('temperature')->nullable();
            $table->string('pulse')->nullable();
            $table->string('blood_pressure_systolic')->nullable();
            $table->string('blood_pressure_diastolic')->nullable();
            $table->string('blood_sugar')->nullable();
            $table->string('oxygen_saturation')->nullable();
            $table->string('stool_observed')->nullable();
            $table->string('respiration')->nullable();
            $table->string('antecedent_event')->nullable();
            $table->string('resident_height')->nullable()->change();
            $table->string('resident_weight')->nullable()->change();
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
