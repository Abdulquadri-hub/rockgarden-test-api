<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Vendor extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'civility',
        'first_name',
        'last_name',
        'company_name',
        'vendor_email',
        'vendor_phone',
        'vendor_web_site',
        'remarks',
        'vendor_no',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Vendor';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Vendor";
    }

    protected $fillable = [
        'civility',
        'first_name',
        'last_name',
        'company_name',
        'vendor_email',
        'vendor_phone',
        'vendor_web_site',
        'remarks',
        'vendor_no'
    ];

    public function contacts(){
        return $this->hasMany(\App\Models\VendorContact::class, 'vendor_id');
    }

}
