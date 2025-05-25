<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SaleOrder extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected $casts =[
        'invoiced' => 'boolean',
    ];

    protected static $logAttributes = [
        'order_no',
        'order_date',
        'invoiced',
        'created_by_user_id',
        'total_amount',
        'client_id',
        'item_id',
        'item_unit',
        'item_name',
        'total_order',
        'item_currency',
        'price_per_unit',
        'order_details',
        'invoice_no',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Sale Order';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Sale Order";
    }

    protected $fillable = [
        'order_no',
        'order_date',
        'invoiced',
        'created_by_user_id',
        'total_amount',
        'client_id',
        'item_id',
        'item_unit',
        'item_name',
        'total_order',
        'item_currency',
        'price_per_unit',
        'order_details',
        'invoice_no'
    ];


    public function createdByUser(){
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    public function client(){
        return $this->belongsTo(\App\Models\Client::class, 'client_id');
    }

    public function item(){
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }

}
