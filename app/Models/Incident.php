<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Incident extends Model
{
    use HasFactory,LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'id',
        'title',
        'description',
        'staff_id',
        'staff_present_id',
        'client_id',
        'media1',
        'media2',
        'media3',
        'media4',
        'report_date',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Item Category';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Item Category";
    }

    protected $fillable = [
        'title',
        'description',
        'staff_id',
        'staff_present_id',
        'client_id',
        'media1',
        'media2',
        'media3',
        'media4',
        'report_date'
    ];
    public function staff(){
        return  $this->belongsTo(\App\Models\Employee::class,'staff_id');
    }

    public function staff_present(){
        return  $this->belongsTo(\App\Models\Employee::class,'staff_present_id');
    }

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }
}
