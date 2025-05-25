<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ShiftController extends Controller
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
                'message' => Shift::orderBy('start_time','ASC')->get()
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
                'message' => Shift::orderBy('start_time','ASC')->paginate((int) $request->get('limit'))
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
                'start_time' => 'required',
                'end_time' => 'required',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid Shift information's."
                ], 400);
            }
            // Create new Shift
            $shift = new Shift();
            $shift->shift_name =  $request->get('shift_name');
            $shift->start_time = date("H:i", strtotime($request->get('start_time'))) ;
            $shift->end_time = date("H:i", strtotime($request->get('end_time'))) ;
            $shift->save();
            return response()->json([
                'success' => true,
                'message' => $shift->id
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
        $Shift = Shift::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $Shift
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
                'start_time' => 'required',
                'end_time' => 'required',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid Shift update information's."
                ], Response::HTTP_BAD_REQUEST);
            }

            $shift = Shift::where('id', $request->get('id'))->firstOrFail();
            if(empty($shift)){
                return response()->json([
                    'success' => false,
                    'message' => 'Shift not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Data
            $shift->shift_name =  $request->get('shift_name');
            $shift->start_time = date("H:i", strtotime($request->get('start_time'))) ;
            $shift->end_time = date("H:i", strtotime($request->get('end_time'))) ;
            $shift->save();

            return response()->json([
                'success' => true,
                'message' => $shift
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

        Shift::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Shift successfully deleted"
        ], Response::HTTP_OK);
    }
}
