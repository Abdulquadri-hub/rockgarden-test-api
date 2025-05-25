<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Shift extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'shift_name', 'start_time', 'end_time',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on Shift';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Shift ";
    }

    protected $fillable =[
        'shift_name', 'start_time', 'end_time'
    ];
}
