<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class User extends Authenticatable
{
    use HasFactory, Notifiable ,HasApiTokens,HasRoles, LogsActivity;

    protected static $logFillable = true;

    protected static $logAttributes = [
        'first_name',
        'last_name',
        'phone_num',
        'email',
        'created_at',
        'updated_at'
    ];
    // Customizing the log name
    protected static $logName = 'Action on Users';

    // Customizing the description
    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} Users ";
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (Auth::check()) {
            $activity->causer_id = Auth::user()->id;
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'phone_num', 'email', 'password',
        'middle_name',
        'gender',
        'date_of_birth',
        'home_address',
        'office_address',
        'city',
        'state',
        'state_of_origin',
        'otp',
        'is_verified',
        'is_admin',
        'email_verified_at',
        'avatar',
        'file_img',
        'remember_token'
    ];

    public function employee(){
        return  $this->hasOne(\App\Models\Employee::class,'user_id');
    }
    
    public function messages()
    {
        return $this->belongsToMany(Message::class, 'message_recipients')
        ->withPivot('recipient_type', 'is_read', 'email')
        ->withTimestamps();
    }


    public function clients(){
        return  $this->hasOne(\App\Models\Client::class,'user_id');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }



    public function isAdmin() {
        return $this->is_admin === 1;
    }

    public function isUser() {
        return $this->is_admin === 0;
    }

    public function role()
    {
        return $this->roles()->with('permissions');
    }

    public function friendsClient(){
        return $this->belongsToMany(\App\Models\Client::class, 'family_friend_assignments', 'client_id', 'familyfriend_id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class, 'customer_user_id');
    }
}
