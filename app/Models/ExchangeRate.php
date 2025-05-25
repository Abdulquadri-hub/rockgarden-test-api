<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;
     protected $table = 'exchange_rates';
    protected $fillable = [
        'currency_base',
        'currency_quote',
        'value',
      
    ];
    // public function getValueAttribute($value)
    // {
    //     return number_format($value, 10); 
    // }
}
