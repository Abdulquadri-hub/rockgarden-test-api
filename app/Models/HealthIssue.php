<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class HealthIssue extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'title',
        'description',
        'review_frequency',
        'initial_treatment_plan',
        'closed_reason',
        'recorded_by_staff_id',
        'closed_by_user_id',
        'start_date',
        'closed_date',
        'image1',
        'image2',
        'image3',
        'client_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Health Issue';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Health Issue";
    }

    public function staff(){
        return  $this->belongsTo(\App\Models\Employee::class,'recorded_by_staff_id');
    }

    public function close_user(){
        return  $this->belongsTo(\App\Models\User::class,'closed_by_user_id');
    }

    protected $fillable = [
        'title',
        'description',
        'review_frequency',
        'initial_treatment_plan',
        'closed_reason',
        'recorded_by_staff_id',
        'closed_by_user_id',
        'start_date',
        'closed_date',
        'image1',
        'image2',
        'image3',
        'client_id'
    ];

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }
}
