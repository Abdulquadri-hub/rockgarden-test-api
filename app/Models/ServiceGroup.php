<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceGroup extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'group_name'
    ];

    protected static $logFillable = true;

    protected static $logAttributes = [
        'group_name',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on Service Group';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Service Group ";
    }

    public function staffGroups(){
        return $this->hasMany(\App\Models\ServiceGroupStaff::class, 'group_id');
    }

    public function clientGroups(){
        return $this->hasMany(\App\Models\ServiceGroupClient::class, 'group_id');
    }
}
