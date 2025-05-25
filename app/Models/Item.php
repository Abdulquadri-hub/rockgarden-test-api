<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Item extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'name',
        'dimension',
        'weight_kg',
        'manufacturer',
        'brand',
        'cost_price',
        'sale_price',
        'sku',
        'reorder_level',
        'vendor_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Item';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Item";
    }

    protected $fillable = [
        'name',
        'type',
        'unit',
        'image1',
        'image2',
        'image3',
        'returnable',
        'dimension',
        'weight_kg',
        'manufacturer',
        'brand',
        'cost_price',
        'sale_price',
        'currency',
        'description',
        'category_name',
        'sku',
        'reorder_level',
        'vendor_id',
        'current_stock_level',
    ];

    public function vendor(){
        return $this->belongsTo(\App\Models\Vendor::class, 'vendor_id');
    }
}
