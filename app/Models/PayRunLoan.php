<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

// Deleted
class PayRunLoan extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'pay_run_id',
        'loan_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Pay Run Loan';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} Family Pay Run Loan";
    }

    protected $fillable = [
        'pay_run_id',
        'loan_id'
    ];

    public function bonus(){
        return  $this->belongsTo(\App\Models\Loan::class,'loan_id');
    }
    public function payRun(){
        return  $this->belongsTo(\App\Models\PayRun::class,'pay_run_id');
    }
}
