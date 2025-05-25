<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStaffChartFieldOnRiskAssessment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_charts', function (Blueprint $table) {
            $table->text('risk_of_falls')->nullable();
            $table->text('risk_of_physical_abuse_to_self')->nullable();
            $table->text('risk_of_physical_abuse_to_others')->nullable();
            $table->text('risk_of_discrimination')->nullable();
            $table->text('risk_of_pressure_sores')->nullable();
            $table->text('risk_of_manual_handling')->nullable();
            $table->text('risk_of_wandering')->nullable();
            $table->text('risk_to_property')->nullable();
            $table->text('agitation_verbal_physical_aggression')->nullable();
            $table->text('self_neglect')->nullable();
            $table->text('vulnerability_from_others')->nullable();
            $table->text('indication_of_physical_emotional_abuse')->nullable();
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
