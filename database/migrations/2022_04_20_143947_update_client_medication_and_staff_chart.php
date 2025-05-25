<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientMedicationAndStaffChart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_charts', function (Blueprint $table) {
            $table->string('medicine_name')->nullable();
            $table->string('medication_type')->nullable();
            $table->string('medication_unit')->nullable();
            $table->string('dosage_morning')->nullable();
            $table->string('dosage_morning_when')->nullable();
        });

        Schema::table('client_medications', function (Blueprint $table) {
            $table->unsignedInteger('created_by_id')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            $table->dateTime('end_date')->nullable();
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
