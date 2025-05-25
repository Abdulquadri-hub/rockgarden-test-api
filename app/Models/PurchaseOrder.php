<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseOrder extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'vendor_id',
        'order_no',
        'reference',
        'order_date',
        'shipment_date',
        'shipment_preference',
        'status',
        'client_id',
        'staff_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Purchase Order';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Purchase Order";
    }

    protected $fillable = [
        'vendor_id',
        'order_no',
        'reference',
        'order_date',
        'shipment_date',
        'shipment_preference',
        'discount',
        'status',
        'invoiced',
        'payment',
        'delivery_method',
        'staff_id',
        'uploaded_file',
        'terms',
        'notes',
        'client_id',
        'total',
        'adjustment'
    ];

    public function vendor(){
        return $this->belongsTo(\App\Models\Vendor::class, 'vendor_id');
    }

    public function staff(){
        return $this->belongsTo(\App\Models\Employee::class, 'staff_id');
    }

    public function client(){
        return $this->belongsTo(\App\Models\Client::class, 'client_id');
    }

    public function itemList(){
        return $this->hasMany(\App\Models\PurchaseOrderDetail::class, 'group_item_id');
    }

    public function itemGroupList(){
        return $this->hasMany(\App\Models\PurchaseOrder::class, 'item_id');
    }
}
