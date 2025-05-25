<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Service extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected $fillable = [

    ];
    protected static $logAttributes = [
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Service';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Service";
    }
}
