<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\MedicalHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class MedicalHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // return \response()->json([
        //         'success' => true,
        //         'message' => MedicalHistory::all()
        //     ]
        //     , Response::HTTP_OK);
        $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    if (!empty($from_date) && !empty($to_date)) {
        $res = MedicalHistory::whereBetween('created_at', [$from_date, $to_date])->get();
    } else {
        $res = MedicalHistory::all();
    }

    return response()->json([
        'success' => true,
        'message' => $res
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
                'message' => MedicalHistory::paginate((int) $request->get('limit'))
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
            $validator = Validator::make($request->only('client_id', 'initial_date_of_occurrence', 'date_description', 'medical_history_details'),[
                'initial_date_of_occurrence' => 'required|date',
                'date_description' => 'required|string',
                'medical_history_details' => 'required|date',
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Medical History data.'
                ], 400);
            }

            // Check Client Existence
            $client =  Client::where('id', $request->get('client_id'))->first();
            if(empty($client)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not fount.'
                ], 400);
            }

            // Create new MedicalHistory name
            $medicalHistory = new MedicalHistory();
            $medicalHistory->initial_date_of_occurrence = new \DateTime($request->get('initial_date_of_occurrence'));
            $medicalHistory->date_description =  $request->get('date_description');
            $medicalHistory->medical_history_details =  $request->get('medical_history_details');
            $medicalHistory->client_id = $request->get('client_id');
            $medicalHistory->save();
            return response()->json([
                'success' => true,
                'message' => $medicalHistory->id
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
        $medicalHistory = MedicalHistory::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $medicalHistory
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
            $validator = Validator::make($request->only('client_id', 'initial_date_of_occurrence', 'date_description', 'medical_history_details'),[
                'initial_date_of_occurrence' => 'required|date',
                'date_description' => 'required|string',
                'medical_history_details' => 'required|date',
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Medical History data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new MedicalHistory name
            $medicalHistory = MedicalHistory::where('id', $request->get('id'))->first();
            if(empty($medicalHistory)){
                return response()->json([
                    'success' => false,
                    'message' => 'Medical History not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $medicalHistory->initial_date_of_occurrence = new \DateTime($request->get('initial_date_of_occurrence'));
            $medicalHistory->date_description =  $request->get('date_description');
            $medicalHistory->medical_history_details =  $request->get('medical_history_details');
            $medicalHistory->client_id = $request->get('client_id');
            $medicalHistory->save();
            return response()->json([
                'success' => true,
                'message' => $medicalHistory
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

        MedicalHistory::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Medical History successfully deleted"
        ], Response::HTTP_OK);
    }
}
