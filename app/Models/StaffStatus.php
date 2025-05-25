<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StaffStatus extends Model
{
   use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id',
        'status',
        'last_chart_time',
        'last_attendance_time',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_chart_time' => 'datetime',
        'last_attendance_time' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the staff that owns the status.
     */
    public function staff()
    {
        return $this->belongsTo(Employee::class);
    }
}