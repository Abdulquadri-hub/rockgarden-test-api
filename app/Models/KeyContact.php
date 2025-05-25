<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class KeyContact extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'client_id',
        'fullname',
        'relationship',
        'home_address',
        'email_address',
        'phone_number',
        'is_primary',
    ];

    protected static $logAttributes = [
        'client_id',
        'fullname',
        'relationship',
        'home_address',
        'email_address',
        'phone_number',
        'is_primary',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on KeyContact.';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a KeyContact.";
    }

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }
}
