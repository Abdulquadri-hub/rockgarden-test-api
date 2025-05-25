<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_no')->nullable(false)->unique();
            $table->string('marital_status')->nullable(false);
            $table->string('nationality')->nullable(false);
            $table->string('religious_pref')->nullable(true);
            $table->string('after_death_pref')->nullable(true);
            $table->enum('sex_of_carer_pref', ['Male', 'Female', 'Both', 'Other'])->nullable(false);
            $table->text('doctors_surgery')->nullable(true);
            $table->string('gp')->nullable(true);
            $table->string('mental_health_doctor')->nullable(true);
            $table->string('funeral_director')->nullable(true);
            $table->text('allergies')->nullable(true);
            $table->text('medical_diagnosis')->nullable(true);
            $table->text('medical_history')->nullable(true);
            $table->text('current_illness')->nullable(true);
            $table->text('dietary_needs')->nullable(true);
            $table->string('treatment_guide')->nullable(true);
            $table->text('treatment_guide_info')->nullable(true);
            $table->float('height_cm')->nullable(true);
            $table->string('eye_colour')->nullable(true);
            $table->string('hair_colour')->nullable(true);
            $table->string('build')->nullable(true);
            $table->string('hair_length')->nullable(true);
            $table->string('eye_length')->nullable(true);
            $table->float('weight_on_admission_kg')->nullable(true);
            $table->boolean('uses_hearing_aid')->nullable(true);
            $table->string('maiden_name')->nullable(true);
            $table->string('prev_occupation')->nullable(true);
            $table->date('date_of_arrival')->nullable(true);
            $table->string('client_type')->nullable(true);
            $table->json('careplan')->nullable(true);
            $table->unsignedInteger('user_id')->nullable(true);
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
        Schema::dropIfExists('clients');
    }
}
