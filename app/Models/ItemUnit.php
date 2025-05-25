<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ItemUnit extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'id',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Item Unit';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Item Unit";
    }

    protected $fillable = [
        'name', 'description'
    ];

}
