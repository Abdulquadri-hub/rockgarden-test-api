<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_application', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plan_id')->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('applicant_id')->nullable();
            $table->string('phone_number_payee')->nullable();
            $table->string('phone_number_next_of_kin')->nullable();
            $table->string('primary_language_spoken')->nullable();
            $table->tinyInteger('receiving_service_elsewhere')->nullable();
            $table->string('home_settings_description')->nullable();
            $table->tinyInteger('require_general_healthcare')->nullable();
            $table->tinyInteger('require_mobility_assistance')->nullable();
            $table->tinyInteger('require_personal_supervision')->nullable();
            $table->tinyInteger('require_emotional_support')->nullable();
            $table->tinyInteger('require_demantia_care')->nullable();
            $table->tinyInteger('require_grocery_shopping_assistance')->nullable();
            $table->tinyInteger('require_feeding_assistance')->nullable();
            $table->tinyInteger('require_haircare_nailcare_assistance')->nullable();
            $table->tinyInteger('require_bathing_grooming_assistance')->nullable();

            $table->tinyInteger('require_dishes_laundry_assistance')->nullable();
            $table->tinyInteger('require_meal_prep_assistance')->nullable();
            $table->tinyInteger('require_toileting_assistance')->nullable();
            $table->tinyInteger('require_health_monitoring')->nullable();
            $table->tinyInteger('require_vital_signs_monitoring')->nullable();
            $table->tinyInteger('require_oral_skin_medication')->nullable();
            $table->tinyInteger('require_injections')->nullable();
            $table->tinyInteger('require_dressing_of_wounds')->nullable();
            $table->tinyInteger('require_oxygen_therapy')->nullable();
            $table->tinyInteger('require_exercise_oral_feeding')->nullable();
            $table->tinyInteger('require_ng_tube_feeding')->nullable();
            $table->tinyInteger('require_post_surgical_management')->nullable();
            $table->tinyInteger('require_companionship')->nullable();
            $table->tinyInteger('require_appointment_reminder')->nullable();
            $table->tinyInteger('require_patient_recovery_monitoring')->nullable();
            $table->tinyInteger('require_improvement_suggestions')->nullable();
            $table->tinyInteger('require_improvement_advice')->nullable();
            $table->tinyInteger('require_steady_availability_for_questions')->nullable();
            $table->tinyInteger('require_highly_skilled_nursing')->nullable();
            $table->tinyInteger('require_other_skilled_nursing')->nullable();
            $table->tinyInteger('require_other_assistance')->nullable();

            $table->string('other_assistance_description')->nullable();
            $table->string('main_source_of_finance')->nullable();

            $table->tinyInteger('has_history_of_urinary_incontinence')->nullable();
            $table->tinyInteger('has_history_of_feacal_incontinence')->nullable();
            $table->tinyInteger('number_of_falls_past_12months')->nullable();
            $table->tinyInteger('has_diabetes')->nullable();
            $table->tinyInteger('has_hypertension')->nullable();
            $table->tinyInteger('has_hearing_impairment')->nullable();
            $table->tinyInteger('has_dental_problem')->nullable();
            $table->tinyInteger('has_stroke_tia')->nullable();
            $table->tinyInteger('has_sleep_problem')->nullable();
            $table->tinyInteger('has_arthritis')->nullable();
            $table->tinyInteger('has_difficulty_moving_around')->nullable();
            $table->tinyInteger('has_blindness_or_partial')->nullable();
            $table->tinyInteger('has_congestive_heart_failure')->nullable();
            $table->tinyInteger('has_history_of_demantia')->nullable();
            $table->tinyInteger('has_history_of_mental_illness')->nullable();
            $table->tinyInteger('has_cancer_or_terminal_illness')->nullable();

            $table->string('other_health_problems')->nullable();
            $table->bigInteger('admissions_in_last_1year')->nullable();

            $table->string('past_medical_surgical_history')->nullable();
            $table->string('all_current_medications')->nullable();
            $table->string('known_allergies')->nullable();

            $table->double('weight_kg', 16, 2)->nullable();
            $table->double('height_ft', 16, 2)->nullable();


            $table->string('build_slim_or_plum')->nullable();
            $table->string('latest_blood_pressure')->nullable();
            $table->string('latest_fasting_blood_sugar')->nullable();
            $table->string('hiv_status')->nullable();
            $table->string('hbsag_hcv_status')->nullable();
            $table->string('all_relevant_diagnosis')->nullable();
            $table->text('signature')->nullable();
            $table->tinyInteger('send_all_correspondence_to_applicant')->nullable();

            $table->double('service_cost', 16, 2)->nullable();

            $table->date('initial_payment_date')->nullable();
            $table->date('date_approved')->nullable();
            $table->tinyInteger('is_approved')->nullable();

            $table->string('fullname_next_of_kin')->nullable();
            $table->string('fullname_signatory')->nullable();
            $table->string('require_basic_food_preparation')->nullable();
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
        Schema::dropIfExists('service_application');
    }
}