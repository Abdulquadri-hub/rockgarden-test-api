<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ItemGroupDetail extends Model
{
    use HasFactory, LogsActivity;
    protected static $logFillable = true;

    protected static $logAttributes = [
        'group_id',
        'item_id',
        'created_at',
        'updated_at'
    ];

    // Customizing the log name
    protected static $logName = 'Action on Item Group Detail';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} an Item Group Detail";
    }

    protected $fillable = [
        'group_id', 'item_id'
    ];

    public function group(){
        return $this->belongsTo(\App\Models\ItemGroup::class, 'group_id');
    }

    public function item(){
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }
}
