<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\MedicationInTake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class MedicationInTakeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // return \response()->json([
        //         'success' => true,
        //         'message' => MedicationInTake::all()
        //     ]
        //     , Response::HTTP_OK);
        $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    if (!empty($from_date) && !empty($to_date)) {
        $res = MedicationInTake::whereBetween('created_at', [$from_date, $to_date])->get();
    } else {
        $res = MedicationInTake::all();
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
                'message' => MedicationInTake::paginate((int) $request->get('limit'))
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
            $validator = Validator::make($request->only('client_id', 'medication_date', 'medicine_name', 'dosage', 'dosage_given', 'status'),[
                'medication_date' => 'required|date',
                'medicine_name' =>  'required|string',
                'dosage' => 'required|string',
                'dosage_given' => 'required|numeric',
                'status' => 'required|string|max:15',
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Medication In Take data.'
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

            // Create new MedicationInTake name
            $medicationInTake = new MedicationInTake();
            $medicationInTake->medicine_name = $request->get('medicine_name');
            $medicationInTake->medication_date = new \DateTime($request->get('medication_date'));
            $medicationInTake->dosage = $request->get('dosage');
            $medicationInTake->dosage_given = $request->get('dosage_given');
            $medicationInTake->status = $request->get('status');
            $medicationInTake->client_id = $request->get('client_id');
            $medicationInTake->save();
            return response()->json([
                'success' => true,
                'message' => $medicationInTake->id
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
        $medicationInTake = MedicationInTake::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $medicationInTake
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
            $validator = Validator::make($request->only('id', 'client_id', 'medication_date', 'medicine_name', 'dosage', 'dosage_given', 'status'),[
                'id' => 'required|integer',
                'medication_date' => 'required|date',
                'medicine_name' =>  'required|string',
                'dosage' => 'required|string',
                'dosage_given' => 'required|numeric',
                'status' => 'required|string|max:15',
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Medication In Take data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new MedicationInTake name
            $medicationInTake = MedicationInTake::where('id', $request->get('id'))->first();
            if(empty($medicationInTake)){
                return response()->json([
                    'success' => false,
                    'message' => 'Medication In Take not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $medicationInTake->medicine_name = $request->get('medicine_name');
            $medicationInTake->medication_date = new \DateTime($request->get('medication_date'));
            $medicationInTake->dosage = $request->get('dosage');
            $medicationInTake->dosage_given = $request->get('dosage_given');
            $medicationInTake->status = $request->get('status');
            $medicationInTake->client_id = $request->get('client_id');
            $medicationInTake->save();
            return response()->json([
                'success' => true,
                'message' => $medicationInTake
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

        MedicationInTake::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Medication In Take successfully deleted"
        ], Response::HTTP_OK);
    }
}
