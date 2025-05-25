<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Attendance extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'category',
        'latitude_checkin',
        'latitude_checkout',
        'longitude_checkin',
        'longitude_checkout',
        'device_checkin',
        'device_checkout',
        'time_checkin',
        'time_checkout',
        'staff_id'
    ];

    protected static $logAttributes = [
        'latitude_checkin',
        'latitude_checkout',
        'longitude_checkin',
        'longitude_checkout',
        'device_checkin',
        'device_checkout',
        'time_checkin',
        'time_checkout',
        'staff_id',
        'category',
        'created_at',
        'updated_at'
    ];


    // Customizing the log name
    protected static $logName = 'Action on Attendance.';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Attendance.";
    }

    public function staff(){
        return $this->belongsTo(\App\Models\Employee::class, 'staff_id');
    }
}
