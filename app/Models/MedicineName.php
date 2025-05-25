<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MedicineName extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'medicine_name'
    ];

    protected static $logAttributes = [
        'id',
        'medicine_name',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on MedicineName.';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a MedicineName.";
    }
}
