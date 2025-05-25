<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StaffReport extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'staff_id',
        'staff_name',
        'employee_no',
        'department',
        'designation',
        'report_start_date',
        'report_end_date',
        'total_attendance_days',
        'total_working_days',
        'attendance_percentage',
        'total_incidents_reported',
        'total_staff_charts_created',
        'average_rating',
        'total_ratings_received',
        'attendance_details',
        'incident_details',
        'staff_chart_details',
        'payrun_details',
        'summary_notes',
        'pdf_path',
        'status'
    ];

    protected $casts = [
        'attendance_details' => 'array',
        'incident_details' => 'array',
        'staff_chart_details' => 'array',
        'payrun_details' => 'array',
        'report_start_date' => 'date',
        'report_end_date' => 'date',
    ];

    protected static $logAttributes = [
        'staff_id', 'staff_name', 'report_start_date', 'report_end_date',
        'total_attendance_days', 'attendance_percentage', 'status',
        'created_at', 'updated_at'
    ];

    protected static $logName = 'Action on Staff Report';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "You have {$eventName} a Staff Report for {$this->staff_name}";
    }

    public function staff()
    {
        return $this->belongsTo(Employee::class, 'staff_id');
    }
}
