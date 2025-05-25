<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ItemController extends Controller
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
                'message' => new ItemCollection(Item::all())
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
        $type =  $request->get('type');
        $limit =  $request->get('limit');
        $category_name =  $request->get('category_name');

        $res =  [];

        if(!empty($limit)){
            if(!empty($type) && !empty($category_name)){
                $res = Item::where('type', $type)->where('category_name', $category_name)->orderBy('name')->paginate($limit);
            }elseif(!empty($type)){
                $res = Item::where('type', $type)->orderBy('name')->paginate($limit);
            }elseif(!empty($category_name)){
                $res = Item::where('category_name', $category_name)->orderBy('name')->paginate($limit);
            }else{
                $res = Item::orderBy('name')->paginate($limit);
            }
        }else{
            if(!empty($type) && !empty($category_name)){
                $res = Item::where('type', $type)->where('category_name', $category_name)->orderBy('name')->get();
            }elseif(!empty($type)){
                $res = Item::where('type', $type)->orderBy('name')->get();
            }elseif(!empty($category_name)){
                $res = Item::where('category_name', $category_name)->orderBy('name')->get();
            }else{
                $res = Item::orderBy('name')->get();
            }
        }

        return \response()->json([
            'success' => true,
            'message' => new ItemCollection($res)
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
                'name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item invalid data.'
                ], 400);
            }
            // Create new Item
            $item = new Item();
            $item->name = $request->get('name');
            $item->type = $request->get('type');
            $item->unit = $request->get('unit');
            $item->image1 = $request->get('image1');
            $item->image2 = $request->get('image2');
            $item->image3 = $request->get('image3');
            $item->returnable = $request->get('returnable');
            $item->dimension = $request->get('dimension');
            $item->weight_kg = $request->get('weight_kg');
            $item->manufacturer = $request->get('manufacturer');
            $item->brand = $request->get('brand');
            $item->cost_price = $request->get('cost_price');
            $item->sale_price = $request->get('sale_price');
            $item->currency = $request->get('currency');
            $item->description = $request->get('description');
            $item->category_name = $request->get('category_name');
            $item->sku = $request->get('sku');
            $item->reorder_level = $request->get('reorder_level');
            $item->current_stock_level = $request->get('current_stock_level');

            $vendor =  Vendor::where('id', $request->get('vendor_id'));
            if(!empty($vendor)){
                $item->vendor_id = $request->get('vendor_id');
            }
            $item->save();
            return response()->json([
                'success' => true,
                'message' => $item->id
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
        $name = $request->get('name');
        if(!empty($name)){
            $item = Item::where('name', $name)->first();
        }else{
            $item = Item::where('id', $request->get('id'))->first();
        }

        return \response()->json([
                'success' => true,
                'message' => new ItemResource($item)
            ], Response::HTTP_OK);
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
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid item update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $item = Item::where('id', $request->get('id'))->firstOrFail();
            if(empty($item)){
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Item
            $item->name = $request->get('name');
            $item->type = $request->get('type');
            $item->unit = $request->get('unit');
            $item->image1 = $request->get('image1');
            $item->image2 = $request->get('image2');
            $item->image3 = $request->get('image3');
            $item->returnable = $request->get('returnable');
            $item->dimension = $request->get('dimension');
            $item->weight_kg = $request->get('weight_kg');
            $item->manufacturer = $request->get('manufacturer');
            $item->brand = $request->get('brand');
            $item->cost_price = $request->get('cost_price');
            $item->sale_price = $request->get('sale_price');
            $item->currency = $request->get('currency');
            $item->description = $request->get('description');
            $item->category_name = $request->get('category_name');
            $item->sku = $request->get('sku');
            $item->reorder_level = $request->get('reorder_level');
            $item->current_stock_level = $request->get('current_stock_level');


            $vendor =  Vendor::where('id', $request->get('vendor_id'));
            if(!empty($vendor)){
                $item->vendor_id = $request->get('vendor_id');
            }

            $item->save();

            return response()->json([
                'success' => true,
                'message' => new ItemResource($item)
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

        Item::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Item successfully deleted"
        ], Response::HTTP_OK);
    }
}
