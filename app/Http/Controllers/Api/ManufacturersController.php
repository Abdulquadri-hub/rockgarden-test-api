<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manufacturers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ManufacturersController extends Controller
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
                'message' => Manufacturers::all()
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
            'message' => Manufacturers::paginate((int) $request->get('limit'))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only('name'),[
                'name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Manufacturers invalid data.'
                ], 400);
            }
            // Create new Manufacturers
            $manufacturers = new Manufacturers();
            $manufacturers->name = $request->get('name');
            $manufacturers->description = $request->get('description');
            $manufacturers->save();
            return response()->json([
                'success' => true,
                'message' => $manufacturers->id
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
        $manufacturers = Manufacturers::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $manufacturers
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
            $validator = Validator::make($request->only('id', 'name'),[
                'id' => 'required|integer',
                'name' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid manufacturers update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $manufacturers = Manufacturers::where('id', $request->get('id'))->firstOrFail();
            if(empty($manufacturers)){
                return response()->json([
                    'success' => false,
                    'message' => 'Manufacturers not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Manufacturers
            $manufacturers->name = $request->get('name');
            $manufacturers->description = $request->get('description');
            $manufacturers->save();

            return response()->json([
                'success' => true,
                'message' => $manufacturers
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

        Manufacturers::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Manufacturers successfully deleted"
        ], Response::HTTP_OK);
    }
}
