<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MedicalHistory extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'client_id', 'initial_date_of_occurrence', 'date_description', 'medical_history_details'
    ];

    protected static $logAttributes = [
        'client_id', 'initial_date_of_occurrence', 'date_description', 'medical_history_details',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on MedicalHistory.';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a MedicalHistory.";
    }

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }
}
