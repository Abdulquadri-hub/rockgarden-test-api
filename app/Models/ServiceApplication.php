<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceApplication extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'service_application';

    protected static $logFillable = true;

    protected static $logAttributes = [
        'plan_id',
        'client_id',
        'applicant_id',
        'phone_number_payee',
        'all_relevant_diagnosis',
        'signature',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Service Application';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Service Application";
    }

    protected $fillable = [
        'plan_id',
        'client_id',
        'applicant_id',
        'phone_number_payee',
        'phone_number_next_of_kin',
        'primary_language_spoken',
        'receiving_service_elsewhere',
        'home_settings_description',
        'require_general_healthcare',
        'require_mobility_assistance',
        'require_personal_supervision',
        'require_emotional_support',
        'require_demantia_care',
        'require_grocery_shopping_assistance',
        'require_feeding_assistance',
        'require_haircare_nailcare_assistance',
        'require_bathing_grooming_assistance',
        'require_dishes_laundry_assistance',
        'require_meal_prep_assistance',
        'require_toileting_assistance',
        'require_health_monitoring',
        'require_vital_signs_monitoring',
        'require_oral_skin_medication',
        'require_injections',
        'require_dressing_of_wounds',
        'require_oxygen_therapy',
        'require_exercise_oral_feeding',
        'require_ng_tube_feeding',
        'require_post_surgical_management',
        'require_companionship',
        'require_appointment_reminder',
        'require_patient_recovery_monitoring',
        'require_improvement_suggestions',
        'require_improvement_advice',
        'require_steady_availability_for_questions',
        'require_highly_skilled_nursing',
        'require_other_skilled_nursing',
        'require_other_assistance',
        'other_assistance_description',
        'main_source_of_finance',
        'has_history_of_urinary_incontinence',
        'has_history_of_feacal_incontinence',
        'number_of_falls_past_12months',
        'has_diabetes',
        'has_hypertension',
        'has_hearing_impairment',
        'has_dental_problem',
        'has_stroke_tia',
        'has_sleep_problem',
        'has_arthritis',
        'has_difficulty_moving_around',
        'has_blindness_or_partial',
        'has_congestive_heart_failure',
        'has_history_of_demantia',
        'has_history_of_mental_illness',
        'has_cancer_or_terminal_illness',
        'other_health_problems',
        'admissions_in_last_1year',
        'past_medical_surgical_history',
        'all_current_medications',
        'known_allergies',
        'weight_kg',
        'height_ft',
        'build_slim_or_plum',
        'latest_blood_pressure',
        'latest_fasting_blood_sugar',
        'hiv_status',
        'hbsag_hcv_status',
        'all_relevant_diagnosis',
        'signature',
        'send_all_correspondence_to_applicant',
        'service_cost',
        'initial_payment_date',
        'date_approved',
        'is_approved',
        'fullname_next_of_kin',
        'fullname_signatory',
        'require_basic_food_preparation',
        'status',
        'client_office_address',
        'plan_name',
        'client_last_name',
        'client_first_name',
        'client_middle_name',
        'client_gender',
        'client_date_of_birth',
        'client_home_address',
        'client_office_address',
        'client_phone_number',
        'client_email',
        'client_state',
        'client_city',
        'disapproval_reason',
        'applying_for_self',
        'relation_next_of_kin',
    ];

    public function applicant(){
        return  $this->belongsTo(\App\Models\User::class,'applicant_id');
    }

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }
}
