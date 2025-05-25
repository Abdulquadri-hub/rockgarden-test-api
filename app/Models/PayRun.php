<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class PayRun extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
        'taxes' => 'array',
        'bonuses' => 'array',
    ];

    protected static $logAttributes = [
        'from_date',
        'to_date',
        'basic_salary',
        'title',
        'deductions',
        'allowances',
        'staff_id',
        'days_present',
        'bonuses',
        'taxes',
        'currency',
        'designation',
        'bank_name',
        'bank_account_number',
        'department',
        'duty_type',
        'staff_name',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Pay Runs';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Pay Run";
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (Auth::check()) {
            $activity->causer_id = Auth::user()->id;
        }
    }

    protected $fillable = [
        'from_date',
        'to_date',
        'title',
        'deductions',
        'allowances',
        'bonuses',
        'taxes',
        'staff_id',
        'bank_name',
        'bank_account_number',
        'department',
        'duty_type',
        'currency',
        'days_present',
        'designation',
        'staff_name',
        'basic_salary'
    ];

    public function staff(){
        return $this->belongsTo(\App\Models\Employee::class, 'staff_id');
    }
}
