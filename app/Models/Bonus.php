<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Bonus extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'name',
        'amount',
        'currency',
        'designation_name',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Bonus';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Bonus";
    }

    protected $fillable = [
        'name', 'amount', 'currency', 'designation_name'
    ];

}
