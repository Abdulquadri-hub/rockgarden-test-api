<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicineName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class MedicineNameController extends Controller
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
                'message' => MedicineName::all()
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
                'message' => MedicineName::paginate((int) $request->get('limit'))
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
            $validator = Validator::make($request->only('medicine_name'),[
                'medicine_name' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Medicine Name data.'
                ], 400);
            }

            // Create new MedicineName name
            $medicineName = new MedicineName();
            $medicineName->medicine_name = $request->get('medicine_name');
            $medicineName->save();
            return response()->json([
                'success' => true,
                'message' => $medicineName->id
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
        $medicineName = MedicineName::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $medicineName
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
            $validator = Validator::make($request->only('id','medicine_name'),[
                'id' => 'required|integer',
                'medicine_name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Medicine Name data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new MedicineName name
            $medicineName = MedicineName::where('id', $request->get('id'))->firstOrFail();
            if(empty($medicineName)){
                return response()->json([
                    'success' => false,
                    'message' => 'Medicine Name not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $medicineName->medicine_name = $request->get('medicine_name');
            $medicineName->save();
            return response()->json([
                'success' => true,
                'message' => $medicineName
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

        MedicineName::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Medicine Name successfully deleted"
        ], Response::HTTP_OK);
    }
}
