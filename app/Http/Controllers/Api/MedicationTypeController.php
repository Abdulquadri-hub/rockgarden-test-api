<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class MedicationTypeController extends Controller
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
                'message' => MedicationType::all()
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
                'message' => MedicationType::paginate((int) $request->get('limit'))
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
            $validator = Validator::make($request->only('medication_type'),[
                'medication_type' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Medication Type data.'
                ], 400);
            }

            // Create new MedicationType name
            $medicationType = new MedicationType();
            $medicationType->medication_type = $request->get('medication_type');
            $medicationType->save();
            return response()->json([
                'success' => true,
                'message' => $medicationType->id
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
        $medicationType = MedicationType::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $medicationType
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
            $validator = Validator::make($request->only('id','medication_type'),[
                'id' => 'required|integer',
                'medication_type' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Medication Type data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new MedicationType name
            $medicationType = MedicationType::where('id', $request->get('id'))->firstOrFail();
            if(empty($medicationType)){
                return response()->json([
                    'success' => false,
                    'message' => 'Medication Type not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $medicationType->medication_type = $request->get('medication_type');
            $medicationType->save();
            return response()->json([
                'success' => true,
                'message' => $medicationType
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

        MedicationType::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Medication Type successfully deleted"
        ], Response::HTTP_OK);
    }
}
