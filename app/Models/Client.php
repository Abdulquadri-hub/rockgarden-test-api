<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'careplan' => 'array'
    ];

    protected $fillable = [
        'category',
        'client_no',
        'marital_status',
        'nationality',
        'religious_pref',
        'after_death_pref',
        'sex_of_carer_pref',
        'doctors_surgery',
        'gp',
        'mental_health_doctor',
        'funeral_director',
        'allergies',
        'medical_diagnosis',
        'medical_history',
        'current_illness',
        'dietary_needs',
        'treatment_guide',
        'treatment_guide_info',
        'height_cm',
        'eye_colour',
        'hair_colour',
        'build',
        'hair_length',
        'eye_length',
        'weight_on_admission_kg',
        'uses_hearing_aid',
        'maiden_name',
        'prev_occupation',
        'date_of_arrival',
        'client_type',
        'careplan',
        'user_id',
        'room_location',
        'room_number',
        'room_suffix',
        'prev_address',
        'postal_code',
        'admitted_from',
        'admitted_by',
        'sex_of_carer_pref'
    ];

    protected static $logAttributes = [
        'category',
        'client_no',
        'marital_status',
        'nationality',
        'religious_pref',
        'after_death_pref',
        'sex_of_carer_pref',
        'doctors_surgery',
        'gp',
        'mental_health_doctor',
        'funeral_director',
        'allergies',
        'medical_diagnosis',
        'medical_history',
        'current_illness',
        'dietary_needs',
        'treatment_guide',
        'treatment_guide_info',
        'height_cm',
        'eye_colour',
        'hair_colour',
        'build',
        'hair_length',
        'eye_length',
        'weight_on_admission_kg',
        'uses_hearing_aid',
        'maiden_name',
        'prev_occupation',
        'date_of_arrival',
        'client_type',
        'careplan',
        'user_id',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on Client.';


    protected $with = ['user'];

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} Client.";
    }

    public function user(){
        return  $this->belongsTo(\App\Models\User::class,'user_id');
    }

    public function medications(){
        return  $this->hasMany(\App\Models\ClientMedication::class,'client_id');
    }

    public function deathRecords(){
        return  $this->hasMany(\App\Models\DeathRecord::class,'client_id');
    }

    public function invoices(){
        return  $this->hasMany(\App\Models\Invoice::class,'client_id');
    }

    public function keyContacts(){
        return  $this->hasMany(\App\Models\KeyContact::class,'client_id');
    }

    public function medicalHistories(){
        return  $this->hasMany(\App\Models\MedicalHistory::class,'client_id');
    }

    public function medicationInTakes(){
        return  $this->hasMany(\App\Models\MedicationInTake::class,'client_id');
    }

    public function friends(){
        return $this->belongsToMany(\App\Models\User::class, 'family_friend_assignments', 'client_id', 'familyfriend_id');
    }

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function transactions(){
        return $this->belongsTo(Transaction::class, 'client_id');
    }
}
