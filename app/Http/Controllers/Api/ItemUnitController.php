<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemUnitRequest;
use App\Http\Requests\UpdateItemUnitRequest;
use App\Models\ItemUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ItemUnitController extends Controller
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
                'message' => ItemUnit::all()
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
            'message' => ItemUnit::paginate((int) $request->get('limit'))
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
                    'message' => 'Item Unit invalid data.'
                ], 400);
            }
            // Create new ItemUnit
            $itemUnit = new ItemUnit();
            $itemUnit->name = $request->get('name');
            $itemUnit->description = $request->get('description');
            $itemUnit->save();
            return response()->json([
                'success' => true,
                'message' => $itemUnit->id
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
        $itemUnit = ItemUnit::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $itemUnit
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
                    'message' => 'Invalid item unit update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $itemUnit = ItemUnit::where('id', $request->get('id'))->firstOrFail();
            if(empty($itemUnit)){
                return response()->json([
                    'success' => false,
                    'message' => 'Item Unit not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing ItemUnit
            $itemUnit->name = $request->get('name');
            $itemUnit->description = $request->get('description');
            $itemUnit->save();

            return response()->json([
                'success' => true,
                'message' => $itemUnit
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

        ItemUnit::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Item Unit successfully deleted"
        ], Response::HTTP_OK);
    }
}
