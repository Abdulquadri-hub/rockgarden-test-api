<?php

namespace App\Http\Controllers\Api;


use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class DesignationController extends BaseController
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
                'message' => Designation::all()
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
                'message' => Designation::paginate((int) $request->get('limit'))
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
                'designation_name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Designation must be a valid name'
                ], 400);
            }
            // Create new Designation name
            $designation = new Designation();
            $designation->designation_name = $request->get('designation_name');
            $designation->save();
            return response()->json([
                'success' => true,
                'message' => $designation->id
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
        $designation = Designation::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $designation
            ]
            ,
            Response::HTTP_OK);
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
                'designation_name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid designation name update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $designation = Designation::where('id', $request->get('id'))->firstOrFail();
            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Designation name not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing name
            $designation->designation_name = $request->get('designation_name');
            $designation->save();

            return response()->json([
                'success' => true,
                'message' => $designation
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

        Designation::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Designation name successfully deleted"
        ], Response::HTTP_OK);
    }
}
