<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeRating extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'reviewer_name',
        'reviewer_id',
        'comment',
        'rating',
        'staff_id',
        'client_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Employee Rating';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Employee Rating";
    }

    public function staff(){
        return  $this->belongsTo(\App\Models\Employee::class,'staff_id');
    }

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }

    protected $fillable = [
        'reviewer_name',
        'reviewer_id',
        'comment',
        'rating',
        'staff_id',
        'client_id'
    ];
}
