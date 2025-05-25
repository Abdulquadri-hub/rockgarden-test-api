<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\StaffChart;
use App\Models\FlaggedTask;
use App\Models\StaffStatus;
use Carbon\Carbon;

class CheckStaffActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:check-activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check staff activity and flag those who have not been charting or have missed attendance beyond 8 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking staff activity...');
        
        $onDutyStaff = $this->getOnDutyStaff();
        
        $thresholdTime = Carbon::now()->subHours(8);
        $this->info('Threshold time set to: ' . $thresholdTime->format('Y-m-d H:i:s'));
        
        $flaggedCount = 0;
        
        foreach ($onDutyStaff as $staff) {
            $this->info("Checking staff ID: {$staff->id} ({$staff->name})");
            
            $lastCareNote = $this->checkChartingActivity($staff, $thresholdTime);
            
            $attendanceStatus = $this->checkAttendanceRecords($staff, $thresholdTime);
            
            if (!$lastCareNote || !$attendanceStatus) {
                $flaggedCount++;
            }
        }
        
        $this->info("Staff activity check completed. {$flaggedCount} staff members flagged.");
        
        return Command::SUCCESS;
    }
    

    private function getOnDutyStaff()
    {

        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        
        $staff = Employee::whereHas('attandences', function ($query) use ($today) {
            $query->whereDate('time_checkin', $today)
                  ->whereNull('time_checkout');
        })->get();
        
        $this->info("Found " . $staff->count() . " staff members currently on duty");
        
        // If you want to test with all staff (even those not on duty), 
        // you could use this instead:
        /*
        if ($staff->count() == 0) {
            $this->info("No staff currently on duty, checking all staff for testing purposes");
            $staff = Staff::all();
        }
        */
        
        return $staff;
    }
    

    private function checkChartingActivity($staff, $thresholdTime)
    {
     
        $latestChart = StaffChart::where('staff_id', $staff->id)
                                ->orderBy('created_at', 'desc')
                                ->first();
        
        $this->info("Latest chart for staff {$staff->id}: " . 
                    ($latestChart ? $latestChart->created_at->format('Y-m-d H:i:s') : 'None'));
        

        if (!$latestChart || $latestChart->created_at->lt($thresholdTime)) {
            $description = $latestChart 
                ? "No charting activity since " . $latestChart->created_at->format('Y-m-d H:i:s') . " (over 8 hours)"
                : "No charting activity found for this staff member";
                
            $this->createFlag($staff, 'charting', $description);
            
            $this->updateStaffStatus($staff, $latestChart ? $latestChart->created_at : null);
            
            return false;
        }
        
        return true;
    }
    

    private function checkAttendanceRecords($staff, $thresholdTime)
    {
        // Get the latest attendance record
        $latestAttendance = Attendance::where('staff_id', $staff->id)
                                      ->orderBy('created_at', 'desc')
                                      ->first();
        
        // Flag if no recent attendance record found
        if (!$latestAttendance) {
            $this->createFlag($staff, 'attendance', 'No attendance record found');
            return false;
        }
        
        $this->info("Latest attendance for staff {$staff->id}: Check-in: " . 
                   ($latestAttendance->time_checkin ? date('Y-m-d H:i:s', strtotime($latestAttendance->time_checkin)) : 'None') . 
                   ", Check-out: " . 
                   ($latestAttendance->time_checkout ? date('Y-m-d H:i:s', strtotime($latestAttendance->time_checkout)) : 'None'));
        
        $checkinTime = Carbon::parse($latestAttendance->time_checkin);
        
        if (!$latestAttendance->time_checkout && $checkinTime->lt($thresholdTime)) {
            $this->createFlag(
                $staff, 
                'attendance', 
                'Check-in at ' . $checkinTime->format('Y-m-d H:i:s') . ' (over 8 hours ago) with no check-out'
            );
            return false;
        }
        

        if ($latestAttendance->time_checkout) {
            $checkoutTime = Carbon::parse($latestAttendance->time_checkout);

            $chartActivity = StaffChart::where('staff_id', $staff->id)
                                      ->where('created_at', '>=', $checkinTime)
                                      ->where('created_at', '<=', $checkoutTime)
                                      ->exists();
            
            if (!$chartActivity) {
                $this->createFlag(
                    $staff, 
                    'attendance', 
                    'No charting activity during shift from ' . 
                    $checkinTime->format('Y-m-d H:i:s') . ' to ' . 
                    $checkoutTime->format('Y-m-d H:i:s')
                );
                return false;
            }
        }
        
        return true;
    }
    

    private function createFlag($staff, $taskType, $description)
    {
        $existingFlag = FlaggedTask::where('staff_id', $staff->id)
                                  ->where('task_type', $taskType)
                                  ->where('status', 'pending')
                                  ->first();
        

        if (!$existingFlag) {
            $flag = FlaggedTask::create([
                'staff_id' => $staff->id,
                'task_type' => $taskType,
                'flag_color' => 'yellow',
                'status' => 'pending',
                'description' => $description,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $this->info("Created {$taskType} flag for staff ID: {$staff->id} - {$description}");
            
            // Trigger notification to managers if needed
            // $this->notifyManagers($flag);
            
            return $flag;
        }
        
        $this->info("Flag already exists for staff ID: {$staff->id}, task type: {$taskType}");
        return $existingFlag;
    }


    private function updateStaffStatus($staff, $lastChartTime)
    {
        $staffStatus = StaffStatus::firstOrNew(['staff_id' => $staff->id]);
        
        // Get the latest attendance
        $latestAttendance = Attendance::where('staff_id', $staff->id)
                                     ->orderBy('created_at', 'desc')
                                     ->first();
        
        $staffStatus->status = 'on duty';
        $staffStatus->last_chart_time = $lastChartTime;
        $staffStatus->last_attendance_time = $latestAttendance ? $latestAttendance->time_checkin : null;
        $staffStatus->updated_at = Carbon::now();
        $staffStatus->save();
    }
}