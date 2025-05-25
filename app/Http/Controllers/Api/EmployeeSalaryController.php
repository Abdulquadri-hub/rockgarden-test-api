<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeSalaryRequest;
use App\Http\Requests\UpdateEmployeeSalaryRequest;
use App\Http\Resources\EmployeeSalaryCollection;
use App\Models\Designation;
use App\Models\EmployeeSalary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        if(!empty($limit)){
            $res = EmployeeSalary::orderBy('updated_at', 'DESC')->paginate($limit);
        }else {
            $res = EmployeeSalary::orderBy('updated_at', 'DESC')->get();
        }
        return \response()->json([
            'success' => true,
            'message' => new EmployeeSalaryCollection($res)
        ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexPaged(Request $request)
    {
        return \response()->json([
            'success' => true,
            'message' => EmployeeSalary::paginate((int) $request->get('limit'))
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
                'currency' => 'required|string',
                'duty_type' => 'string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee Salary invalid data.'
                ], 400);
            }
            $designation  =  Designation::where('designation_name', $request->get('designation_name'))->first();

            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee Salary designation not found.'
                ], 400);
            }

            // Create new EmployeeSalary
            $updata['designation_name'] = $designation->designation_name;
            $updata['duty_type'] = $request->get('duty_type');
            $updata['currency'] = $request->get('currency');
            $updata['basic_salary'] = $request->get('basic_salary');
            $updata['basic_salary_per_day'] = $request->get('basic_salary_per_day');

            $matchThese = ['designation_name'=> $updata['designation_name'],'duty_type'=> $updata['duty_type']];
            EmployeeSalary::updateOrCreate($matchThese, $updata);

            $employeeSalary = EmployeeSalary::where('designation_name', $updata['designation_name'])->where('duty_type', $updata['duty_type'])->first();

            return response()->json([
                'success' => true,
                'message' => $employeeSalary->id
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
        $employeeSalary = EmployeeSalary::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $employeeSalary
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
                'currency' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid employeeSalary update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $employeeSalary = EmployeeSalary::where('id', $request->get('id'))->firstOrFail();
            if(empty($employeeSalary)){
                return response()->json([
                    'success' => false,
                    'message' => 'EmployeeSalary not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing EmployeeSalary
            $designation  =  Designation::where('designation_name', $request->get('designation_name'))->first();

            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee Salary designation not found.'
                ], 400);
            }

            // Update new EmployeeSalary
            $employeeSalary->designation_name = $designation->designation_name;
            $employeeSalary->duty_type = $request->get('duty_type');
            $employeeSalary->currency = $request->get('currency');
            $employeeSalary->basic_salary = $request->get('basic_salary');
            $employeeSalary->basic_salary_per_day = $request->get('basic_salary_per_day');
            $employeeSalary->save();

            return response()->json([
                'success' => true,
                'message' => $employeeSalary
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

        EmployeeSalary::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Employee Salary successfully deleted"
        ], Response::HTTP_OK);
    }
}
