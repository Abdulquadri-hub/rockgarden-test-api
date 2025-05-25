<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeePayRun extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
    ];

    protected static $logFillable = true;

    protected static $logAttributes = [
        'staff_id',
        'basic_salary',
        'basic_salary_currency',
        'payment_date',
        'status',
        'deductions',
        'reimbursement',
        'reimbursement_currency',
        'reimbursement_info',
        'pay_days',
        'allowances',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Employee Pay Run';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Employee Pay Run";
    }

    protected $fillable = [
        'staff_id',
        'basic_salary',
        'basic_salary_currency',
        'payment_date',
        'status',
        'deductions',
        'reimbursement',
        'reimbursement_currency',
        'reimbursement_info',
        'pay_days',
        'allowances'
    ];

    public function staff(){
        return $this->belongsTo(\App\Models\Employee::class, 'staff_id');
    }
}
