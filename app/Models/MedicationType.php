<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MedicationType extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'medication_type'
    ];

    protected static $logAttributes = [
        'medication_type',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on MedicationType.';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a MedicationType.";
    }
}
