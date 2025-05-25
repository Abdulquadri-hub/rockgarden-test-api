<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MedicationInTake extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'client_id', 'medication_date', 'medicine_name', 'dosage', 'dosage_given', 'status',
    ];

    protected static $logAttributes = [
        'client_id', 'medication_date', 'medicine_name', 'dosage', 'dosage_given', 'status',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on MedicationInTake.';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a MedicationInTake.";
    }

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }
}
