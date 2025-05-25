<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Stock extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'item_name',
        'item_category',
        'stock_level_before',
        'stock_level_after',
        'stock_entry',
        'created_by_user_id',
        'item_id',
        'unit',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Stock';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Stock";
    }

    protected $fillable = [
        'item_name',
        'item_category',
        'stock_level_before',
        'stock_level_after',
        'stock_entry',
        'created_by_user_id',
        'item_id',
        'unit'
    ];

    public function item(){
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }

    public function createdByUser(){
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

}
