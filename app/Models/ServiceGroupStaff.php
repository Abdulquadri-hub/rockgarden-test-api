<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceGroupStaff extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'group_id',
        'staff_id',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on Service Group Staff';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Service Group Staff ";
    }

    protected $fillable = [
        'group_id',
        'staff_id'
    ];

    public function staffs(){
        return $this->belongsToMany(\App\Models\Employee::class, 'service_group_staff', 'group_id', 'staff_id');
    }
}
