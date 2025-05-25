<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'invoice_id',
        'invoice_ids',
        'customer_user_id',
        'customer_email',
        'payment_name',
        'amount',
        'currency',
        'authorization_url',
        'access_code',
        'reference',
        'status',
        'gateway_response',
        'charge_attempted',
        'transaction_date',
        'save_card_auth',
        'is_flutterwave',
        'link',
        'client_id',
        'client_name',
        'created_at',
        'updated_at'
    ];
protected $casts = [
        'invoice_ids' => 'json',
    ];
    // Customizing the log name
    protected static $logName = 'Action on Transactions';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Transactions";
    }

    protected $fillable = [
        'invoice_id',
        'invoice_ids',
        'customer_user_id',
        'customer_email',
        'payment_name',
        'amount',
        'currency',
        'authorization_url',
        'access_code',
        'reference',
        'status',
        'gateway_response',
        'charge_attempted',
        'transaction_date',
        'save_card_auth',
        'is_flutterwave',
        'link',
        'client_id',
        'client_name'
    ];

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer_user_id');
    }
}
