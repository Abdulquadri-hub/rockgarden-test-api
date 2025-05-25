<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $client_id = $request->get('client_id');
        $customer_user_id = $request->get('');
        $limit=  $request->get('limit');
        $tx = [];
        if(!empty($limit)){
            if (!empty($client_id)){
                $tx = Transaction::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($customer_user_id)){
                $tx = Transaction::where('customer_user_id', $customer_user_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }else{
            if (!empty($client_id)){
                $tx = Transaction::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($customer_user_id)){
                $tx = Transaction::where('customer_user_id', $customer_user_id)->orderBy('updated_at', 'DESC')->get();
            }
        }

        return \response()->json([
            'success' => true,
            'message' => new TransactionCollection($tx)
        ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function indexPaged(Request $request)
    {
        // $tx = [];
        // $client_id = $request->get('client_id');
        // $customer_user_id = $request->get('');
        // $limit=  $request->get('limit');

        // if(!empty($limit)){
        //     if (!empty($client_id)){
        //         $tx = Transaction::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit);
        //     }elseif (!empty($customer_user_id)){
        //         $tx = Transaction::where('customer_user_id', $customer_user_id)->orderBy('updated_at', 'DESC')->paginate($limit);
        //     }else{
        //         $tx = Transaction::orderBy('updated_at', 'DESC')->paginate($limit);
        //     }
        // }else{
        //     if (!empty($client_id)){
        //         $tx = Transaction::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->get();
        //     }elseif (!empty($customer_user_id)){
        //         $tx = Transaction::where('customer_user_id', $customer_user_id)->orderBy('updated_at', 'DESC')->get();
        //     }else{
        //         $tx = Transaction::orderBy('updated_at', 'DESC')->get();
        //     }
        // }

        // return \response()->json([
        //     'success' => true,
        //     'message' => new TransactionCollection($tx)
        // ], Response::HTTP_OK);
        $tx = [];
    $client_id = $request->get('client_id');
    $customer_user_id = $request->get('customer_user_id');
    $limit = $request->get('limit');
    $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    $query = Transaction::orderBy('updated_at', 'DESC');

    if (!empty($client_id)) {
        $query->where('client_id', $client_id);
    }

    if (!empty($customer_user_id)) {
        $query->where('customer_user_id', $customer_user_id);
    }

    if (!empty($from_date) && !empty($to_date)) {
        $query->whereBetween('updated_at', [$from_date, $to_date]);
    }

    if (!empty($limit)) {
        $tx = $query->paginate($limit);
    } else {
        $tx = $query->get();
    }

    return response()->json([
        'success' => true,
        'message' => new TransactionCollection($tx)
    ], Response::HTTP_OK);
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $tx = Transaction::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new TransactionResource($tx)
            ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
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

        Transaction::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Transaction successfully deleted"
        ], Response::HTTP_OK);
    }
}
