<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected $fillable = [
        'total_amount_paid',
        'payment_amount',
        'currency',
        'due_date',
        'payment_description',
        'is_monthly_recurrent', 'next_charge_date', 'client_id',
    ];

    protected static $logAttributes = [
        'total_amount_paid',
        'currency',
        'due_date',
        'payment_description',
        'is_monthly_recurrent', 'next_charge_date', 'client_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Invoice';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Invoice";
    }

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }

    public function receipts(){
        return  $this->hasMany(\App\Models\Receipt::class,'invoice_id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class, 'invoice_id');
    }
}
