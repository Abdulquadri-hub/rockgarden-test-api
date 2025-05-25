<?php

namespace App\Models;

use App\Http\Controllers\Api\TwilioSMSController;
use App\Mail\DeathRecordAdminMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Traits\LogsActivity;

class DeathRecord extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'cause_of_death', 'date_of_death', 'time_of_death', 'client_id'
    ];

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }

    protected static $logAttributes = [
        'cause_of_death', 'date_of_death', 'time_of_death', 'client_id',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on DeathRecord.';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a DeathRecord.";
    }

    protected static function boot()
    {
        parent::boot();

        parent::boot();
        DeathRecord::saving(function ($model) {
            DeathRecord::notify($model->client_id);
        });
    }

    public static function notify($client_id){
        //// This is wrongly implemented! Mails should go to congifured email addresses.
        
        // $client = Client::where('id', $client_id)->first();
        // $users =  User::role('Administrators')->get();

        // foreach ($users as $user){
        //     // Send Email to new user
        //     Mail::to($user->email)->send(new DeathRecordAdminMail($client->user->first_name.' '.$client->user->last_name, env('DASHBOARD_LINK')));

        //     // Send Sms to user
        //     TwilioSMSController::sendSMS($user->phone_num, TwilioSMSController::deathRecordAdminMessage($client->user->first_name.' '.$client->user->last_name, env('DASHBOARD_LINK')));
        // }

    }
}
