<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ProductTypeController extends Controller
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
                'message' => ProductType::all()
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
            'message' => ProductType::paginate((int) $request->get('limit'))
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
                    'message' => 'Product Type invalid data.'
                ], 400);
            }
            // Create new Product Type
            $productType = new ProductType();
            $productType->name = $request->get('name');
            $productType->category = $request->get('category');
            $productType->save();
            return response()->json([
                'success' => true,
                'message' => $productType->id
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
        $productType = ProductType::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $productType
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
                    'message' => 'Invalid product type update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $productType = ProductType::where('id', $request->get('id'))->firstOrFail();
            if(empty($productType)){
                return response()->json([
                    'success' => false,
                    'message' => 'Product Type not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Product Type
            $productType->name = $request->get('name');
            $productType->category = $request->get('category');
            $productType->save();

            return response()->json([
                'success' => true,
                'message' => $productType
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

        ProductType::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Product Type successfully deleted"
        ], Response::HTTP_OK);
    }
}
