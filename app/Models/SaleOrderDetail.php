<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SaleOrderDetail extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'stock_id',
        'order_id',
        'item_id',
        'group_item_id',
        'quantity',
        'discount',
        'amount',
        'tax_id',
        'currency',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Sale Order Detail';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Sale Order Detail";
    }

    protected $fillable = [
        'stock_id',
        'order_id',
        'item_id',
        'group_item_id',
        'quantity',
        'discount',
        'amount',
        'tax_id',
        'currency'
    ];

    public function stock(){
        return $this->belongsTo(\App\Models\Stock::class, 'stock_id');
    }

    public function order(){
        return $this->belongsTo(\App\Models\SaleOrder::class, 'order_id');
    }

    public function itemGroup(){
        return $this->belongsTo(\App\Models\ItemGroup::class, 'group_item_id');
    }

    public function item(){
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }

    public function taxes(){
        return $this->belongsTo(\App\Models\Tax::class, 'tax_id');
    }
}
