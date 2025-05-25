<?php

namespace App\Console\Commands;

use App\Http\Resources\ClientResource;
use App\Http\Resources\EmployeeResource;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientStatisticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientstaff:chart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to cache chart data in cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->count();
        return 0;
    }

    public function count(){
        $countClient = Client::count();

        $countStaff = Employee::count();;

        $countClientByStatus = DB::select( "select count(ct.id) as number, usr.active as status FROM clients AS ct INNER JOIN users AS usr ON ct.user_id = usr.id GROUP BY usr.active");

        $countStaffByStatus = DB::select( "select count(emp.id) as number, usr.active  as status FROM employees AS emp INNER JOIN users AS usr ON emp.user_id = usr.id GROUP BY usr.active");

        $countClientAssigned = DB::select( "select count(DISTINCT ct.id) as number FROM clients AS ct INNER JOIN staff_assignments AS st ON ct.id = st.client_id");

        $countStaffAssigned = DB::select( "select count(DISTINCT emp.id) as number FROM employees AS emp INNER JOIN staff_assignments AS st ON emp.id = st.staff_id");

        $sumInvoicesClientActive = DB::select( "select SUM(invc.payment_amount) as total, ct.id as primary_id FROM clients AS ct INNER JOIN users AS usr ON ct.user_id = usr.id INNER JOIN invoices AS invc on invc.client_id = ct.id WHERE usr.active = 1 ORDER BY total");

        $staffChartMonth = DB::table('staff_charts')
            ->selectRaw('COUNT(*) as nbr, staff_charts.staff_id as staff_id')
            ->whereMonth('report_date', date('m'))
            ->whereYear('report_date', date('Y'))
            ->groupBy('staff_charts.staff_id')
            ->orderByRaw('nbr DESC')
            ->get();

        Log::debug([
            $staffChartMonth,
            $countClient,
            $countStaff,
            $countClientByStatus,
            $countStaffByStatus,
            $countClientAssigned,
            $countStaffAssigned,
            $sumInvoicesClientActive
        ]);

//        * active_clients (int) <- count
//        * inactive_clients (int)  <- count
        foreach ($countClientByStatus as $countClit){
            if($countClit['status'] === 1){
                $active_clients = $countClit['number'];
            }else{
                $inactive_clients = $countClit['number'];
            }
        }

        // * assigned_clients (int) <- count Clients with staff assigned
        $assigned_clients = $countClientAssigned[0]->number;
        // * unassigned_clients (int) <- count Clients without staff assigned
        $unassigned_clients = $countClient - $countClientAssigned[0]->number;

        // * highest_paying_active_client (client object with highest total invoice amount)
        // * lowest_paying_active_client (client object with lowest total invoice amount)
        $totatlSum = count($sumInvoicesClientActive);
        if($totatlSum <= 0){
            $highest_paying_active_client = null;
            $lowest_paying_active_client = null;
        }else if($totatlSum == 1){
            $highest_paying_active_client = new ClientResource(Client::where('id',$sumInvoicesClientActive[0]['primary_id'])->first());
            $lowest_paying_active_client = $highest_paying_active_client;
        }else{
            $highest_paying_active_client = new ClientResource(Client::where('id',$sumInvoicesClientActive[0]['primary_id'])->first());
            $lowest_paying_active_client = new ClientResource(Client::where('id',$sumInvoicesClientActive[$totatlSum -1]['primary_id'])->first());
        }

        // * active_staffs (int) <- count
        // * inactive_staffs (int)  <- count
        foreach ($countStaffByStatus as $countStf){
            if($countStf['status'] === 1){
                $active_staffs = $countStf['number'];
            }else{
                $inactive_staffs = $countStf['number'];
            }
        }

        // * assigned_staffs (int) <- count Clients with staff assigned
        $assigned_staffs = $countStaffAssigned[0]->number;
        // * unassigned_staffs (int) <- count Clients without staff assigned
        $unassigned_staffs = $countStaff - $countStaffAssigned[0]->number;

        // * most_chart_for_month_staff (employee that submitted the most charts for current month)
        // * least_chart_for_month_staff (employee that submitted the least charts for current month)
        $toStaff = count($staffChartMonth);
        if($toStaff <= 0){
            $most_chart_for_month_staff = null;
            $least_chart_for_month_staff = null;
        }else if($totatlSum == 1){
            $most_chart_for_month_staff = new EmployeeResource(Employee::where('id',$staffChartMonth[0]['staff_id'])->first());
            $least_chart_for_month_staff = $most_chart_for_month_staff;
        }else{
            $most_chart_for_month_staff = new EmployeeResource(Employee::where('id',$staffChartMonth[0]['staff_id'])->first());
            $least_chart_for_month_staff = new EmployeeResource(Employee::where('id',$staffChartMonth[$toStaff -1]['staff_id'])->first());
        }

        $client_stats = [
            'active_clients' => $active_clients,
            'inactive_clients' => $inactive_clients,
            'assigned_clients' => $assigned_clients,
            'unassigned_clients' => $unassigned_clients,
            'highest_paying_active_client' => $highest_paying_active_client,
            'lowest_paying_active_client' => $lowest_paying_active_client,
        ];

        $staff_stats = [
            'active_staffs' => $active_staffs,
            'inactive_staffs' => $inactive_staffs,
            'assigned_staffs' => $assigned_staffs,
            'unassigned_staffs' => $unassigned_staffs,
            'most_chart_for_month_staff' => $most_chart_for_month_staff,
            'least_chart_for_month_staff' => $least_chart_for_month_staff,
        ];

        try {
            cache()->put('staff_chart', $staff_stats);
            cache()->put('client_chart', $client_stats);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
        }
    }
}
