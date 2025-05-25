<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ItemCategoryController extends Controller
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
        //         'message' => ItemCategory::orderBy('name', 'ASC')->get()
        //     ]
        //     , Response::HTTP_OK);
        $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    $query = ItemCategory::orderBy('name', 'ASC');

    if (!empty($from_date) && !empty($to_date)) {
        $query->whereBetween('created_at', [$from_date, $to_date]);
    }

    $categories = $query->get();

    return \response()->json([
        'success' => true,
        'message' => $categories
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
                'message' => ItemCategory::orderBy('name', 'ASC')->paginate((int) $request->get('limit'))
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
            // Validate information
            $validator = Validator::make($request->only('name'),[
                'name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            // Create new Room Category
            $itemCategory = new ItemCategory();
            $itemCategory->name = $request->get('name');
            $itemCategory->description = $request->get('description');
            $itemCategory->type = $request->get('type');
            $itemCategory->save();
            return response()->json([
                'success' => true,
                'message' => $itemCategory->id,
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => "Internal Server Error."
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
        $itemCategory = ItemCategory::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'data' => $itemCategory
            ]
            , Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        try {
            // Validate information
            $validator = Validator::make($request->only('id','name'),[
                'id' => 'required|integer',
                'name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            // Create new Room Category
            $itemCategory = ItemCategory::where('id', $request->get('id'))->first();
            if(empty($itemCategory)){
                return response()->json([
                    'success' => false,
                    'message' => "Catégorie de chambre non retrouvée."
                ], Response::HTTP_NOT_FOUND);
            }

            $itemCategory->name = $request->get('name');
            $itemCategory->description = $request->get('description');
            $itemCategory->type = $request->get('type');
            $itemCategory->save();
            return response()->json([
                'success' => true,
                'message' => $itemCategory->id
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => "Internal Server Error."
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

        ItemCategory::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Category Successfully deleted."
        ], Response::HTTP_OK);
    }
}
