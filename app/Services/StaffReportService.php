<?php

namespace App\Services;

use App\Models\StaffReport;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Incident;
use App\Models\StaffChart;
use App\Models\PayRun;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffReportService
{
    public function generateMonthlyReport($staffId, $startDate, $endDate)
    {
        try {
            $staff = Employee::with('user')->find($staffId);
            if (!$staff) {
                throw new \Exception("Staff member not found with ID: {$staffId}");
            }

            // Calculate attendance data
            $attendanceData = $this->calculateAttendanceData($staffId, $startDate, $endDate);

            // Get incident data
            $incidentData = $this->getIncidentData($staffId, $startDate, $endDate);

            // Get staff chart data
            $staffChartData = $this->getStaffChartData($staffId, $startDate, $endDate);

            // Get payrun data
            $payrunData = $this->getPayrunData($staffId, $startDate, $endDate);

            // Get ratings data
            $ratingsData = $this->getRatingsData($staffId, $startDate, $endDate);

            // Generate summary notes
            $summaryNotes = $this->generateSummaryNotes($attendanceData, $incidentData, $staffChartData, $ratingsData);

            // Create or update staff report
            $report = StaffReport::updateOrCreate(
                [
                    'staff_id' => $staffId,
                    'report_start_date' => $startDate,
                    'report_end_date' => $endDate,
                ],
                [
                    'staff_name' => $staff->user->name ?? 'Unknown',
                    'employee_no' => $staff->employee_no,
                    'department' => $staff->department,
                    'designation' => $staff->designation,
                    'total_attendance_days' => $attendanceData['total_days'],
                    'total_working_days' => $attendanceData['working_days'],
                    'attendance_percentage' => $attendanceData['percentage'],
                    'total_incidents_reported' => $incidentData['total_count'],
                    'total_staff_charts_created' => $staffChartData['total_count'],
                    'average_rating' => $ratingsData['average_rating'],
                    'total_ratings_received' => $ratingsData['total_ratings'],
                    'attendance_details' => $attendanceData['details'],
                    'incident_details' => $incidentData['details'],
                    'staff_chart_details' => $staffChartData['details'],
                    'payrun_details' => $payrunData,
                    'summary_notes' => $summaryNotes,
                    'status' => 'generated'
                ]
            );

            Log::info( "this are the report". json_encode($report));
            Log::info("Staff report generated successfully for staff ID: {$staffId}");
            return $report;

        } catch (\Exception $e) {
            Log::error("Error generating staff report: " . $e->getMessage());
            throw $e;
        }
    }

    private function calculateAttendanceData($staffId, $startDate, $endDate)
    {
        $attendances = Attendance::where('staff_id', $staffId)
            ->whereBetween('time_checkin', [$startDate, $endDate])
            ->get();

        $totalDays = $attendances->count();
        $workingDays = Carbon::parse($startDate)->diffInWeekdays(Carbon::parse($endDate)) + 1;
        $percentage = $workingDays > 0 ? round(($totalDays / $workingDays) * 100, 2) : 0;

        $details = $attendances->map(function ($attendance) {
            return [
                'date' => Carbon::parse($attendance->time_checkin)->format('Y-m-d'),
                'checkin_time' => $attendance->time_checkin,
                'checkout_time' => $attendance->time_checkout,
                'category' => $attendance->category,
                'location_checkin' => [
                    'lat' => $attendance->latitude_checkin,
                    'lng' => $attendance->longitude_checkin
                ],
                'location_checkout' => [
                    'lat' => $attendance->latitude_checkout,
                    'lng' => $attendance->longitude_checkout
                ]
            ];
        })->toArray();

        return [
            'total_days' => $totalDays,
            'working_days' => $workingDays,
            'percentage' => $percentage,
            'details' => $details
        ];
    }

    private function getIncidentData($staffId, $startDate, $endDate)
    {
        $incidents = Incident::with('client')
            ->where('staff_id', $staffId)
            ->whereBetween('report_date', [$startDate, $endDate])
            ->get();

        $details = $incidents->map(function ($incident) {
            return [
                'id' => $incident->id,
                'title' => $incident->title,
                'description' => $incident->description,
                'client_name' => $incident->client->user->name ?? 'Unknown',
                'report_date' => $incident->report_date,
                'created_at' => $incident->created_at->format('Y-m-d H:i:s')
            ];
        })->toArray();

        return [
            'total_count' => $incidents->count(),
            'details' => $details
        ];
    }

    private function getStaffChartData($staffId, $startDate, $endDate)
    {
        $staffCharts = StaffChart::with('client')
            ->where('staff_id', $staffId)
            ->whereBetween('report_date', [$startDate, $endDate])
            ->get();

        $chartsByType = $staffCharts->groupBy('type');

        $details = [
            'by_type' => $chartsByType->map(function ($charts, $type) {
                return [
                    'type' => $type,
                    'count' => $charts->count(),
                    'charts' => $charts->map(function ($chart) {
                        return [
                            'id' => $chart->id,
                            'client_name' => $chart->client->user->name ?? 'Unknown',
                            'report_date' => $chart->report_date,
                            'comment' => $chart->comment
                        ];
                    })->toArray()
                ];
            })->values()->toArray(),
            'total_by_client' => $staffCharts->groupBy('client_id')->map(function ($charts, $clientId) {
                $client = $charts->first()->client;
                return [
                    'client_name' => $client->user->name ?? 'Unknown',
                    'total_charts' => $charts->count()
                ];
            })->values()->toArray()
        ];

        return [
            'total_count' => $staffCharts->count(),
            'details' => $details
        ];
    }

    private function getPayrunData($staffId, $startDate, $endDate)
    {
        $payruns = PayRun::where('staff_id', $staffId)
            ->whereBetween('from_date', [$startDate, $endDate])
            ->get();

        return $payruns->map(function ($payrun) {
            return [
                'id' => $payrun->id,
                'title' => $payrun->title,
                'from_date' => $payrun->from_date,
                'to_date' => $payrun->to_date,
                'basic_salary' => $payrun->basic_salary,
                'days_present' => $payrun->days_present,
                'allowances' => $payrun->allowances,
                'deductions' => $payrun->deductions,
                'bonuses' => $payrun->bonuses,
                'taxes' => $payrun->taxes,
                'currency' => $payrun->currency
            ];
        })->toArray();
    }

    private function getRatingsData($staffId, $startDate, $endDate)
    {
        $reviews = Review::where('staff_id', $staffId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalRatings = $reviews->count();
        $averageRating = $totalRatings > 0 ? round($reviews->avg('rating'), 2) : null;

        return [
            'total_ratings' => $totalRatings,
            'average_rating' => $averageRating,
            'reviews' => $reviews->map(function ($review) {
                return [
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('Y-m-d')
                ];
            })->toArray()
        ];
    }

    private function generateSummaryNotes($attendanceData, $incidentData, $staffChartData, $ratingsData)
    {
        $notes = [];

        // Attendance summary
        if ($attendanceData['percentage'] >= 95) {
            $notes[] = "Excellent attendance record with {$attendanceData['percentage']}% attendance rate.";
        } elseif ($attendanceData['percentage'] >= 80) {
            $notes[] = "Good attendance record with {$attendanceData['percentage']}% attendance rate.";
        } else {
            $notes[] = "Attendance needs improvement with {$attendanceData['percentage']}% attendance rate.";
        }

        // Incident summary
        if ($incidentData['total_count'] == 0) {
            $notes[] = "No incidents reported during this period.";
        } else {
            $notes[] = "Reported {$incidentData['total_count']} incident(s) during this period.";
        }

        // Staff chart summary
        $notes[] = "Created {$staffChartData['total_count']} staff chart entries.";

        // Rating summary
        if ($ratingsData['average_rating']) {
            if ($ratingsData['average_rating'] >= 4.5) {
                $notes[] = "Outstanding performance with average rating of {$ratingsData['average_rating']}/5.0.";
            } elseif ($ratingsData['average_rating'] >= 3.5) {
                $notes[] = "Good performance with average rating of {$ratingsData['average_rating']}/5.0.";
            } else {
                $notes[] = "Performance needs improvement with average rating of {$ratingsData['average_rating']}/5.0.";
            }
        }

        return implode(' ', $notes);
    }

    public function generateAllStaffReports($startDate, $endDate)
    {
        $staffMembers = Employee::with('user')->get();
        $generatedReports = [];

        foreach ($staffMembers as $staff) {
            try {
                $report = $this->generateMonthlyReport($staff->id, $startDate, $endDate);
                $generatedReports[] = $report;
            } catch (\Exception $e) {
                Log::error("Failed to generate report for staff ID {$staff->id}: " . $e->getMessage());
            }
        }

        return $generatedReports;
    }
}
