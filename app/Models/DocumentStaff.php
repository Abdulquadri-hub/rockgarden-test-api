<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentStaff extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'staff_id',
        'doc_title',
        'doc_desc',
        'file_url',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Staff Document';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Staff Document";
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (Auth::check()) {
            $activity->causer_id = Auth::user()->id;
        }
    }

    protected $fillable = [
        'staff_id',
        'doc_title',
        'doc_desc',
        'file_url'
    ];

    public function staff(){
        return  $this->belongsTo(\App\Models\Employee::class,'staff_id');
    }
}
