<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class InAppNotifications extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'owner_ids' => 'array',
    ];

    protected static $logFillable = true;

    protected static $logAttributes = [
        'id',
        'title',
        'message',
        'owner_ids',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on InApp Notification';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an InApp Notification";
    }

    protected $fillable = [
        'title',
        'message',
        'owner_ids',
    ];
}
