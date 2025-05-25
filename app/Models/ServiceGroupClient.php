<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceGroupClient extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'group_id',
        'client_id',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on Service Group Client';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Service Group Client ";
    }

    protected $fillable = [
        'group_id',
        'client_id'
    ];

    public function clients(){
        return $this->belongsToMany(\App\Models\Client::class, 'service_group_clients', 'group_id', 'client_id');
    }
}
