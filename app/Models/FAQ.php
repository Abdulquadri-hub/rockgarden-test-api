<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FAQ extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'question',
        'answer',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on FAQ';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} FAQ.";
    }

    protected $fillable = [
        'question',
        'answer'
    ];
}
