<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\StaffChart;
use App\Models\Incident;
use App\Models\ClientMedication;
use App\Models\MedicationInTake;
use App\Models\KeyContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateClientSummaryReport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'report:client-summary {--client_id=} {--month=} {--year=}';

    /**
     * The console command description.
     */
    protected $description = 'Generate monthly client summary reports and send to family members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientId = $this->option('client_id');
        $month = $this->option('month') ?: Carbon::now()->subMonth()->month;
        $year = $this->option('year') ?: Carbon::now()->subMonth()->year;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        if ($clientId) {
            $this->generateReportForClient($clientId, $startDate, $endDate);
        } else {
            $this->generateReportsForAllClients($startDate, $endDate);
        }
    }

    private function generateReportsForAllClients($startDate, $endDate)
    {
        $clients = Client::with(['keyContacts', 'medications', 'medicationInTakes'])
                        ->get();

        foreach ($clients as $client) {
            try {
                $this->generateReportForClient($client->id, $startDate, $endDate);
                $this->info("Report generated for client: {$client->client_no}");
            } catch (\Exception $e) {
                $this->error("Failed to generate report for client {$client->client_no}: " . $e->getMessage());
            }
        }
    }

    private function generateReportForClient($clientId, $startDate, $endDate)
    {
        $client = Client::with(['keyContacts', 'medications', 'medicationInTakes'])
                       ->findOrFail($clientId);

        // Gather all report data
        $reportData = $this->gatherReportData($client, $startDate, $endDate);

        // Generate PDF
        $pdf = $this->generatePDF($client, $reportData, $startDate, $endDate);

        // Send emails to family members
        $this->sendReportToFamily($client, $pdf, $startDate, $endDate);
    }

    private function gatherReportData($client, $startDate, $endDate)
    {
        return [
            'medications' => $this->getMedicationData($client, $startDate, $endDate),
            'vital_signs' => $this->getVitalSignsData($client, $startDate, $endDate),
            'medication_changes' => $this->getMedicationChanges($client, $startDate, $endDate),
            'weight_assessments' => $this->getWeightAssessments($client, $startDate, $endDate),
            'laboratory_tests' => $this->getLaboratoryTests($client, $startDate, $endDate),
            'medical_incidents' => $this->getMedicalIncidents($client, $startDate, $endDate),
            'hospital_visits' => $this->getHospitalVisits($client, $startDate, $endDate),
            'nursing_evaluations' => $this->getNursingEvaluations($client, $startDate, $endDate),
            'doctor_observations' => $this->getDoctorObservations($client, $startDate, $endDate),
            'food_intake' => $this->getFoodIntake($client, $startDate, $endDate),
            'fluid_intake' => $this->getFluidIntake($client, $startDate, $endDate),
            'bowel_movements' => $this->getBowelMovements($client, $startDate, $endDate),
            'activities' => $this->getActivities($client, $startDate, $endDate)
        ];
    }

    private function getMedicationData($client, $startDate, $endDate)
    {
        return $client->medications()
                     ->where('created_at', '>=', $startDate)
                     ->where('created_at', '<=', $endDate)
                     ->get()
                     ->map(function($med) {
                         return [
                             'name' => $med->medicine_name ?? 'N/A',
                             'type' => $med->medication_type ?? 'N/A',
                             'unit' => $med->medication_unit ?? 'N/A',
                             'reason' => $med->reason_for_medication ?? 'N/A',
                             'start_date' => $med->start_date ?? 'N/A'
                         ];
                     });
    }

    private function getVitalSignsData($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->whereIn('category', ['WellbeingCheck', 'Covid19 Check'])
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'category' => $chart->category,
                                'weight_kg' => $chart->weight_kg,
                                'weight_pounds' => $chart->weight_pounds,
                                'description' => $chart->description,
                                'result' => $chart->result,
                                'score' => $chart->score,
                                'is_positive_covid_19' => $chart->is_positive_covid_19 ? 'Yes' : 'No',
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getMedicationChanges($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->whereIn('category', ['Medical Visits', 'Procedures'])
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'category' => $chart->category,
                                'medicine_name' => $chart->medicine_name,
                                'medication_type' => $chart->medication_type,
                                'reason' => $chart->reason_for_medication,
                                'morning_dose' => $chart->is_morning_dose_administered ? 'Yes' : 'No',
                                'afternoon_dose' => $chart->is_afternoon_dose_administered ? 'Yes' : 'No',
                                'evening_dose' => $chart->is_evening_dose_administered ? 'Yes' : 'No',
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getWeightAssessments($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->where('category', 'WellbeingCheck')
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'weight_kg' => $chart->weight_kg,
                                'weight_pounds' => $chart->weight_pounds,
                                'weight_grams' => $chart->weight_grams,
                                'weight_stone' => $chart->weight_stone,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getLaboratoryTests($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->whereIn('category', ['Procedures', 'Medical Visits'])
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'category' => $chart->category,
                                'description' => $chart->description,
                                'result' => $chart->result,
                                'score' => $chart->score,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getMedicalIncidents($client, $startDate, $endDate)
    {
        return Incident::where('client_id', $client->id)
                      ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->with(['staff', 'staff_present'])
                      ->get()
                      ->map(function($incident) {
                          return [
                              'date' => $incident->report_date,
                              'title' => $incident->title,
                              'description' => $incident->description,
                              'staff' => $incident->staff->name ?? 'Unknown',
                              'staff_present' => $incident->staff_present->name ?? 'N/A'
                          ];
                      });
    }

    private function getHospitalVisits($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->whereIn('category', ['Hospital/Doctor Visit', 'Medical Visits'])
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'category' => $chart->category,
                                'location' => $chart->location,
                                'reason_for_visit' => $chart->reason_for_visit,
                                'resultant_action' => $chart->resultant_action,
                                'visit_type' => $chart->visit_type,
                                'visit_location' => $chart->visit_location,
                                'visitor_name' => $chart->visitor_name,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getNursingEvaluations($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->whereIn('category', ['Personal Care', 'BathShower', 'Cream Usage', 'WaterBed Cleaning', 'Bed Change'])
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'category' => $chart->category,
                                'level_of_need' => $chart->level_of_need,
                                'present_situation' => $chart->present_situation,
                                'actions' => $chart->actions,
                                'resident_needs' => $chart->resident_needs,
                                'cream_applied' => $chart->cream_applied,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getDoctorObservations($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->whereIn('category', ['Hospital/Doctor Visit', 'Medical Visits', 'WellbeingCheck'])
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'category' => $chart->category,
                                'description' => $chart->description,
                                'health_issue' => $chart->health_issue,
                                'review_date' => $chart->review_date,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function generatePDF($client, $reportData, $startDate, $endDate)
    {
        $data = [
            'client' => $client,
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'monthYear' => $startDate->format('F Y')
        ];

        return PDF::loadView('reports.client-summary', $data);
    }

    private function sendReportToFamily($client, $pdf, $startDate, $endDate)
    {
        $familyContacts = $client->keyContacts()
                               ->where('relationship', 'family')
                               ->whereNotNull('email_address')
                               ->get();

        foreach ($familyContacts as $contact) {
            try {
                Mail::send('emails.client-summary-report', [
                    'client' => $client,
                    'contact' => $contact,
                    'monthYear' => $startDate->format('F Y')
                ], function ($message) use ($client, $contact, $pdf, $startDate) {
                    $message->to($contact->email, $contact->name)
                           ->subject("Monthly Summary Report for {$client->user->first_name} {$client->user->last_name} - {$startDate->format('F Y')}")
                           ->attachData($pdf->output(), "client_summary_{$client->client_no}_{$startDate->format('Y_m')}.pdf");
                });

                $this->info("Report sent to: {$contact->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send report to {$contact->email}: " . $e->getMessage());
            }
        }
    }

    private function getFoodIntake($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->where('category', 'Food Intake')
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'food_type' => $chart->food_type,
                                'food_period' => $chart->food_period,
                                'description' => $chart->description,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getFluidIntake($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->where('category', 'FluidIntake')
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'drink_type' => $chart->drink_type,
                                'delivery_method' => $chart->delivery_method,
                                'quantity_ml' => $chart->quantity_ml,
                                'description' => $chart->description,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getBowelMovements($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->where('category', 'Bowel Movement')
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'bowel_type' => $chart->bowel_type,
                                'description' => $chart->description,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }

    private function getActivities($client, $startDate, $endDate)
    {
        return StaffChart::where('client_id', $client->id)
                        ->where('category', 'Activities')
                        ->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->get()
                        ->map(function($chart) {
                            return [
                                'date' => $chart->report_date,
                                'description' => $chart->description,
                                'family_participant' => $chart->family_participant,
                                'other_participants' => $chart->other_participants,
                                'staff' => $chart->staff->name ?? 'Unknown'
                            ];
                        });
    }
}
