<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FlaggedTask extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id',
        'task_type',
        'flag_color',
        'status',
        'description',
        'created_at',
        'updated_at',
        'resolved_at',
        'resolved_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the staff that owns the flagged task.
     */
    public function staff()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who resolved the task.
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}