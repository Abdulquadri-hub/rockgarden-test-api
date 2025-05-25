<?php

namespace App\Http\Controllers\Api;

use App\Dto\EventType;
use App\Dto\PayrunDto;
use App\Events\IndividualPayRunEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayRunRequest;
use App\Http\Requests\UpdatePayRunRequest;
use App\Http\Resources\PayRunCollection;
use App\Http\Resources\PayRunResource;
use App\Models\Allowance;
use App\Models\Bonus;
use App\Models\Deduction;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Loan;
use App\Models\PayRun;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\PayRunMail;
use App\Helpers\Helper;
use App\Models\PayRunBonuses;
use App\Models\PayRunLoan;
use App\Models\Rota;
use App\Models\Tax;
use Doctrine\DBAL\Types\DateTimeType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PayRunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
     
    // public function index(Request $request)
    // {
    //     // $limit = $request->get('limit');
    //     // $staff_id = $request->get('staff_id');

    //     // $response = [];

    //     // if(!empty($limit)){
    //     //     if(!empty($staff_id)){
    //     //         $response =  PayRun::where('staff_id', $staff_id)
    //     //             ->orderBy('created_at', 'DESC')
    //     //             ->orderBy('duty_type', 'ASC')
    //     //             ->orderBy('designation', 'ASC')
    //     //             ->orderBy('staff_name', 'ASC')
    //     //             ->paginate($limit);
    //     //     }else{
    //     //         $response =  PayRun::orderBy('created_at', 'DESC')
    //     //             ->orderBy('duty_type', 'ASC')
    //     //             ->orderBy('designation', 'ASC')
    //     //             ->orderBy('staff_name', 'ASC')
    //     //             ->paginate($limit);
    //     //     }
    //     // }else {

    //     //     if(!empty($staff_id)){
    //     //         $response =  PayRun::where('staff_id', $staff_id)
    //     //             ->orderBy('created_at', 'DESC')
    //     //             ->orderBy('duty_type', 'ASC')
    //     //             ->orderBy('designation', 'ASC')
    //     //             ->orderBy('staff_name', 'ASC')
    //     //             ->get();
    //     //     }else{
    //     //         $response =  PayRun::orderBy('created_at', 'DESC')
    //     //             ->orderBy('duty_type', 'ASC')
    //     //             ->orderBy('designation', 'ASC')
    //     //             ->orderBy('staff_name', 'ASC')
    //     //             ->get();
    //     //     }
    //     // }

    //     // return \response()->json([
    //     //         'success' => true,
    //     //         'message' => new PayRunCollection($response)
    //     //     ]
    //     //     , Response::HTTP_OK);
    //     $limit = $request->get('limit');
    // $staff_id = $request->get('staff_id');
    // $from_date = $request->get('from_date');
    // $to_date = $request->get('to_date');

    // $query = PayRun::orderBy('created_at', 'DESC')
    //     ->orderBy('duty_type', 'ASC')
    //     ->orderBy('designation', 'ASC')
    //     ->orderBy('staff_name', 'ASC');

    // if (!empty($staff_id)) {
    //     $query->where('staff_id', $staff_id);
    // }

    // if (!empty($from_date) && !empty($to_date)) {
    //     $query->whereBetween('created_at', [$from_date, $to_date]);
    // }

    // if (!empty($limit)) {
    //     $response = $query->paginate($limit);
    // } else {
    //     $response = $query->get();
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => new PayRunCollection($response)
    // ], Response::HTTP_OK);
    // }
    
    
    # UPDATE THE INDEX 
    public function index(Request $request)
    {
        $limit = $request->get('limit');
        $staff_id = $request->get('staff_id');

        $response = [];

        if(!empty($limit)){
            if(!empty($staff_id)){
                $response =  PayRun::where('staff_id', $staff_id)
                    ->join("employees", "pay_runs.staff_id", "=", "employees.id")
                    ->join("users", "employees.user_id", "=", "users.id")
                    ->where("users.active", 1)
                    ->orderBy('pay_runs.created_at', 'DESC')
                    ->orderBy('pay_runs.duty_type', 'ASC')
                    ->orderBy('pay_runs.designation', 'ASC')
                    ->orderBy('pay_runs.staff_name', 'ASC')
                    ->paginate($limit);
            }else{
                $response =  PayRun::join("employees", "pay_runs.staff_id", "=", "employees.id")
                    ->join("users", "employees.user_id", "=", "users.id")
                    ->where("users.active", 1)
                    ->orderBy('pay_runs.created_at', 'DESC')
                    ->orderBy('pay_runs.duty_type', 'ASC')
                    ->orderBy('pay_runs.designation', 'ASC')
                    ->orderBy('pay_runs.staff_name', 'ASC')
                    ->paginate($limit);
            }
        }else {

            if(!empty($staff_id)){
                $response =  PayRun::where('staff_id', $staff_id)
                    ->join("employees", "pay_runs.staff_id", "=", "employees.id")
                    ->join("users", "employees.user_id", "=", "users.id")
                    ->where("users.active", 1)
                    ->orderBy('pay_runs.created_at', 'DESC')
                    ->orderBy('pay_runs.duty_type', 'ASC')
                    ->orderBy('pay_runs.designation', 'ASC')
                    ->orderBy('pay_runs.staff_name', 'ASC')
                    ->get();
            }else{
                $response =  PayRun::join("employees", "pay_runs.staff_id", "=", "employees.id")
                    ->join("users", "employees.user_id", "=", "users.id")
                    ->where("users.active", 1)
                    ->orderBy('pay_runs.created_at', 'DESC')
                    ->orderBy('pay_runs.duty_type', 'ASC')
                    ->orderBy('pay_runs.designation', 'ASC')
                    ->orderBy('pay_runs.staff_name', 'ASC')
                    ->get();
            }
        }

        return \response()->json([
                'success' => true,
                'message' => new PayRunCollection($response)
            ]
            , Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(),[
                'title' => 'required|string',
                'date_from'=> 'required',
                'date_to' => 'required',
                'bonuses' => 'array',
                'taxes'=> 'array',
                'department_type' => 'required|string|in:RGH,RHA',
                'allowances' => 'array',
                'deductions' => 'array'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PayRun invalid data.'
                ], 400);
            }

            // Get Staff Id and department ID
            $st_id = $request->get('staff_id');
            $department_type = $request->get('department_type');

            // Fetch all employees
            // $staffs = !empty($st_id) ? Employee::where('id', $st_id)->get() : Employee::all();

            // Filter staff based on department type
            $staffQuery = Employee::query();

            if (!empty($st_id)) {
                $staffQuery->where('id', $st_id);
            }

            // Apply department filtering
            if ($department_type === 'RGH') {
                $staffQuery->whereNotIn('department', ['RHA Team 1', 'RHA Team 2']);
            } else if ($department_type === 'RHA') {
                $staffQuery->whereIn('department', ['RHA Team 1', 'RHA Team 2']);
            }

            $staffs = $staffQuery->get();


            // Check if any staff were found
            if ($staffs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No staff found for the specified department type.'
                ], 404);
            }

            $title = $request->get('title');

            $payRuns = PayRun::where('title', $title)->get();

            // Fetch required elements from dataBase
            $designations = Designation::all();
            $bonuses = Bonus::whereIn('name',$request->get('bonuses'))->get()->all();
            $allowances = Allowance::whereIn('name',$request->get('allowances'))->get()->all();
            $deductions = Deduction::whereIn('name',$request->get('deductions'))->get()->all();
            $bSalary = EmployeeSalary::all();

            $from =  new \DateTime($request->get('date_from'));
            $to = new \DateTime($request->get('date_to'));
            $days  = (int)($from->diff($to)->format('%a'));

            Log::debug($days);

            if($days < 1){
                return response()->json([
                    'success' => false,
                    'message' => 'invalid payment period.'
                ], 400);
            }

            $workingDays = DB::table('rotas')->selectRaw('staff_id, count(*) as total')->whereBetween('rota_date', [$from, $to])->where('is_present', 1)->groupBy('staff_id')->get();

            // Initialization
            $payRunsToSave = [];
            $payRunsToUpdate = [];

            foreach ($staffs as $staff){
                $bonusVal = [];
                $allowanceVal = [];
                $deductionVal = [];
                $taxesVal = [];

                $key = null;
                if (!empty($payRuns)){
                    foreach ($payRuns as $j => $pa){
                        if ($pa->staff_id === $staff->id){
                            $key = $j;
                            break;
                        }
                    }
                }

                $payRun =  [];

                $designationKey = null;
                foreach ($designations as $i => $des){
                    if ($des->designation_name === $staff->designation){
                        $designationKey = $i;
                        break;
                    }
                }

                $designation = $designationKey !== null ? $designations[$designationKey] : null;
                $salary = null;

                // Check for staff bonuses, deductions, allowances and also taxes
                if(!empty($designation)){

                    $bonArr = array_filter($bonuses, function($element) use ($designation){
                        return $element->designation_name === $designation->designation_name;
                    });

                    if(!empty($bonArr)){
                        foreach ($bonArr as $item){
                            $bonus =  [
                                'name' => $item->name,
                                'currency' => $item->currency,
                                'amount' => $item->amount,
                                'percentage' => $item->percentage,
                                'designation_name' => $item->designation_name
                            ];
                            $bonusVal[] = $bonus;
                        }
                    }

                    $allowArr = array_filter($allowances, function($element) use ($designation){
                        return $element->designation_name === $designation->designation_name;
                    });

                    if(!empty($allowArr)){
                        foreach ($allowArr as $item){
                            $allowance =  [
                                'name' => $item->name,
                                'currency' => $item->currency,
                                'amount' => $item->amount,
                                 'percentage' => $item->percentage,
                                'designation_name' => $item->designation_name
                            ];
                            $allowanceVal[] = $allowance;
                        }
                    }

                    $deductArr = array_filter($deductions, function($element) use ($designation){
                        return $element->designation_name === $designation->designation_name;
                    });

                    if(!empty($deductArr)){
                        foreach ($deductArr as $item){
                            $deduction =  [
                                'name' => $item->name,
                                'currency' => $item->currency,
                                'amount' => $item->amount,
                                'designation_name' => $item->designation_name
                            ];
                            $deductionVal[] = $deduction;
                        }
                    }

                    $taxesArr = Tax::whereIn('name', $request->get('taxes'))->get();

                    if(!empty($taxesArr)){
                        foreach ($taxesArr as $item){
                            $taxe =  [
                                'name' => $item->name,
                                'currency' => $item->currency,
                                'amount' => $item->amount,
                                 'percentage' => $item->percentage,
                                  'is_fixed' => $item->is_fixed,
                                'designation_name' => $item->designation_name
                            ];
                            $taxesVal[] = $taxe;
                        }
                    }

                    $salaryKey = null;
                    foreach ($bSalary as $k => $bsal){
                        if ($bsal->designation_name === $designation->designation_name && $staff->duty_type === $bsal->duty_type){
                            $salaryKey = $k;
                            break;
                        }
                    }

                    $salary = !empty($salaryKey) ? $bSalary[$salaryKey] : null;
                }

                $staffWorkingDays = 0;
                // Get Staff working days
                foreach ($workingDays as $keyDays => $workingDay){
                    if ($workingDay->staff_id === $staff->id){
                        $staffWorkingDaysKey = $keyDays;
                        $staffWorkingDays = $workingDay->total;
                        break;
                    }
                }

                $payRun['title'] = $title;
                $payRun['staff_id'] = $staff->id;
                $payRun['deductions'] = array_unique($deductionVal, SORT_REGULAR);
                $payRun['taxes'] = array_unique($taxesVal, SORT_REGULAR);
                $payRun['bonuses'] = array_unique($bonusVal, SORT_REGULAR);
                $payRun['allowances'] = array_unique($allowanceVal, SORT_REGULAR);
                $payRun['from_date'] = $from;
                $payRun['to_date'] =  $to;
                $payRun['staff_name'] = $staff->user ? $staff->user->first_name.' '.$staff->user->last_name : "";
                $payRun['days_present'] = $staffWorkingDays;
                $payRun['bank_name'] = $staff->bank_name;
                $payRun['bank_account_number'] = $staff->bank_account_number;
                $payRun['department'] = $staff->department;
                $payRun['duty_type'] = $staff->duty_type;

                if(!empty($designation))
                    $payRun['designation'] = $designation->designation_name;
                if(!empty($salary)){
                    Log::debug([$staffWorkingDays]);
                    $payRun['currency'] = $salary->currency;
                    if(!empty($salary->basic_salary_per_day)){
                        $payRun['basic_salary'] = ((float)($staffWorkingDays)) * ((float)($salary->basic_salary_per_day));
                    }else if(!empty($salary->basic_salary)){
                        $payRun['basic_salary'] =  ((float)($salary->basic_salary));
                    }else{
                        $payRun['basic_salary'] = 0.0;
                    }
                }else{
                    $payRun['basic_salary'] = 0.0;
                }
                $payRunsToSave[] = $payRun;
            }

            $res = [];
          /*  Log::debug($payRunsToSave);*/
            if(!empty($payRunsToSave) && count($payRunsToSave) > 0){
                foreach ($payRunsToSave as $updata)
                {
                    $matchThese = ['title'=> $updata['title'],'staff_id'=> $updata['staff_id']];
                    PayRun::updateOrCreate($matchThese, $updata);

                    // Raise Events
                    // Or use cron task to review
                    if(!empty($updata)){
                        $invoiceDto = new PayrunDto(
                            $updata['department']??null,
                            $updata['taxes']??null,
                            $updata['deductions']??null,
                            $updata['allowances']??null,
                            $updata['bonuses']??null,
                            $updata['title']??null,
                            $updata['to_date']??null,
                            $updata['from_date']??null,
                            $updata['basic_salary']??null,
                            $updata['currency'] ?? null,
                            $updata['bank_account_number']??null,
                            $updata['duty_type']??null,
                            $updata['bank_name']??null,
                            $updata['designation']??null,
                            $updata['staff_name']??null,
                            $updata['basic_salary']??null,
                            $updata['currency']??null,
                            null, EventType::INDIVIDUAL_PAY_RUN);
                        event(new IndividualPayRunEvent($invoiceDto, null, $updata['staff_id']??null, null));
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "PayRun successfully executed.",
                'data' => $res
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e);
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], Response::HTTP_CONFLICT
            );
        }
    }
    
    public function payrun_email(Request $request)
    {

        $payrun_title = $request->input('payrun_title');
        $staff_ids = $request->input('staff_ids');

        if (!isset($staff_ids)) {
            return response()->json(['message' => 'Invalid staff_ids'], 400);
        }
           $payruns = Payrun::where('title', $payrun_title,"AND")
            ->whereIn('staff_id',  $staff_ids)
            ->get();


        if ($payruns->isEmpty()) {
            return response()->json(['message' => 'Payruns not found'], 404);
        }
         $tax =Tax::get();

        // return $payruns;
       foreach ($payruns as $key => $payrun) {
            $staff_user = $payrun->staff->user;
            $staffs = $payrun->staff;

            // Calculate the total amount
            $totalAmount = $payrun->basic_salary;

            foreach ($payrun->bonuses as $bonus) {
                $totalAmount += $bonus['amount'];
            }

            foreach ($payrun->deductions as $deduction) {
                $totalAmount -= $deduction['amount'];
            }

            foreach ($payrun->taxes as $tax) {
                $totalAmount -= $tax['amount'];
            }

            $details = [
                'empoyee_fullname' => $payrun->staff_name,
                'department_name' => $payrun->department,
                'taxes' => $payrun->taxes,
                'deductions' => $payrun->deductions,
                'payrun_title' => $payrun->title,
                'allowances' => $payrun->allowances,
                'bonuses' => $payrun->bonuses,
                'date_to' => $payrun->to_date,
                'date_from' => $payrun->from_date,
                'totalAmount' => $totalAmount,
                'currency' => $payrun->currency,
                'account_no' => $staffs->bank_account_number,
                'duty_type' => $staffs->duty_type,
                'bank_name' => $staffs->bank_name,
                'designation_name' => $staffs->designation,
                'payrun' => $payrun,
                'amount' => $payrun->basic_salary,
                'total_currency' => $payrun->total_currency,
            ];

            $pdfView = "emails.payments.attachment_payrun";
            $pdf = PDF::loadView($pdfView, compact('staff_user', 'payrun', 'staffs', 'details'));
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            $pdfOutput = $pdf->output();

            $attachment = [
                'data' => $pdfOutput,
                'name' => 'Salary Payslip.pdf',
            ];
            $staff_user->email;

            // Helper::sendEmail($staff_user->email, new PayRunMail($details, $attachment));
            Helper::sendEmail("opeyemi.ajegbomogun@yahoo.com", new PayRunMail($details, $attachment));
        }

        return response()->json([
            'success' => true,
            'message' => 'Payslip email sent successfully'
        ], Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $payRun = PayRun::where('id', $request->get('id'))->first();
            return \response()->json([
                    'success' => true,
                    'message' => empty($payRun)? null : new PayRunResource($payRun)
                ]
                , Response::HTTP_OK);
        }catch (\Exception $e){
           Log::debug($e);
            return \response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]
                , Response::HTTP_BAD_REQUEST);
        }
    }


