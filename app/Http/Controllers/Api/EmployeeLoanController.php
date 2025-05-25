<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeLoanCollection;
use App\Http\Resources\EmployeeLoanResource;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class EmployeeLoanController extends Controller
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
                'message' => EmployeeLoan::all()
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
                'message' => new EmployeeLoanCollection(EmployeeLoan::where('staff_id', $staff_id)->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
        }
        return \response()->json([
            'success' => true,
            'message' => new EmployeeLoanCollection(EmployeeLoan::paginate((int) $request->get('limit')))
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
                'name' => 'required|string',
                'currency' => 'required|string',
                'amount' => 'required|numeric',
                'status' => 'required|string',
                'disbursement_date' => 'required|date',
                'repayment_start_date' => 'required|date',
                'installment_amount' => 'required|numeric',
                'reason' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee Loan invalid data.'
                ], 400);
            }

            $staffs =  Employee::where('id', $request->get('staff_id'))->first();

            if(empty($staffs)){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found.'
                ], 400);
            }

            // Create new EmployeeLoan
            $employeeLoan = new EmployeeLoan();
            $employeeLoan->staff_id = $request->get('staff_id');
            $employeeLoan->name = $request->get('name');
            $employeeLoan->currency = $request->get('currency');
            $employeeLoan->disbursement_date = new \DateTime($request->get('disbursement_date'));
            $employeeLoan->status = $request->get('status');
            $employeeLoan->amount = $request->get('amount');
            $employeeLoan->repayment_start_date = new \Datetime($request->get('repayment_start_date'));
            $employeeLoan->installment_amount = $request->get('installment_amount');
            $employeeLoan->reason = $request->get('reason');
            $employeeLoan->overdue_date = new \DateTime($request->get('overdue_date'));
            $employeeLoan->interest_rate = $request->get('interest_rate');
            $employeeLoan->total_amount_paid = $request->get('total_amount_paid');

            $employeeLoan->save();
            return response()->json([
                'success' => true,
                'message' => $employeeLoan->id
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
        $employeeLoan = EmployeeLoan::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new EmployeeLoanResource($employeeLoan)
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
                'name' => 'required|string',
                'currency' => 'required|string',
                'amount' => 'required|numeric',
                'status' => 'required|string',
                'disbursement_date' => 'required|date',
                'repayment_start_date' => 'required|date',
                'installment_amount' => 'required|numeric',
                'reason' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid employeeLoan name update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $employeeLoan = EmployeeLoan::where('id', $request->get('id'))->firstOrFail();
            if(empty($employeeLoan)){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee Loan not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $staffs =  Employee::where('id', $request->get('staff_id'))->first();

            if(empty($staffs) || $staffs->id !== $employeeLoan->staff_id){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not matched.'
                ], 400);
            }

            // Replace existing Employee Loan
            $employeeLoan->staff_id = $request->get('staff_id');
            $employeeLoan->name = $request->get('name');
            $employeeLoan->currency = $request->get('currency');
            $employeeLoan->disbursement_date = new \DateTime($request->get('disbursement_date'));
            $employeeLoan->status = $request->get('status');
            $employeeLoan->amount = $request->get('amount');
            $employeeLoan->repayment_start_date = new \Datetime($request->get('repayment_start_date'));
            $employeeLoan->installment_amount = $request->get('installment_amount');
            $employeeLoan->reason = $request->get('reason');
            $employeeLoan->overdue_date = new \DateTime($request->get('overdue_date'));
            $employeeLoan->interest_rate = $request->get('interest_rate');
            $employeeLoan->total_amount_paid = $request->get('total_amount_paid');
            $employeeLoan->save();

            return response()->json([
                'success' => true,
                'message' => $employeeLoan
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

        EmployeeLoan::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Employee Loan successfully deleted"
        ], Response::HTTP_OK);
    }
}
