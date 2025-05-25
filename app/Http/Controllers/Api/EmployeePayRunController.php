<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeePayRunCollection;
use App\Http\Resources\EmployeePayRunResource;
use App\Models\Employee;
use App\Models\EmployeePayRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class EmployeePayRunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return \response()->json([
                'success' => true,
                'message' => EmployeePayRun::all()
            ]
            , Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexPaged(Request $request)
    {
        $staff_id = $request->get('staff_id');
        if(!empty($staff_id)){
            return \response()->json([
                'success' => true,
                'message' => new EmployeePayRunCollection(EmployeePayRun::where('staff_id', $staff_id)->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
        }
        return \response()->json([
            'success' => true,
            'message' => new EmployeePayRunCollection(EmployeePayRun::paginate((int) $request->get('limit')))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'staff_id' => 'required|integer',
                'basic_salary' => 'required|numeric',
                'basic_salary_currency' => 'required|string',
                'payment_date' => 'required|date',
                'status' => 'required|string',
                'deductions' => 'required|array',
                'reimbursement' => 'required|numeric',
                'reimbursement_currency' => 'required|string',
                'reimbursement_info' => 'required|string',
                'pay_days' => 'required|integer',
                'allowances' => 'required|array',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee PayRun invalid data.'
                ], 400);
            }

            $staffs =  Employee::where('id', $request->get('staff_id'))->first();

            if(empty($staffs)){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found.'
                ], 400);
            }

            // Create new EmployeePayRun
            $employeePayRun = new EmployeePayRun();
            $employeePayRun->staff_id = $request->get('staff_id');
            $employeePayRun->basic_salary = $request->get('basic_salary');
            $employeePayRun->basic_salary_currency = $request->get('basic_salary_currency');
            $employeePayRun->payment_date = new \DateTime($request->get('payment_date'));
            $employeePayRun->status = $request->get('status');
            $employeePayRun->deductions = $request->get('deductions');
            $employeePayRun->reimbursement = $request->get('reimbursement');
            $employeePayRun->reimbursement_currency = $request->get('reimbursement_currency');
            $employeePayRun->reimbursement_info = $request->get('reimbursement_info');
            $employeePayRun->pay_days = $request->get('pay_days');
            $employeePayRun->allowances = $request->get('allowances');
            $employeePayRun->save();
            return response()->json([
                'success' => true,
                'message' => $employeePayRun->id
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], Response::HTTP_CONFLICT
            );
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $employeePayRun = EmployeePayRun::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new EmployeePayRunResource($employeePayRun)
            ]
            , Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {
            // validate request data
            $validator = Validator::make($request->all(),[
                'id' => 'required|integer',
                'staff_id' => 'required|integer',
                'basic_salary' => 'required|numeric',
                'basic_salary_currency' => 'required|string',
                'payment_date' => 'required|date',
                'status' => 'required|string',
                'deductions' => 'required|array',
                'reimbursement' => 'required|numeric',
                'reimbursement_currency' => 'required|string',
                'reimbursement_info' => 'required|string',
                'pay_days' => 'required|integer',
                'allowances' => 'required|array'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Employee PayRun update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $employeePayRun = EmployeePayRun::where('id', $request->get('id'))->firstOrFail();
            if(empty($employeePayRun)){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee PayRun not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $staffs =  Employee::where('id', $request->get('staff_id'))->first();

            if(empty($staffs) || $staffs->id !== $employeePayRun->staff_id){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not matched.'
                ], 400);
            }

            // Replace existing Employee PayRun
            $employeePayRun->staff_id = $request->get('staff_id');
            $employeePayRun->basic_salary = $request->get('basic_salary');
            $employeePayRun->basic_salary_currency = $request->get('basic_salary_currency');
            $employeePayRun->payment_date = new \DateTime($request->get('payment_date'));
            $employeePayRun->status = $request->get('status');
            $employeePayRun->deductions = $request->get('deductions');
            $employeePayRun->reimbursement = $request->get('reimbursement');
            $employeePayRun->reimbursement_currency = $request->get('reimbursement_currency');
            $employeePayRun->reimbursement_info = $request->get('reimbursement_info');
            $employeePayRun->pay_days = $request->get('pay_days');
            $employeePayRun->allowances = $request->get('allowances');
            $employeePayRun->save();

            return response()->json([
                'success' => true,
                'message' => $employeePayRun
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], Response::HTTP_CONFLICT
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required|integer'
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'id not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        EmployeePayRun::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Employee PayRun successfully deleted"
        ], Response::HTTP_OK);
    }
}
