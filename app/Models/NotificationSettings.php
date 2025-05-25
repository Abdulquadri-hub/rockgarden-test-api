<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class NotificationSettings extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'send_sms' => 'boolean',
        'send_email' => 'boolean',
        'send_inapp' => 'boolean',
    ];

    protected static $logFillable = true;

    protected static $logAttributes = [
        'trigger_name',
        'send_sms',
        'send_email',
        'send_inapp',
        'created_at',
        'updated_at',
        'system_contact_id'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Notification Settings';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Notification Settings";
    }

    protected $fillable = [
        'trigger_name',
        'send_sms',
        'send_email',
        'send_inapp',
        'system_contact_id'
    ];

    public function systemContacts(){
        return $this->belongsTo(\App\Models\SystemContacts::class, 'system_contact_id');
    }
}
