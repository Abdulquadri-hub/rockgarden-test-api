<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffReport;
use App\Models\Employee;
use App\Services\StaffReportPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StaffReportController extends Controller
{
    private $pdfService;

    public function __construct(StaffReportPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_name' => 'nullable|string|max:255',
            'employee_no' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
            'status' => 'nullable|in:generated,downloaded,archived',
            'per_page' => 'nullable|integer|between:10,100',
            'sort_by' => 'nullable|in:staff_name,department,report_start_date,attendance_percentage,created_at',
            'sort_order' => 'nullable|in:asc,desc'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = StaffReport::with('staff.user');

            if ($request->filled('staff_name')) {
                $query->where('staff_name', 'LIKE', '%' . $request->staff_name . '%');
            }

            if ($request->filled('employee_no')) {
                $query->where('employee_no', 'LIKE', '%' . $request->employee_no . '%');
            }

            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->where(function($q) use ($request) {
                    $q->whereBetween('report_start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('report_end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q2) use ($request) {
                          $q2->where('report_start_date', '<=', $request->start_date)
                             ->where('report_end_date', '>=', $request->end_date);
                      });
                });
            }

            if ($request->filled('month') && $request->filled('year')) {
                $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($request->year, $request->month, 1)->endOfMonth();

                $query->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('report_start_date', [$startDate, $endDate])
                      ->orWhereBetween('report_end_date', [$startDate, $endDate])
                      ->orWhere(function($q2) use ($startDate, $endDate) {
                          $q2->where('report_start_date', '<=', $startDate)
                             ->where('report_end_date', '>=', $endDate);
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'report_start_date');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Add secondary sort for consistency
            if ($sortBy !== 'created_at') {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->input('per_page', 15);
            $reports = $query->paginate($perPage);

            // $reports->getCollection()->transform(function ($report) {
            //     $report->period_display = $report->report_start_date->format('M j, Y') . ' - ' . $report->report_end_date->format('M j, Y');
            //     $report->has_pdf = !empty($report->pdf_path) && Storage::exists($report->pdf_path);
            //     $report->attendance_status = $this->getAttendanceStatus($report->attendance_percentage);
            //     $report->performance_status = $this->getPerformanceStatus($report->average_rating);
            //     return $report;
            // });

            return response()->json([
                'success' => true,
                'data' => $reports,
                'message' => 'Staff reports retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving staff reports: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $report = StaffReport::with('staff.user')->find($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff report not found'
                ], 404);
            }

            $report->period_display = $report->report_start_date->format('M j, Y') . ' - ' . $report->report_end_date->format('M j, Y');
            $report->has_pdf = !empty($report->pdf_path) && Storage::exists($report->pdf_path);
            $report->attendance_status = $this->getAttendanceStatus($report->attendance_percentage);
            $report->performance_status = $this->getPerformanceStatus($report->average_rating);

            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Staff report retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving staff report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPdf($id)
    {
        try {
            $report = StaffReport::find($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff report not found'
                ], 404);
            }

            if (!$report->pdf_path || !Storage::exists($report->pdf_path)) {
                $this->pdfService->generatePdf($report);
                $report->refresh();
            }

            // Update status to downloaded if it was only generated
            if ($report->status === 'generated') {
                $report->update(['status' => 'downloaded']);
            }

            $filename = "staff-report-{$report->staff_name}-{$report->report_start_date->format('M-Y')}.pdf";

            // return Storage::download($report->pdf_path, $filename);

             $url = Storage::url($report->pdf_path);

            return response()->json([
                'success' => true,
                'url' => asset($url)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStaffOptions()
    {
        try {
            $staff = Employee::with('user:id,first_name')
                ->select('id', 'employee_no', 'department', 'designation', 'user_id')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->user?->first_name ?? 'Unknown',
                        'employee_no' => $employee->employee_no,
                        'department' => $employee->department,
                        'designation' => $employee->designation
                    ];
                });

            // Get unique departments for filter dropdown
            $departments = StaffReport::select('department')
                ->distinct()
                ->whereNotNull('department')
                ->orderBy('department')
                ->pluck('department');

            return response()->json([
                'success' => true,
                'data' => [
                    'staff' => $staff,
                    'departments' => $departments
                ],
                'message' => 'Staff options retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving staff options: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'department' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfYear());
            $endDate = $request->input('end_date', Carbon::now()->endOfYear());

            $query = StaffReport::whereBetween('report_start_date', [$startDate, $endDate]);

            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }

            $stats = [
                // Overview statistics
                'overview' => [
                    'total_reports' => $query->count(),
                    'total_staff' => $query->distinct('staff_id')->count('staff_id'),
                    'average_attendance' => round($query->avg('attendance_percentage'), 2),
                    'total_incidents' => $query->sum('total_incidents_reported'),
                    'total_staff_charts' => $query->sum('total_staff_charts_created'),
                    'reports_with_ratings' => $query->whereNotNull('average_rating')->count()
                ],

                'monthly_data' => StaffReport::selectRaw('
                        YEAR(report_start_date) as year,
                        MONTH(report_start_date) as month,
                        MONTHNAME(MIN(report_start_date)) as month_name,
                        COUNT(*) as total_reports,
                        AVG(attendance_percentage) as avg_attendance,
                        SUM(total_incidents_reported) as total_incidents,
                        AVG(average_rating) as avg_rating
                    ')
                    ->whereBetween('report_start_date', [$startDate, $endDate])
                    ->when($request->filled('department'), function($q) use ($request) {
                        return $q->where('department', $request->department);
                    })
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get(),

                // Department breakdown
                'department_stats' => StaffReport::selectRaw('
                        department,
                        COUNT(*) as total_reports,
                        COUNT(DISTINCT staff_id) as unique_staff,
                        AVG(attendance_percentage) as avg_attendance,
                        SUM(total_incidents_reported) as total_incidents,
                        AVG(average_rating) as avg_rating
                    ')
                    ->whereBetween('report_start_date', [$startDate, $endDate])
                    ->when($request->filled('department'), function($q) use ($request) {
                        return $q->where('department', $request->department);
                    })
                    ->groupBy('department')
                    ->orderBy('total_reports', 'desc')
                    ->get(),

                // Performance distribution
                'performance_distribution' => [
                    'attendance' => [
                        'excellent' => $query->where('attendance_percentage', '>=', 95)->count(),
                        'good' => $query->whereBetween('attendance_percentage', [80, 94.99])->count(),
                        'needs_improvement' => $query->where('attendance_percentage', '<', 80)->count()
                    ],
                    'ratings' => [
                        'outstanding' => $query->where('average_rating', '>=', 4.5)->count(),
                        'good' => $query->whereBetween('average_rating', [3.5, 4.49])->count(),
                        'needs_improvement' => $query->where('average_rating', '<', 3.5)->whereNotNull('average_rating')->count(),
                        'no_ratings' => $query->whereNull('average_rating')->count()
                    ]
                ],

                // Top/Bottom performers
                'top_performers' => [
                    'attendance' => StaffReport::select('staff_name', 'department', 'attendance_percentage', 'report_start_date')
                        ->whereBetween('report_start_date', [$startDate, $endDate])
                        ->when($request->filled('department'), function($q) use ($request) {
                            return $q->where('department', $request->department);
                        })
                        ->orderBy('attendance_percentage', 'desc')
                        ->limit(5)
                        ->get(),
                    'ratings' => StaffReport::select('staff_name', 'department', 'average_rating', 'total_ratings_received', 'report_start_date')
                        ->whereBetween('report_start_date', [$startDate, $endDate])
                        ->whereNotNull('average_rating')
                        ->when($request->filled('department'), function($q) use ($request) {
                            return $q->where('department', $request->department);
                        })
                        ->orderBy('average_rating', 'desc')
                        ->limit(5)
                        ->get()
                ],

                // Report status distribution
                'status_distribution' => StaffReport::selectRaw('status, COUNT(*) as count')
                    ->whereBetween('report_start_date', [$startDate, $endDate])
                    ->when($request->filled('department'), function($q) use ($request) {
                        return $q->where('department', $request->department);
                    })
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status')
                    ->toArray()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'filters' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'department' => $request->input('department')
                ],
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailablePeriods()
    {
        try {
            $periods = StaffReport::selectRaw('
                    YEAR(report_start_date) as year,
                    MONTH(report_start_date) as month,
                    MONTHNAME(report_start_date) as month_name,
                    COUNT(*) as report_count
                ')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $periods,
                'message' => 'Available periods retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving available periods: ' . $e->getMessage()
            ], 500);
        }
    }

    public function archiveOldReports(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'before_date' => 'required|date|before:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $archivedCount = StaffReport::where('report_end_date', '<', $request->before_date)
                ->where('status', '!=', 'archived')
                ->update(['status' => 'archived']);

            return response()->json([
                'success' => true,
                'data' => ['archived_count' => $archivedCount],
                'message' => "Successfully archived {$archivedCount} reports"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving reports: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAttendanceStatus($percentage)
    {
        if ($percentage >= 95) return 'excellent';
        if ($percentage >= 80) return 'good';
        return 'needs_improvement';
    }

    private function getPerformanceStatus($rating)
    {
        if (!$rating) return 'no_rating';
        if ($rating >= 4.5) return 'outstanding';
        if ($rating >= 3.5) return 'good';
        return 'needs_improvement';
    }
}
