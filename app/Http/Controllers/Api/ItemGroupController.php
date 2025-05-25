<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\ItemGroupCollection;
use App\Http\Resources\ItemGroupResource;
use App\Models\ItemGroup;
use App\Models\ItemGroupDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ItemGroupController extends Controller
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
                'message' => new ItemGroupCollection(ItemGroup::all())
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
            'message' => new ItemGroupCollection(ItemGroup::paginate((int) $request->get('limit')))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Create new Item Group
            $itemGroup = new ItemGroup();
            $itemGroup->group_name = $request->get('group_name');
            $itemGroup->taxable = $request->get('taxable');
            $itemGroup->attributes = $request->get('attributes');
            $itemGroup->type = $request->get('type');
            $itemGroup->unit = $request->get('unit');
            $itemGroup->images = $request->get('images');
            $itemGroup->returnable = $request->get('returnable');
            $itemGroup->dimension = $request->get('dimension');
            $itemGroup->weight_kg = $request->get('weight_kg');
            $itemGroup->manufacturer = $request->get('manufacturer');
            $itemGroup->brand = $request->get('brand');
            $itemGroup->sale_price = $request->get('cost_price');
            $itemGroup->currency = $request->get('currency');
            $itemGroup->description = $request->get('description');
            $itemGroup->save();

            $itemIds =  $request->get('items');
            foreach ($itemIds as $itemId){
                $res[] = ItemGroupDetail::firstOrCreate(
                    [
                        'group_id' => $itemGroup->id,
                        'item_id' => $itemId
                    ],
                );
            }
            return response()->json([
                'success' => true,
                'message' => $itemGroup->id
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
        $itemGroup = ItemGroup::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new ItemGroupResource($itemGroup)
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
            $validator = Validator::make($request->all(),[
                'id' => 'required|integer',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid itemGroup update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $itemGroup = ItemGroup::where('id', $request->get('id'))->firstOrFail();
            if(empty($itemGroup)){
                return response()->json([
                    'success' => false,
                    'message' => 'ItemGroup not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing ItemGroup
            $itemGroup->group_name = $request->get('group_name');
            $itemGroup->type = $request->get('type');
            $itemGroup->taxable = $request->get('taxable');
            $itemGroup->attributes = $request->get('attributes');
            $itemGroup->unit = $request->get('unit');
            $itemGroup->images = $request->get('images');
            $itemGroup->returnable = $request->get('returnable');
            $itemGroup->dimension = $request->get('dimension');
            $itemGroup->weight_kg = $request->get('weight_kg');
            $itemGroup->manufacturer = $request->get('manufacturer');
            $itemGroup->brand = $request->get('brand');
            $itemGroup->sale_price = $request->get('cost_price');
            $itemGroup->currency = $request->get('currency');
            $itemGroup->description = $request->get('description');
            $itemGroup->save();

            $itemIds =  $request->get('items');
            foreach ($itemIds as $itemId){
                $res[] = ItemGroupDetail::firstOrCreate(
                    [
                        'group_id' => $itemGroup->id,
                        'item_id' => $itemId
                    ],
                );
            }

            return response()->json([
                'success' => true,
                'message' => $itemGroup
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

        ItemGroup::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "ItemGroup successfully deleted"
        ], Response::HTTP_OK);
    }
}