//    /**
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function update(Request $request)
//    {
//        try {
//            // validate request data
//            $validator = Validator::make($request->all(),[
//                'title' => 'required|date',
//                'deductions' => 'array',
//                'allowances' => 'array',
//                'bonuses' => 'array',
//                'employee_loans' => 'array',
//            ]);
//
//            if($validator->fails()) {
//                return response()->json([
//                    'success' => false,
//                    'message' => 'PayRun invalid data.'
//                ], 400);
//            }
//
//            $deductions = $request->get('deductions');
//            $allowances = $request->get('allowances');
//            $bonuses = $request->get('bonuses');
//            $employeeLoans = $request->get('employee_loans');
//
//            $deductionsVal =  Deduction::whereIn('name')->get();
//            $allowancesVal =  Allowance::whereIn('name')->get();
//            $bonusesVal =  Bon
//    us::whereIn('id')->get();
//            $employeeLoansVal =  Loan::whereIn('id')->get();
//
//            if(count($deductions) !== count($deductionsVal)){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Deductions not matched.'
//                ], 400);
//            }
//
//            if(count($allowances) !== count($allowancesVal)){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Allowances not matched.'
//                ], 400);
//            }
//
//            if(count($bonuses) !== count($bonusesVal)){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Bonuses not matched.'
//                ], 400);
//            }
//
//            if(count($employeeLoans) !== count($employeeLoansVal)){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Loans not matched.'
//                ], 400);
//            }
//
//            // Create new PayRun
//            $payRun =  PayRun::where('id', $request->get(''))->first();
//            if(empty($payRun)){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'PayRun not found.'
//                ], 400);
//            }
//            $payRun->title = new \DateTime($request->get('title'));
//            $payRun->deductions = $deductions;
//            $payRun->allowances = $allowances;
//
//            $payRun->save();
//
//            foreach ($bonuses as $bonus){
//                PayRunBonuses::firstOrCreate(
//                    [
//                        'bonus_id' => $bonus,
//                        'pay_run_id' => $payRun->id,
//                    ]
//                );
//            }
//
//            foreach ($employeeLoans as $loan){
//                PayRunBonuses::firstOrCreate(
//                    [
//                        'loan_id' => $loan,
//                        'pay_run_id' => $payRun->id,
//                    ]
//                );
//            }
//
//            return response()->json([
//                'success' => true,
//                'message' => $payRun->id
//            ], Response::HTTP_OK);
//        }catch (\Exception $e){
//            Log::error($e->getMessage());
//            return response()->json(
//                [
//                    'success' => false,
//                    'message' => $e->getMessage()
//                ], Response::HTTP_CONFLICT
//            );
//        }
//    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $id = $request->get('id');

            $title = $request->get('title');

            if(!empty($id) && !empty($title)){
                PayRun::where('id', $id)
                    ->where('title', $title)
                    ->delete();
            }else if (!empty($id)){
                PayRun::where('id', $id)
                    ->delete();
            }else if (!empty($title)){
                PayRun::where('title', $title)
                    ->delete();
            }else{
                return response()->json([
                    'success' => true,
                    'message' => "PayRun not found"
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => "PayRun successfully deleted"
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::debug($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }
}
