<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PurchaseOrderCollection;
use App\Http\Resources\PurchaseOrderResource;
use App\Http\Services\UserService;
use App\Models\Client;
use App\Models\Employee;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Stock;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request  $request)
    {
        $client_id = $request->get('client_id');
        $staff_id = $request->get('staff_id');
        if (!empty($staff_id) && !empty($client_id)){
            $res = PurchaseOrder::where('client_id', $client_id)
                ->where('staff_id', $staff_id)
                ->paginate($request->get('limit'));
        }else if(!empty($staff_id)){
            $res = PurchaseOrder::where('staff_id', $staff_id)
                ->paginate($request->get('limit'));
        }else if (!empty($client_id)){
            $res = PurchaseOrder::where('client_id', $client_id)
                ->paginate($request->get('limit'));
        }else{
            return \response()->json([
                'success' => true,
                'message' => new PurchaseOrdercollection(PurchaseOrder::orderBy('created_at', 'DESC')->paginate($request->get('limit')))
            ], Response::HTTP_OK);
        }

        return \response()->json([
            'success' => true,
            'message' => new PurchaseOrdercollection($res)
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
            'message' => new PurchaseOrdercollection(PurchaseOrder::paginate((int) $request->get('limit')))
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
                'vendor_id' => 'required|string',
                'client_id' => 'required|boolean',
                'order_date' => 'required|string',
                'status' => 'required|string',
                'invoiced' => 'required|boolean',
                'payment' => 'required|numeric',
                'delivery_method' => 'required|numeric',
                'staff_id' => 'required|numeric',
                'shipping_charges' => 'required|numeric',
                'total' => 'required|string',
                'order_details' => 'required|array'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale Order invalid data.'
                ], 400);
            }

            // Find Vendor
            $vendor = Vendor::where('id', $request->get('vendor_id'))->first();
            if(empty($vendor)){
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found.'
                ], 404);
            }

            $client = Client::where('id', $request->get('client_id'))->first();
            if(empty($client)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found.'
                ], 404);
            }

            $staff = Employee::where('id', $request->get('staff_id'))->first();
            if(empty($staff)){
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found.'
                ], 404);
            }

            // Create new Sale Order
            $userService = new UserService();
            $purchaseOrder = new PurchaseOrder();
            $latestOrder = PurchaseOrder::orderBy('created_at','DESC')->first();
            $purchaseOrder->order_no = 'SLO'.$userService->generateCode($latestOrder === null ? 1 : ($latestOrder->id + 1));
            $purchaseOrder->vendor_id = $request->get('vendor_id');
            $purchaseOrder->client_id = $request->get('client_id');
            $purchaseOrder->reference = $request->get('reference');
            $purchaseOrder->order_date = new \DateTime($request->get('order_date'));
            $purchaseOrder->shipment_date = new \DateTime($request->get('shipment_date'));
            $purchaseOrder->shipment_preference = $request->get('shipment_preference');
            $purchaseOrder->discount = $request->get('discount');
            $purchaseOrder->status = $request->get('status');
            $purchaseOrder->invoiced = $request->get('invoiced');
            $purchaseOrder->payment = $request->get('payment');
            $purchaseOrder->delivery_method = $request->get('delivery_method');
            $purchaseOrder->staff_id = $request->get('staff_id');
            $purchaseOrder->uploaded_file = $request->get('uploaded_file');
            $purchaseOrder->terms = $request->get('terms');
            $purchaseOrder->notes = $request->get('notes');
            $purchaseOrder->total = $request->get('total');
            $purchaseOrder->adjustment = $request->get('adjustment');
            $purchaseOrder->save();

            $order_details =  $request->get('items');
            foreach ($order_details as $order_detail){
                $stock =  Stock::where('id', $order_detail['stock_id'])->first();
                if(!empty($stock)){
                    $details = new PurchaseOrderDetail();
                    $details->stock_id = $order_detail['stock_id'];
                    $details->order_id = $purchaseOrder->id;
                    $details->item_id = $order_detail['item_id'];
                    $details->group_item_id = $order_detail['group_item_id'];
                    $details->quantity = $order_detail['quantity'];
                    $details->discount = $order_detail['discount'];
                    $details->amount = $order_detail['amount'];
                    $details->tax_id = $order_detail['tax_id'];
                    $details->currency = $order_detail['currency'];
                    $details->save();
                }
            }
            return response()->json([
                'success' => true,
                'message' => $purchaseOrder->id
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
        $purchaseOrder = PurchaseOrder::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new PurchaseOrderResource($purchaseOrder)
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
                'vendor_id' => 'required|string',
                'client_id' => 'required|boolean',
                'order_date' => 'required|string',
                'status' => 'required|string',
                'invoiced' => 'required|boolean',
                'payment' => 'required|numeric',
                'delivery_method' => 'required|numeric',
                'staff_id' => 'required|numeric',
                'shipping_charges' => 'required|numeric',
                'total' => 'required|string',
                'order_details' => 'required|array'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid purchaseOrder update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $purchaseOrder = PurchaseOrder::where('id', $request->get('id'))->firstOrFail();
            if(empty($purchaseOrder)){
                return response()->json([
                    'success' => false,
                    'message' => 'PurchaseOrder not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Find Vendor
            $vendor = Vendor::where('id', $request->get('vendor_id'))->first();
            if(empty($vendor)){
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found.'
                ], 404);
            }

            $client = Client::where('id', $request->get('client_id'))->first();
            if(empty($client)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found.'
                ], 404);
            }

            $staff = Employee::where('id', $request->get('staff_id'))->first();
            if(empty($staff)){
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found.'
                ], 404);
            }

            // Replace existing PurchaseOrder
            $purchaseOrder->vendor_id = $request->get('vendor_id');
            $purchaseOrder->client_id = $request->get('client_id');
            $purchaseOrder->reference = $request->get('reference');
            $purchaseOrder->order_date = new \DateTime($request->get('order_date'));
            $purchaseOrder->shipment_date = new \DateTime($request->get('shipment_date'));
            $purchaseOrder->shipment_preference = $request->get('shipment_preference');
            $purchaseOrder->discount = $request->get('discount');
            $purchaseOrder->status = $request->get('status');
            $purchaseOrder->invoiced = $request->get('invoiced');
            $purchaseOrder->payment = $request->get('payment');
            $purchaseOrder->delivery_method = $request->get('delivery_method');
            $purchaseOrder->staff_id = $request->get('staff_id');
            $purchaseOrder->uploaded_file = $request->get('uploaded_file');
            $purchaseOrder->terms = $request->get('terms');
            $purchaseOrder->notes = $request->get('notes');
            $purchaseOrder->total = $request->get('total');
            $purchaseOrder->adjustment = $request->get('adjustment');
            $purchaseOrder->save();

            $order_details =  $request->get('items');
            foreach ($order_details as $order_detail){
                $stock =  Stock::where('id', $order_detail['stock_id'])->first();
                if(!empty($stock)){
                    $details = new PurchaseOrderDetail();
                    $details->stock_id = $order_detail['stock_id'];
                    $details->order_id = $purchaseOrder->id;
                    $details->item_id = $order_detail['item_id'];
                    $details->group_item_id = $order_detail['group_item_id'];
                    $details->quantity = $order_detail['quantity'];
                    $details->discount = $order_detail['discount'];
                    $details->amount = $order_detail['amount'];
                    $details->tax_id = $order_detail['tax_id'];
                    $details->currency = $order_detail['currency'];
                    $details->save();
                }
            }
            return response()->json([
                'success' => true,
                'message' => $purchaseOrder
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

        PurchaseOrder::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "PurchaseOrder successfully deleted"
        ], Response::HTTP_OK);
    }
}
