<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class VendorContact extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'vendor_id',
        'contact_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Vendor Contact';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Vendor Contact";
    }

    protected $fillable = [
        'vendor_id', 'contact_id'
    ];

    public function vendor(){
        return $this->belongsTo(\App\Models\Vendor::class, 'vendor_id');
    }

    public function contact(){
        return $this->belongsTo(\App\Models\Contact::class, 'contact_id');
    }
}
