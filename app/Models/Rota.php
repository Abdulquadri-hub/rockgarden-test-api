<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Rota extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected $casts = [
        'is_present' => 'boolean',
    ];

    protected $fillable = [
        'is_present',
        'rota_date',
        'shift',
        'staff_id',
    ];

    protected static $logAttributes = [
        'is_present',
        'rota_date',
        'shift',
        'staff_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Rota';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Rota";
    }

    public function staff(){
        return  $this->belongsTo(\App\Models\Employee::class,'staff_id');
    }

}
