<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FamilyFriendAssignment extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'client_id',
        'familyfriend_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Family Friend Assignment';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} Family Friend Assignment";
    }

    protected $fillable = [
        'client_id',
        'familyfriend_id'
    ];

    public function friend(){
        return  $this->belongsTo(\App\Models\User::class,'familyfriend_id');
    }
    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }

}
