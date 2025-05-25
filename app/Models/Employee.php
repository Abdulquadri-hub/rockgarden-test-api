<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'average_rating',
        'total_ratings',
        'duty_type',
        'employee_no',
        'nationality',
        'national_identification_number',
        'department',
        'designation',
        'user_id',
        'bank_account_number',
        'bank_name',
        'date_employed',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Employee';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Employee";
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (Auth::check()) {
            $activity->causer_id = Auth::user()->id;
        }
    }

    protected $fillable = [
        'average_rating',
        'total_ratings',
        'duty_type',
        'employee_no',
        'nationality',
        'national_identification_number',
        'department',
        'designation',
        'user_id',
        'bank_account_number',
        'bank_name',
        'date_employed',
    ];

    public function user(){
        return  $this->belongsTo(\App\Models\User::class,'user_id');
    }

    public function attandences(){
        return $this->hasMany(\App\Models\Attendance::class, 'staff_id');
    }
}
