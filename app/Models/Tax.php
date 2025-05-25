<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Tax extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'is_fixed' => 'boolean',
    ];

    protected static $logFillable = true;

    protected static $logAttributes = [
        'name',
        'amount',
        'currency',
        'is_fixed',
        'percentage',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Tax';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Tax";
    }

    protected $fillable = [
        'percentage', 'name', 'amount', 'currency', 'is_fixed'
    ];

}
