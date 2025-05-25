<?php

namespace App\Http\Controllers\Api;

use App\Dto\EventType;
use App\Dto\ReceiptDto;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\User;
use App\Mail\ReceiptCreatedMail;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\Client;
use App\Models\NotificationSettings;

use App\Helpers\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request  $request)
    {
$invoice_no = $request->get('invoice_no');
$limit = $request->get('limit');
$from_date = $request->get('from_date');
$to_date = $request->get('to_date');

$query = Receipt::orderBy('updated_at', 'DESC');

if (!empty($invoice_no)) {
    $query->where('id', $invoice_no);
}

if (!empty($from_date) && !empty($to_date)) {
    $query->whereBetween('created_at', [$from_date, $to_date]);
}

if (!empty($limit)) {
    $res = $query->paginate($limit);
} else {
    $res = $query->get();
}

return response()->json([
    'success' => true,
    'message' => $res
], Response::HTTP_OK);


        // $invoice_no = $request->get('invoice_no');
        // $limit =  $request->get('limit');
        // if(!empty($limit)){
        //     if(!empty($invoice_no)){
        //         $res = Receipt::where('id', $request->get('invoice_no'))->orderBy('updated_at', 'DESC')->paginate($limit);
        //     }else{
        //         $res = Receipt::orderBy('updated_at', 'DESC')->paginate($limit);
        //     }
        // }else{
        //     if(!empty($invoice_no)){
        //         $res = Receipt::where('id', $request->get('invoice_no'))->orderBy('updated_at', 'DESC')->get();
        //     }else{
        //         $res = Receipt::orderBy('updated_at', 'DESC')->get();
        //     }
        // }
        // return \response()->json([
        //         'success' => true,
        //         'message' => $res
        //     ], Response::HTTP_OK);
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
                'message' => Receipt::paginate((int) $request->get('limit'))
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
            $validator = Validator::make($request->only('invoice_no', 'amount_paid', 'paid_by_user_id', 'payment_date'),[
                'invoice_no' => 'required|string',
                'payment_date' => 'required|date',
                'amount_paid' => 'required|numeric',
                'paid_by_user_id' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Receipt data.'
                ], 400);
            }

            // Check Invoice Existence
            $invoice =  Invoice::where('invoice_no', $request->get('invoice_no'))->first();
            if(empty($invoice)){
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not fount.'
                ], 400);
            }

            // Create new Receipt name
            $receipt = new Receipt();
            $receipt->invoice_no = $invoice->invoice_no;
            $receipt->payment_date = new \DateTime($request->get('payment_date'));
            $receipt->paid_by_user_id =  $request->get('paid_by_user_id');
            $receipt->amount_paid =  $request->get('amount_paid');
            $receipt->invoice_id = $invoice->id;
            $receipt->currency = $invoice->currency;
            $receipt->save();
            
             $invoice->total_amount_paid += $request->get('amount_paid');
            $invoice->save();

            $payer = User::where('id',  $request->get('paid_by_user_id'))->first();
             $client = $invoice->client;

            // Raise Events Receipt event
            if(!empty($payer) && !empty($client)){
                $emailNotification = NotificationSettings::where('trigger_name', 'RECEIPT')->where('send_email', 1)->first();
            
                $trans_date = $receipt->payment_date->format('d-m-Y');
                //  $payer->email;
            if ($emailNotification) {
            $mail = new ReceiptCreatedMail(
            $payer->first_name.' '.$payer->last_name,        
            $client->user->first_name.' '.$client->user->last_name,   
            $receipt->invoice_no,    
            $receipt->id,    
            $trans_date,    
            $invoice->payment_name,    
            $invoice->payment_amount,    
            $invoice->currency,    
            
            $request->get('amount_paid'));
                Helper::sendEmail($payer->email, $mail);
            }

       
                $smsNotification = NotificationSettings::where('trigger_name', 'RECEIPT')->where('send_sms', 1)->first();
                if ($smsNotification) {
                    $message = TwilioSMSController::receiptCreatedMessage($payer->first_name.' '.$payer->last_name,        
                    $client->user->first_name.' '.$client->user->last_name,   
                    $receipt->invoice_no,    
                    $receipt->id,    
                    $trans_date,    
                    $invoice->payment_name,    
                    $invoice->payment_amount,    
                    $invoice->currency,    
            $request->get('amount_paid'));
                    Helper::sendSms($payer->phone_num, $message);
                }
                        // $invoiceDto = new ReceiptDto($payer->first_name.' '.$payer->last_name,$client->user->first_name.' '.$client->user->last_name, $invoice->payment_name, $invoice->payment_amount, $invoice->currency, date('d-m-Y', strtotime($invoice->due_date)), $request->get('payment_link'),EventType::INVOICE_CREATED);
                        // event(new InvoiceEvent($invoiceDto, $client->user->email, $client->user->id, $client->user->phone_num));
            }    

            return response()->json([
                'success' => true,
                'message' => $receipt->id
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
        $receipt = Receipt::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $receipt
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
            $validator = Validator::make($request->only('id', 'invoice_no', 'amount_paid', 'paid_by_user_id', 'payment_date'),[
                'id' => 'required|integer',
                'invoice_no' => 'required|string',
                'payment_date' => 'required|date',
                'amount_paid' => 'required|numeric',
                'paid_by_user_id' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Receipt data.'
                ], 400);
            }

            // Check Invoice Existence
            $invoice =  Invoice::where('invoice_no', $request->get('invoice_no'))->first();
            if(empty($invoice)){
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not fount.'
                ], 400);
            }

            // Create new Receipt name
            $receipt = Receipt::where('id', $request->get('id'))->first();
            if(empty($receipt)){
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $receipt->invoice_no = $invoice->invoice_no;
            $receipt->payment_date = new \DateTime($request->get('payment_date'));
            $receipt->paid_by_user_id =  $request->get('paid_by_user_id');
            $receipt->amount_paid =  $request->get('amount_paid');
            $receipt->invoice_id = $invoice->id;
            $receipt->currency = $invoice->currency;
            $receipt->save();
            return response()->json([
                'success' => true,
                'message' => $receipt
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

        Receipt::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Receipt successfully deleted"
        ], Response::HTTP_OK);
    }
}
