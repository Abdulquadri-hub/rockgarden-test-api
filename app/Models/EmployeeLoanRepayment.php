<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeLoanRepayment extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'loan_id',
        'amount_paid',
        'payment_date',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Employee Loan Repayment';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Employee Loan Repayment";
    }

    protected $fillable = [
        'loan_id',
        'amount_paid',
        'payment_date'
    ];

    public function loan(){
        return $this->belongsTo(\App\Models\EmployeeLoan::class, 'loan_id');
    }
}
