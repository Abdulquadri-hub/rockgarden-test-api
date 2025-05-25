<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StaffAssignment extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'client_id',
        'staff_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Staff Assignment';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Staff Assignment";
    }

    protected $fillable = [
        'client_id',
        'staff_id'
    ];

    public function staff(){
        return  $this->belongsTo(\App\Models\Employee::class,'staff_id');
    }
    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }

}
