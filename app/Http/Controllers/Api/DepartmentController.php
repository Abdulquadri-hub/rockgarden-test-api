<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class DepartmentController extends Controller
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
                'message' => Department::all()
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
        return \response()->json([
                'success' => true,
                'message' => Department::paginate((int) $request->get('limit'))
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
            $validator = Validator::make($request->only('department_name', 'lead_fullname', 'lead_telephone'),[
                'department_name' => 'required|string',
                'lead_fullname' => 'required|string',
                'lead_telephone' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Department data.'
                ], 400);
            }

            // Create new Department name
            $department = new Department();
            $department->department_name = $request->get('department_name');
            $department->lead_fullname = $request->get('lead_fullname');
            $department->lead_email = $request->get('lead_email');
            $department->lead_telephone = $request->get('lead_telephone');
            $department->save();
            return response()->json([
                'success' => true,
                'message' => $department->id
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
        $department = Department::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $department
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
            $validator = Validator::make($request->only('id','department_name', 'lead_fullname', 'lead_telephone'),[
                'id' => 'required|integer',
                'department_name' => 'required|string',
                'lead_fullname' => 'required|string',
                'lead_telephone' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Department data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new Department name
            $department = Department::where('id', $request->get('id'))->firstOrFail();
            if(empty($department)){
                return response()->json([
                    'success' => false,
                    'message' => 'Department not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $department->department_name = $request->get('department_name');
            $department->lead_fullname = $request->get('lead_fullname');
            $department->lead_email = $request->get('lead_email');
            $department->lead_telephone = $request->get('lead_telephone');
            $department->save();
            return response()->json([
                'success' => true,
                'message' => $department
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

        Department::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Department successfully deleted"
        ], Response::HTTP_OK);
    }
}
