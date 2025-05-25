<?php

namespace App\Services;

use App\Models\StaffReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StaffReportPdfService
{
    public function generatePdf(StaffReport $report)
    {
        $data = [
            'report' => $report,
            'generated_date' => Carbon::now()->format('F j, Y'),
            'period' => $report->report_start_date->format('F j, Y') . ' - ' . $report->report_end_date->format('F j, Y')
        ];

        $pdf = Pdf::loadView('reports.staff-report-pdf', $data);

        $filename = "staff-report-{$report->staff_id}-{$report->report_start_date->format('Y-m')}.pdf";
        $path = "staff-reports/{$filename}";

        Storage::put($path, $pdf->output());

        $report->update(['pdf_path' => $path]);

        return $path;
    }
}
