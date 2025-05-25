<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Allowance extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'name',
        'amount',
        'designation_name',
        'currency',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Allowance';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Allowance";
    }

    protected $fillable = [
        'name', 'amount', 'currency', 'designation_name'
    ];

}
