<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

// Deleted
class PayRunBonuses extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'pay_run_id',
        'bonus_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on  Pay Run Bonus';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} Pay Run Bonus";
    }

    protected $fillable = [
        'pay_run_id',
        'bonus_id'
    ];

    public function bonus(){
        return  $this->belongsTo(\App\Models\Bonus::class,'bonus_id');
    }

    public function payRun(){
        return  $this->belongsTo(\App\Models\PayRun::class,'pay_run_id');
    }
}
