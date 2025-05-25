<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockCollection;
use App\Http\Resources\StockResource;
use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\Stock;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // $limit = $request->get('limit');
        // if(!empty($limit)){
        //     return \response()->json([
        //         'success' => true,
        //         'message' => new StockCollection(Stock::orderBy('created_at', 'DESC')->paginate((int) $limit))
        //     ], Response::HTTP_OK);
        // }
        // return \response()->json([
        //         'success' => true,
        //         'message' => new StockCollection(Stock::orderBy('created_at', 'DESC')->get())
        //     ]
        //     , Response::HTTP_OK);
        $limit = $request->get('limit');
    $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    $query = Stock::orderBy('created_at', 'DESC');

    if (!empty($from_date) && !empty($to_date)) {
        $query->whereBetween('created_at', [$from_date, $to_date]);
    }

    if (!empty($limit)) {
        $res = $query->paginate((int) $limit);
        return \response()->json([
            'success' => true,
            'message' => new StockCollection($res)
        ], Response::HTTP_OK);
    }

    $res = $query->get();

    return \response()->json([
        'success' => true,
        'message' => new StockCollection($res)
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
            'message' => new StockCollection(Stock::paginate((int) $request->get('limit')))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only( 'item_name', 'stock_entry'),[
                'item_name' => 'required|string',
                'stock_entry' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock invalid data.'
                ], 400);
            }

            // Create new Stock
            $item_name =   $request->get('item_name');
            if(empty($item_name)){
                return response()->json([
                    'success' => false,
                    'message' => 'No item found.'
                ], 400);
            }
            // Check Item or ItemGroup Existence
            $stock = new Stock();

            $item = Item::where('name', $item_name)->first();
            if(empty($item)){
                return response()->json([
                    'success' => false,
                    'message' => 'No item found.'
                ], 400);
            }
            $stock->item_id = $item->id;
            $stock->item_name = $request->get('item_name');
            $stock->unit = $item->unit;
            $stock->item_category =  $item->category_name;
            $stock->stock_level_before = $item->current_stock_level;
            $item->current_stock_level += $request->get('stock_entry');
            $stock->stock_level_after = $item->current_stock_level ;

            if($stock->stock_level_after < 0){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid stock entry.'
                ], 400);
            }

            $stock->created_by_user_id = Auth::user()->id;
            $stock->item_name = $request->get('item_name');
            $stock->stock_entry = $request->get('stock_entry');

            $item->save();
            $stock->save();
            return response()->json([
                'success' => true,
                'message' => $stock->id
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
        $stock = Stock::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new StockResource($stock)
            ]
            , Response::HTTP_OK);
    }


//    /**
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function update(Request $request)
//    {
//        try {
//            // validate request data
//            $validator = Validator::make($request->only('id', 'item_name', 'stock_entry'),[
//                'id' => 'required|integer',
//                'item_name' => 'required|string',
//                'stock_entry' => 'required|integer'
//            ]);
//
//            if($validator->fails()) {
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Invalid stock update information'
//                ], Response::HTTP_BAD_REQUEST);
//            }
//
//            $stock = Stock::where('id', $request->get('id'))->firstOrFail();
//            if(empty($stock)){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Stock not found'
//                ], Response::HTTP_NOT_FOUND);
//            }
//            // Replace existing Stock
//            // Create new Stock
//            $item_name =   $request->get('item_name');
//            $item_group_id =   $request->get('item_group_id');
//            if(!empty($item_name)){
//                $item = Item::where('name', $item_name)->first();
//                $stock->item_id = $item->id;
//                $stock->item_name = $request->get('item_name');
//                $stock->unit = $item->unit;
//                $stock->item_category =  $item->category_name;
//                $stock->stock_level_before = $stock->stock;
//                $stock->stock_level_after = $stock->stock + $request->get('stock_entry');
//                $item->current_stock_level = $item->current_stock_level + $request->get('stock_entry');
//                $stock->created_by_user_id = Auth::user()->id;
//                $item->save();
//            }elseif(!empty($item_group_id)){
//                $itemGroup = ItemGroup::where('id', $item_group_id)->first();
//                $stock->item_group_id = $itemGroup->id;
//            }else{
//                return response()->json([
//                    'success' => false,
//                    'message' => 'No item found.'
//                ], 400);
//            }
//
//            $vendor = Vendor::where('id', $request->get('vendor_id'))->first();
//            if(empty($vendor)){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Vendor not found.'
//                ], 400);
//            }
//
//            $stock->vendor_id = $vendor->id;
//            $stock->unit = $request->get('unit');
//            $stock->stock = $request->get('stock');
//            $stock->item_name = $request->get('item_name');
//            $stock->item_category = $request->get('item_category');
//            $stock->stock_level_before = $request->get('stock_level_before');
//            $stock->stock_level_after = $request->get('stock_level_after');
//            $stock->stock_entry = $request->get('stock_entry');
//            $stock->created_by_user_id = $request->get('created_by_user_id');
//            $stock->save();
//
//            return response()->json([
//                'success' => true,
//                'message' => new StockResource($stock)
//            ], Response::HTTP_OK);
//        }catch (\Exception $e){
//            Log::error($e->getMessage());
//            return response()->json(
//                [
//                    'success' => false,
//                    'message' => $e->getMessage()
//                ], Response::HTTP_CONFLICT
//            );
//        }
//    }

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

        Stock::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Stock successfully deleted"
        ], Response::HTTP_OK);
    }
}
