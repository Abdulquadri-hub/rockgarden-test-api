<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;

class Receipt extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'currency', 'invoice_id', 'invoice_no', 'amount_paid', 'paid_by_user_id', 'payment_date',
    ];

    protected static $logAttributes = [
        'currency', 'invoice_id', 'invoice_no', 'amount_paid', 'paid_by_user_id', 'payment_date',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on Receipt.';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Receipt.";
    }

    public function invoice(){
        return  $this->belongsTo(\App\Models\Invoice::class,'invoice_id');
    }


    protected static function boot()
    {
        parent::boot();
        Receipt::saving(function ($model) {
            Receipt::updateInvoice($model->invoice_no);
        });

        Receipt::updating(function ($model) {
            Receipt::updateInvoice($model->invoice_no);
        });

        Receipt::deleting(function ($model) {
            Receipt::updateInvoice($model->invoice_no);
        });
    }

    public static function updateInvoice($invoice_no){
        $invoice =  Invoice::where('invoice_no', $invoice_no)->first();

        $totalPaid =  Receipt::where('invoice_no', $invoice_no)->sum('amount_paid');
        $invoice->total_amount_paid =  $totalPaid;
        $invoice->save();
        DB::commit();
    }
}

