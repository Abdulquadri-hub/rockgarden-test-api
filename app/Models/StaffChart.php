<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StaffChart extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'staff_id',
        'client_id',
        'type',
        'report_date',
        'report_time',
        'comment',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Staff Chart';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Staff Chart";
    }

    protected $fillable = [
        'staff_id', 'client_id', 'type', 'report_date', 'report_time', 'comment',
        'risk_of_falls',
        'is_red_flag',
        'media1',
        'media2',
        'media3',
        'media4',
        'category',
        'health_issue',
        'cream_applied',
        'consequence_event',
        'location',
        'reason_for_visit',
        'resultant_action',
        'is_age6069',
        'is_age7079',
        'is_age8089',
        'is_age90_plus',
        'movement_going_up_stairs',
        'movement_coming_down_stairs',
        'suggested_equip_resident',
        'suggested_equip_staff',
        'any_safeguarding_issues',
        'risk_to_children',
        'risk_to_offending',
        'any_other_areas_of_risk',
        'dosage_afternoon_when',
        'dosage_evening',
        'dosage_evening_when',
        'dosage_afternoon',
        'reason_for_medication',
        'other_intake_guide',
        'is_morning_dose_administered',
        'is_afternoon_dose_administered',
        'is_evening_dose_administered',
        'start_date',
        'is_positive_covid_19',
        'weight_pounds',
        'use_kilogram',
        'description',
        'result',
        'score',
        'drink_type',
        'delivery_method',
        'quantity_ml',
        'level_of_need',
        'present_situation',
        'actions',
        'family_participant',
        'other_participants',
        'resident_needs',
        'weight_kg',
        'review_date',
        'risk_of_physical_abuse_to_self',
        'risk_of_physical_abuse_to_others',
        'risk_of_discrimination',
        'risk_of_pressure_sores',
        'risk_of_manual_handling',
        'risk_of_wandering',
        'risk_to_property',
        'agitation_verbal_physical_aggression',
        'self_neglect',
        'vulnerability_from_others',
        'indication_of_physical_emotional_abuse',
        'any_safeguarding_issues',
        'risk_to_children',
        'any_other_areas_of_risk',
        'is_full_mobile',
        'use_walking_aid',
        'is_wheel_chair_dependant',
        'need_assistance_of_one',
        'need_assistance_of_two',
        'is_fully_dependant',
        'has_demantia_frail',
        'has_high_blood_pressure',
        'has_poor_circulation',
        'has_cva_tia',
        'has_osteo_rheumatoid_arthritis',
        'has_osteoporosis',
        'has_poor_vision',
        'has_poor_hearing',
        'has_diabetes',
        'has_amputee',
        'has_history_of_falls',
        'admitted_for_investigation',
        'has_catheter',
        'has_inco_urine',
        'has_inco_faeces',
        'has_incontinent_doubly',
        'requires_assistance',
        'uses_diuretics',
        'uses_sedatives',
        'uses_tranquilisers',
        'uses_anti_hypertensive',
        'uses_reg_aperient',
        'uses_hypoglycaemic_agents',
        'drink_alcohol',
        'resident_height',
        'resident_weight',
        'info_comprehension',
        'info_behavior',
        'info_disability',
        'handling_pain',
        'handling_skin',
        'handling_other',
        'movement_walking',
        'movement_standing',
        'movement_using_toilet',
        'movement_going_to_bed',
        'movement_getting_from_bed',
        'movement_on_bed',
        'visit_type',
        'visit_location',
        'visitor_name',
        'bowel_type',
        'weight_grams',
        'weight_stone',
        'food_period',
        'food_type',
        'review_type',
        'service_user_participant',
        'medication_type',
        'medicine_name',
        'medication_unit',

    ];

    public function client(){
        return $this->belongsTo(\App\Models\Client::class, 'client_id');
    }

    public function staff(){
        return $this->belongsTo(\App\Models\Employee::class, 'staff_id');
    }
}
