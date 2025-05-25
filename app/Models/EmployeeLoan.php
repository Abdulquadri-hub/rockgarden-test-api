<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeLoan extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'staff_id',
        'name',
        'currency',
        'amount',
        'disbursement_date',
        'repayment_start_date',
        'installment_amount',
        'reason',
        'state',
        'overdue_date',
        'interest_rate',
        'total_amount_paid',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Employee Loan';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Employee Loan";
    }

    protected $fillable = [
        'staff_id',
        'name',
        'currency',
        'amount',
        'disbursement_date',
        'repayment_start_date',
        'installment_amount',
        'overdue_date',
        'interest_rate',
        'total_amount_paid',
        'reason',
        'state'
    ];

    public function staff(){
        return $this->belongsTo(\App\Models\Employee::class, 'staff_id');
    }

    public function loanRepayment(){
        return $this->hasMany(\App\Models\EmployeeLoanRepayment::class, 'loan_id');
    }
}
