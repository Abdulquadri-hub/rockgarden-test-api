<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ItemGroup extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'group_name',
        'dimension',
        'weight_kg',
        'manufacturer',
        'brand',
        'cost_price',
        'sale_price',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Item Group';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Item Group";
    }

    protected $fillable = [
        'group_name',
        'taxable',
        'attributes',
        'type',
        'unit',
        'images',
        'returnable',
        'dimension',
        'weight_kg',
        'manufacturer',
        'brand',
        'cost_price',
        'sale_price',
        'currency',
        'description'
    ];

    public function items(){
        return $this->hasMany(\App\Models\ItemGroupDetail::class, 'group_id');
    }
}
