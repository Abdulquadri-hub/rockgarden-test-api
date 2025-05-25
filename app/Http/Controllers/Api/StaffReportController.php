<?php

use App\Http\Controllers\Controller;
use App\Models\StaffReport;
use App\Models\Employee;
use App\Services\StaffReportService;
use App\Services\StaffReportPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StaffReportController extends Controller
{
    private $staffReportService;
    private $pdfService;

    public function __construct(StaffReportService $staffReportService, StaffReportPdfService $pdfService)
    {
        $this->staffReportService = $staffReportService;
        $this->pdfService = $pdfService;
    }

    /**
     * Search for staff reports
     */
    public function searchReports(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'nullable|exists:employees,id',
            'staff_name' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020',
            'per_page' => 'nullable|integer|between:10,100'
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

            // Filter by staff ID
            if ($request->filled('staff_id')) {
                $query->where('staff_id', $request->staff_id);
            }

            // Filter by staff name
            if ($request->filled('staff_name')) {
                $query->where('staff_name', 'LIKE', '%' . $request->staff_name . '%');
            }

            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('report_start_date', [$request->start_date, $request->end_date]);
            }

            // Filter by month and year
            if ($request->filled('month') && $request->filled('year')) {
                $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($request->year, $request->month, 1)->endOfMonth();
                $query->whereBetween('report_start_date', [$startDate, $endDate]);
            }

            $perPage = $request->input('per_page', 15);
            $reports = $query->orderBy('report_start_date', 'desc')->paginate($perPage);

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

    /**
     * Get specific staff report
     */
    public function getReport($id)
    {
        try {
            $report = StaffReport::with('staff.user')->find($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff report not found'
                ], 404);
            }

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

    /**
     * Generate report for specific staff and period
     */
    public function generateReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $report = $this->staffReportService->generateMonthlyReport(
                $request->staff_id,
                $request->start_date,
                $request->end_date
            );

            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Staff report generated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating staff report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download report as PDF
     */
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

            // Generate PDF if not exists
            if (!$report->pdf_path || !Storage::exists($report->pdf_path)) {
                $this->pdfService->generatePdf($report);
            }

            // Update status to downloaded
            $report->update(['status' => 'downloaded']);

            $filename = "staff-report-{$report->staff_name}-{$report->report_start_date->format('M-Y')}.pdf";

            return Storage::download($report->pdf_path, $filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all staff members for dropdown
     */
    public function getStaffList()
    {
        try {
            $staff = Employee::with('user:id,name')
                ->select('id', 'employee_no', 'department', 'designation', 'user_id')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->user->name ?? 'Unknown',
                        'employee_no' => $employee->employee_no,
                        'department' => $employee->department,
                        'designation' => $employee->designation
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $staff,
                'message' => 'Staff list retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving staff list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfYear());
            $endDate = $request->input('end_date', Carbon::now()->endOfYear());

            $stats = [
                'total_reports' => StaffReport::whereBetween('report_start_date', [$startDate, $endDate])->count(),
                'reports_by_month' => StaffReport::selectRaw('YEAR(report_start_date) as year, MONTH(report_start_date) as month, COUNT(*) as count')
                    ->whereBetween('report_start_date', [$startDate, $endDate])
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get(),
                'reports_by_department' => StaffReport::selectRaw('department, COUNT(*) as count')
                    ->whereBetween('report_start_date', [$startDate, $endDate])
                    ->groupBy('department')
                    ->get(),
                'average_attendance' => StaffReport::whereBetween('report_start_date', [$startDate, $endDate])
                    ->avg('attendance_percentage'),
                'total_incidents' => StaffReport::whereBetween('report_start_date', [$startDate, $endDate])
                    ->sum('total_incidents_reported')
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
