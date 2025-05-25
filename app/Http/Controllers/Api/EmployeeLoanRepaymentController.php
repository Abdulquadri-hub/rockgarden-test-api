<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeLoanRepaymentRepaymentRequest;
use App\Http\Requests\UpdateEmployeeLoanRepaymentRepaymentRequest;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanRepayment;
use App\Models\EmployeeLoanRepaymentRepayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class EmployeeLoanRepaymentController extends Controller
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
                'message' => EmployeeLoanRepayment::all()
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
        $loan_id = $request->get('loan_id');
        if(!empty($loan_id)){
            return \response()->json([
                'success' => true,
                'message' =>  EmployeeLoanRepayment::where('loan_id', $loan_id)->paginate((int) $request->get('limit'))
            ], Response::HTTP_OK);
        }
        return \response()->json([
            'success' => true,
            'message' => EmployeeLoanRepayment::paginate((int) $request->get('limit'))
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
                'loan_id' => 'required|integer',
                'amount_paid' => 'required|numeric',
                'payment_date' => 'required|date'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee Loan Repayment invalid data.'
                ], 400);
            }

            $loan =  EmployeeLoan::where('id', $request->get('loan_id'))->first();

            if(empty($staffs)){
                return response()->json([
                    'success' => false,
                    'message' => 'Loan not found.'
                ], 400);
            }

            // Create new EmployeeLoanRepayment
            $employeeLoanRepayment = new EmployeeLoanRepayment();
            $employeeLoanRepayment->loan_id = $request->get('loan_id');
            $employeeLoanRepayment->amount_paid = $request->get('amount_paid');
            $employeeLoanRepayment->payment_date = new \DateTime($request->get('payment_date'));
            $employeeLoanRepayment->save();
            return response()->json([
                'success' => true,
                'message' => $employeeLoanRepayment->id
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
        $employeeLoanRepayment = EmployeeLoanRepayment::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $employeeLoanRepayment
            ], Response::HTTP_OK);
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
                'loan_id' => 'required|integer',
                'amount_paid' => 'required|numeric',
                'payment_date' => 'required|date'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid employeeLoanRepayment name update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $employeeLoanRepayment = EmployeeLoanRepayment::where('id', $request->get('id'))->firstOrFail();
            if(empty($employeeLoanRepayment)){
                return response()->json([
                    'success' => false,
                    'message' => 'Employee Loan not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $loan =  EmployeeLoan::where('id', $request->get('loan_id'))->first();

            if(empty($loan) || $loan->id !== $employeeLoanRepayment->loan){
                return response()->json([
                    'success' => false,
                    'message' => 'Loan not matched.'
                ], 400);
            }

            // Replace existing Employee Loan
            $employeeLoanRepayment->loan_id = $request->get('loan_id');
            $employeeLoanRepayment->amount_paid = $request->get('amount_paid');
            $employeeLoanRepayment->payment_date = new \DateTime($request->get('payment_date'));
            $employeeLoanRepayment->save();

            return response()->json([
                'success' => true,
                'message' => $employeeLoanRepayment
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

        EmployeeLoanRepayment::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Employee Loan successfully deleted"
        ], Response::HTTP_OK);
    }
}
