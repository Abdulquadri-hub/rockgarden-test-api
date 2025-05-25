<?php

namespace App\Http\Controllers\Api;

use App\Dto\EventType;
use App\Dto\InvoiceDto;
use App\Dto\LowStockDto;
use App\Events\InvoiceEvent;
use App\Events\LowStockEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\SaleOrderCollection;
use App\Http\Resources\SaleOrderResource;
use App\Http\Services\UserService;
use App\Http\Services\SaleOrderService;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\Client;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\NotificationSettings;
use App\Mail\LowStockMail;
use App\Models\Item;
use App\Models\SaleOrder;
use App\Models\SaleOrderDetail;
use App\Models\Stock;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class SaleOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
     
    // public function index(Request  $request)
    // {
    //     // $client_id = $request->get('client_id');
    //     // $created_by_user_id = $request->get('created_by_user_id');
    //     // if (!empty($created_by_user_id) && !empty($client_id)){
    //     //     $res = SaleOrder::where('client_id', $client_id)
    //     //         ->where('created_by_user_id', $created_by_user_id)
    //     //         ->paginate($request->get('limit'));
    //     // }else if(!empty($created_by_user_id)){
    //     //     $res = SaleOrder::where('created_by_user_id', $created_by_user_id)
    //     //         ->paginate($request->get('limit'));
    //     // }else if (!empty($client_id)){
    //     //     $res = SaleOrder::where('client_id', $client_id)
    //     //         ->paginate($request->get('limit'));
    //     // }else{
    //     //     return \response()->json([
    //     //         'success' => true,
    //     //         'message' => new SaleOrderCollection(SaleOrder::orderBy('created_at', 'DESC')->paginate($request->get('limit')))
    //     //     ], Response::HTTP_OK);
    //     // }

    //     // return \response()->json([
    //     //     'success' => true,
    //     //     'message' => new SaleOrderCollection($res)
    //     // ], Response::HTTP_OK);
    //     $client_id = $request->get('client_id');
    // $from_date = $request->get('from_date');
    // $to_date = $request->get('to_date');
    // $created_by_user_id = $request->get('created_by_user_id');

    // $query = SaleOrder::orderBy('created_at', 'DESC');

    // if (!empty($created_by_user_id) && !empty($client_id)) {
    //     $query->where('client_id', $client_id)
    //         ->where('created_by_user_id', $created_by_user_id);
    // } elseif (!empty($created_by_user_id)) {
    //     $query->where('created_by_user_id', $created_by_user_id);
    // } elseif (!empty($client_id)) {
    //     $query->where('client_id', $client_id);
    // }

    // if (!empty($from_date) && !empty($to_date)) {
    //     $query->whereBetween('created_at', [$from_date, $to_date]);
    // }

    // $limit = $request->get('limit');

    // if (!empty($limit)) {
    //     $res = $query->paginate((int) $limit);
    //     return response()->json([
    //         'success' => true,
    //         'message' => new SaleOrderCollection($res)
    //     ], Response::HTTP_OK);
    // }

    // $res = $query->get();


    // return response()->json([
    //     'success' => true,
    //     'message' => new SaleOrderCollection($res)
    // ], Response::HTTP_OK);
    // }
    
    public function index(Request $request)
    {
        
        $client_id = $request->get('client_id');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $created_by_user_id = $request->get('created_by_user_id');

       // Default limit to avoid loading too many records
       $limit = $request->get('limit', 500);

       $query = SaleOrder::orderBy('created_at', 'DESC');

        if (!empty($created_by_user_id) && !empty($client_id)) {
            $query->where('client_id', $client_id)
                  ->where('created_by_user_id', $created_by_user_id);
        } elseif (!empty($created_by_user_id)) {
            $query->where('created_by_user_id', $created_by_user_id);
        } elseif (!empty($client_id)) {
            $query->where('client_id', $client_id);
        }

        if (!empty($from_date) && !empty($to_date)) {
                $query->whereBetween('created_at', [$from_date, $to_date]);
        }

        $res = $query->paginate((int) $limit);
    
        return response()->json([
            'success' => true,
            'message' => new SaleOrderCollection($res)
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
            'message' => new SaleOrderCollection(SaleOrder::paginate((int) $request->get('limit')))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
           $saleOrderService = new SaleOrderService();
        return $saleOrderService->createSaleOrder($request);
    }
    
 public function getItemSales(Request $request)
{
    
    // return $request;
    $startDate = $request->input('start_date'); 
    $endDate = $request->input('end_date');     

     $itemsWithSales = Item::select('items.id', 'items.name')
    ->selectRaw('SUM(sale_orders.total_order) as total_sold') 
    ->selectRaw('items.current_stock_level as stock_level') 
    ->leftJoin('sale_orders', 'items.id', '=', 'sale_orders.item_id')
    ->whereBetween('sale_orders.created_at', [$startDate, $endDate])
    ->groupBy('items.id', 'items.name', 'items.current_stock_level')
    ->orderBy('total_sold', 'desc')
    ->get();




    return response()->json([
        'success' => true,
        'message' => $itemsWithSales
    ], Response::HTTP_OK);
}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $saleOrder = SaleOrder::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new SaleOrderResource($saleOrder)
            ]
            , Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'id not found'
                ], Response::HTTP_BAD_REQUEST);
            }

            $saleOrder = SaleOrder::where('id', $request->get('id'))->first();

            Invoice::where('invoice_no', $saleOrder->invoice_no)->delete();
            SaleOrder::where('id', $request->get('id'))->delete();
            return response()->json([
                'success' => true,
                'message' => "Sale Order successfully deleted"
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }
}
