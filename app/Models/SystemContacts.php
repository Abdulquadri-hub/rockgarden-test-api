<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SystemContacts extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'is_default' => 'boolean'
    ];

    protected static $logFillable = true;

    protected static $logAttributes = [
        'email',
        'phone',
        'name',
        'is_default',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on System Contacts';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a System Contacts";
    }

    protected $fillable = [
        'email',
        'phone',
        'name',
        'is_default'
    ];

    public function notificationSettings(){
        return $this->hasMany(\App\Models\NotificationSettings::class, 'system_contact_id');
    }
}
