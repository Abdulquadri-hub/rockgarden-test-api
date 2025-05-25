<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StaffReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyStaffReports extends Command
{
    protected $signature = 'reports:generate-monthly-staff {--month=} {--year=}';
    protected $description = 'Generate monthly staff reports for all staff members';

    private $staffReportService;

    public function __construct(StaffReportService $staffReportService)
    {
        parent::__construct();
        $this->staffReportService = $staffReportService;
    }

    public function handle()
    {
        $this->info('Starting monthly staff report generation...');

        // Get month and year from options or use previous month
        $month = $this->option('month') ?: Carbon::now()->subMonth()->month;
        $year = $this->option('year') ?: Carbon::now()->subMonth()->year;

        $month = 3;
        $year = 2023;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $this->info("Generating reports for period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        try {
            $reports = $this->staffReportService->generateAllStaffReports(
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            );

            $this->info("Successfully generated " . count($reports) . " staff reports.");
            Log::info("Monthly staff reports generated successfully", [
                'count' => count($reports),
                'period' => "{$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}"
            ]);

        } catch (\Exception $e) {
            $this->error("Error generating staff reports: " . $e->getMessage());
            Log::error("Error generating monthly staff reports: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
