<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeSalary extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'designation_name',
        'duty_type',
        'basic_salary',
        'basic_salary_per_day',
        'currency',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Employee Salary Configuration';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Employee Salary Configuration";
    }

    protected $fillable = [
        'designation_name',
        'duty_type',
        'basic_salary',
        'basic_salary_per_day',
        'currency',
    ];

}
