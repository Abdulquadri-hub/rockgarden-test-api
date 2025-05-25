<?php

namespace App\Http\Controllers\Api;

/*use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;*/

use App\Dto\EventType;
use App\Dto\InvoiceDto;
use App\Events\InvoiceEvent;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\ClientMedication;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Resources\InvoiceCollection;
use Illuminate\Support\Facades\File;
use App\Http\Resources\InvoiceResource;
use App\Models\Client;
use App\Models\StaffChart;
use App\Models\NotificationSettings;
use App\Models\FamilyFriendAssignment;
use App\Http\Resources\FriendFamilyAssignmentResource;
use App\Models\Invoice;
use App\Helpers\Helper;
use App\Lib\MedicationDosageHelper;
use App\Models\Receipt;
use App\Models\User;
use App\Models\StaffAssignment;
use App\Mail\InvoiceCreatedMail;
use App\Mail\ReceiptAfterPayMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use function auth;
use function response;
use App\Mail\InvoiceEmail;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {

        $user_id = Auth::user()->id;
        $client_ids =  FamilyFriendAssignment::where('familyfriend_id', $user_id)->pluck('client_id');
        $client = Client::where('user_id', $user_id)->first();

        if(!empty($client)){
            $client_ids[] = $client->id;
        }

        $limit = $request->get('limit');

        $res = [];
        if(!empty($limit)){
            $res = Invoice::whereIn('client_id', $client_ids)->orderBy('updated_at', 'DESC')->paginate($limit);
        }else{
            $res = Invoice::whereIn('client_id', $client_ids)->orderBy('updated_at', 'DESC')->get();
        }

        return \response()->json([
            'success' => true,
            'message' => new InvoiceCollection($res)
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
     
    // public function indexPaged(Request $request)
    // {
    //     // Not required fields
    //     // $client_id = $request->get('client_id');
    //     // $limit = $request->get('limit');

    //     // $res = [];
    //     // if(!empty($limit)){
    //     //     if (!empty($client_id)){
    //     //         $res = Invoice::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit);
    //     //     }else{
    //     //         $res = Invoice::orderBy('updated_at', 'DESC')->paginate($limit);
    //     //     }
    //     // }else{
    //     //     if (!empty($client_id)){
    //     //         $res = Invoice::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->get();
    //     //     }else{
    //     //         $res = Invoice::orderBy('updated_at', 'DESC')->get();
    //     //     }
    //     // }

    //     // return \response()->json([
    //     //     'success' => true,
    //     //     'message' => new InvoiceCollection($res)
    //     // ], Response::HTTP_OK);
    //     $client_id = $request->get('client_id');
    // $limit = $request->get('limit');
    // $from_date = $request->get('from_date');
    // $to_date = $request->get('to_date');

    // $query = Invoice::orderBy('updated_at', 'DESC');

    // if (!empty($client_id)) {
    //     $query->where('client_id', $client_id);
    // }

    // if (!empty($from_date) && !empty($to_date)) {
    //     $query->whereBetween('updated_at', [$from_date, $to_date]);
    // }

    // $res = [];

    // if (!empty($limit)) {
    //     $res = $query->paginate($limit);
    // } else {
    //     $res = $query->get();
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => new InvoiceCollection($res)
    // ], Response::HTTP_OK);
    // }
    
    
    /* UPDATE INDEXPAGED */
    
    public function indexPaged(Request $request)
    {
        // Not required fields
        $client_id = $request->get('client_id');
        $limit = $request->get('limit');
        $start_date = $request->get('startDate');
        $end_date = $request->get('endDate');

        $res = [];

        if (!empty($client_id)) 
        {
            if (empty($start_date) || empty($end_date)) 
            {
                if (empty($limit)) 
                {
                    $res =  Invoice::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->get();
                    
                }else{
                    
                    $res = Invoice::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit);
                }
        

            } else {
                
                if (empty($limit)) 
                {
            
                    $res = Invoice::where('client_id', $client_id)
                                    ->whereBetween('created_at', [$start_date, $end_date])
                                    ->orderBy('updated_at', 'DESC')
                                    ->get();
                }else{
                    
                    $res =  Invoice::where('client_id', $client_id)
                                ->whereBetween('created_at', [$start_date, $end_date])
                                ->orderBy('updated_at', 'DESC')
                                ->paginate($limit);
                }

            }
            
    
        } else {
            
            if (empty($start_date) || empty($end_date)) 
            {
                if (empty($limit)) 
                {
                    $res =  Invoice::orderBy('updated_at', 'DESC')->get(); 
                    
                }else{
                    
                    $res =  Invoice::orderBy('updated_at', 'DESC')->paginate($limit);
                }

                

            } else {
                
                if (empty($limit)) 
                {
                    $res =  Invoice::whereBetween('created_at', [$start_date, $end_date])
                                    ->orderBy('updated_at', 'DESC')
                                    ->get();
                }else{
                    
                    $res =  Invoice::whereBetween('created_at', [$start_date, $end_date])
                                ->orderBy('updated_at', 'DESC')
                                ->paginate($limit);
                }

            }
            
        }


        return \response()->json([
            'success' => true,
            'message' => new InvoiceCollection($res)
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Create new Invoice
            $invoice = new Invoice();

            $invoice->payment_name = $request->get('payment_name');
            $invoice->payment_amount =  $request->get('payment_amount');
            $invoice->total_amount_paid = $request->get('total_amount_paid');
            $invoice->due_date = $request->get('due_date');
            $invoice->payment_description = $request->get('payment_description');
            $invoice->is_monthly_recurrent = $request->get('is_monthly_recurrent');
            $invoice->next_charge_date = $request->get('next_charge_date');

            $client_id =  $request->get('client_id');
            if (!empty($client_id)){
                $client = Client::where('id', $client_id)->first();
                if(empty($client)){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Client not found for invoice."
                        ], Response::HTTP_BAD_REQUEST);
                }

                $invoice->client_id = $client->id;
            }

            $invoice->invoice_no = $request->get('invoice_no');
            $invoice->currency = $request->get('currency');
            $invoice->save();

            // Raise Events
            if(!empty($client)){
                $res = FamilyFriendAssignment::where('client_id', $client->id)->get();
                foreach (FriendFamilyAssignmentResource::collection($res) as $value) {
                    try{
                        if(!empty($value['friend'])){
                            
                            // Mail::to($value['friend']['email'])->send(new InvoiceCreatedMail($value['friend']['first_name'], $client->user->last_name.' '.$client->user->first_name,$invoice->payment_name, $invoice->payment_amount, $invoice->currency, date('d-m-Y', strtotime($invoice->due_date)), $request->get('payment_link')));
                        
                            // TwilioSMSController::sendSMS($value['friend']['phone_num'], TwilioSMSController::invoiceCreatedMessage($value['friend']['first_name'], $client->user->last_name.' '.$client->user->first_name,$invoice->payment_name, $invoice->payment_amount, $invoice->currency, date('d-m-Y', strtotime($invoice->due_date)), $request->get('payment_link')));
                        }
                    }catch (\Exception $e){}
                }
                $name = $client->user->first_name . " " . $client->user->last_name;
                $emailNotification = NotificationSettings::where('trigger_name', 'INVOICE_CREATED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'INVOICE_CREATED')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    foreach ( $client->friends as $friend) {
                        $email = $friend->email;
                      $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new InvoiceCreatedMail($name, $familyfriend_name,$invoice->payment_name, $invoice->payment_amount, $invoice->currency, date('d-m-Y', strtotime($invoice->due_date)), $request->get('payment_link'));
                        Helper::sendEmail($email, $mail);
                    }
                    
                       
                    
                    
                
                }
                
                if ($smsNotification) {
                    foreach ( $client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::invoiceCreatedMessage($name, $familyfriend_name,$invoice->payment_name, $invoice->payment_amount, $invoice->currency, date('d-m-Y', strtotime($invoice->due_date)), $request->get('payment_link'));
                        Helper::sendSms($phoneNumber, $message);
                    }
                
                        
                   
                }
                
             
            }

            return response()->json([
                'success' => true,
                'message' => $invoice->id
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

        $invoice_no = $request->get('invoice_no');
        $invoice = null;
        if(!empty($invoice_no)){
            $invoice = Invoice::where('invoice_no', $invoice_no)->first();
        }else{
            $invoice = Invoice::where('id', $request->get('id'))->first();
        }
        if ($invoice) {
        return response()->json([
            'success' => true,
            'message' => new InvoiceResource($invoice)
        ], Response::HTTP_OK);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Invoice not found'
        ], Response::HTTP_NOT_FOUND);
    }
        // return \response()->json([
        //         'success' => true,
        //         'message' => new InvoiceResource($invoice)
        //     ]
        //     , Response::HTTP_OK);
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
                'currency' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid invoice update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $invoice = Invoice::where('id', $request->get('id'))->firstOrFail();
            if(empty($invoice)){
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Invoice
            $invoice->payment_name = $request->get('payment_name');
            $invoice->payment_amount =  $request->get('payment_amount');
            $invoice->total_amount_paid = $request->get('total_amount_paid');
            $invoice->due_date = $request->get('due_date');
            $invoice->payment_description = $request->get('payment_description');
            $invoice->is_monthly_recurrent = $request->get('is_monthly_recurrent');
            $invoice->next_charge_date = $request->get('next_charge_date');

            $client_id =  $request->get('client_id');
            if (!empty($client_id)){
                $client = Client::where('id', $client_id)->first();
                if(empty($client)){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Client not found for invoice."
                        ], Response::HTTP_BAD_REQUEST);
                }

                $invoice->client_id = $client->id;
            }

            $invoice->invoice_no = $request->get('invoice_no');
            $invoice->currency = $request->get('currency');
            $invoice->save();

            return response()->json([
                'success' => true,
                'message' => $invoice
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

        Invoice::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Invoice successfully deleted"
        ], Response::HTTP_OK);
    }
    public function destroyMultiple(Request $request)
{
    $ids = $request->get('ids');

    if (is_string($ids)) {
        $ids = json_decode($ids, true);
    }

    if (!is_array($ids)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid format for IDs'
        ], Response::HTTP_BAD_REQUEST);
    }

    $invoices = Invoice::whereIn('id', $ids)->get();

    if ($invoices->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No invoices found with the given IDs'
        ], Response::HTTP_BAD_REQUEST);
    }

    Invoice::whereIn('id', $ids)->delete();

    return response()->json([
        'success' => true,
        'message' => 'Invoices successfully deleted'
    ], Response::HTTP_OK);
}

    public function get_invoices(Request $request)
    {
        //$token= $request->bearerToken();
        //$user = JWTAuth::authenticate($token);
        $user = auth()->user();;
        // $reference = $request->only('reference');
        if(isset($_GET['reference']) && !empty($_GET['reference'])){
            $reference = $_GET['reference'];
            $this->verify_payment_rave($reference);
        }

        // if(isset($_GET['client_id']) && !empty($_GET['client_id'])){
        //     $Id = $_GET['client_id'];
        //     $where = 'service_application.client_id';
        // }

        // if(isset($_GET['id']) && !empty($_GET['id'])){
        //     $Id = $_GET['id'];
        //     $where = 'id';
        // }

        // if(!$Id) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => []]);
        // }

        $invoices = DB::table('invoices')
        ->join('service_application', 'invoices.service_application_id', '=', 'service_application.id')
        ->where('service_application.applicant_id', $user->id)
        ->orWhere('service_application.client_id', $user->id)
        ->select('invoices.*', 'service_application.client_id', 'service_application.applicant_id')->orderBy('created_at', 'desc')->get();
        if(!$invoices) {
            return response()->json([
                'success' => true,
                'message' => []]);
        } else {
            foreach($invoices as $row) {
                $user = DB::table('users')->where('id', $row->client_id)->first();
                if($row->client_id != $row->applicant_id){
                    $applicant = DB::table('users')->where('id', $row->applicant_id)->first();
                    $row->applicant = $applicant;
                }
                $client = DB::table('users')->where('id', $row->client_id)->first();
                $row->client = $client;

            }

            return response()->json(['success' => true, 'message' => $invoices]);
        }
    }
    public function init_transaction(Request $request)
    {
        $data = $request->only('client_id', 'client_name', 'invoice_id', 'invoice_ids', 'is_multiple_invoice', 'email', 'is_rave');

        // return $request;
        // if multiple invoices
        if((bool)$request->get('is_multiple_invoice')){
            $validator = Validator::make($data, [
                'invoice_ids' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Required field cannot be blank.'
                ], 404);
            }
 
                $invoice_ids = $request->get('invoice_ids');
            
                if (is_string($invoice_ids)) {
                    $invoice_ids = json_decode($invoice_ids, true);
                }

              $invoices = DB::table('invoices')->whereIn('id', $invoice_ids)->get();

       
            if (!empty($invoices)) {
                    $user = auth()->user();
                    $invoices = DB::table('invoices')->whereIn('id',$invoice_ids)->pluck('id');
                    $res;
                    if ($request->is_rave) {
                        if (empty($request->currency)) {
                            $res = $this->initialize_rave_multiple($invoices, $user, $request->email);
                        } else {
                            $res = $this->initialize_rave_multiple2($invoices, $user, $request->email, $request->currency);
                        }
                    } else {
                        $res = $this->initialize_multiple($invoices, $user, $request->email);
                    }
                    return $res;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record not found!'
                    ]);
}

        }else{
            // If unique invoice.
            $validator = Validator::make($data, [
                'invoice_id' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Required field cannot be blank.'
                ], 404);
            }

            $invoice = DB::table('invoices')->where('id', $request->invoice_id)->first();

            if(!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found!']);
            } else {
                /*$token= $request->bearerToken();
                $user = JWTAuth::authenticate($token);*/
                $user = auth()->user();

                if($request->is_rave){
                    return $this->initialize_rave($invoice, $user, $request->email);
                }

                return $this->initialize($invoice, $user, $request->email);
            }
        }

    }
  

    public function charge_callback(Request $request)
    {
        // Retrieve the request's body
        $event = $request->input('event');
        $email = $request->input('data.customer.email');
        $gateway_response = $request->input('data.gateway_response');
        $reference = $request->input('data.reference');

        Log::info($event);

        if($event === 'charge.success'){
            $dt = date('Y-m-d H:i:s');
            $transaction = DB::table('transactions')->where('reference', $reference)->first();
            Log::info('$transaction->invoice_id: '.$transaction->invoice_id);
            if($transaction){
                // Log::info('$user: '.$user->id);
                DB::table('invoices')->where('id', $transaction->invoice_id)->update(
                    ['updated_at' => $dt]);


            DB::table('transactions')->where('id', $transaction->id)->update(
                ['gateway_response' => $transaction->gateway_response,
                'status' => $transaction->status,
                'transaction_date' => $transaction->transaction_date,
                'charge_attempted' => 1,
                'updated_at' => $dt,
            ]);
            // Log::info('$invoice: updated');
            }
        }
        return response()->json('processed', 200);
    }

    public function charge_callback_rave(Request $request)
    {
        Log::info('$hello 1234:');
        // Retrieve the request's body
        $event = $request->input('event');
        $status = $request->input('data.status');
        $createdAt = $request->input('data.created_at');
        $reference = $request->input('data.tx_ref');

        Log::info('$reference: '.$reference);
        Log::info('$createdAt: '.$createdAt);
        Log::info('$status: '.strtoupper($status));
        
       $flutterwaveHash = $request->header('verif-hash');
       $ourHash = '34t2nFxJx4v2';

       if($event === 'charge.success' && strtoupper($status) == 'SUCCESSFUL' && $flutterwaveHash == $ourHash){
        // $this->verify_payment_rave($reference);
        $this->update_transaction_as_paid($reference, 'success', $createdAt);
            // Log::info('$invoice: updated');
       }
        return response()->json('processed', 200);
    }

    protected function update_transaction_as_paid($reference, $status, $createdAt){
            DB::beginTransaction();
              $dt = date('Y-m-d H:i:s');
                  $transaction = DB::table('transactions')->where('reference', $reference)->first();
                
       
            if($transaction && $transaction->invoice_id){
            Log::info('$transaction->invoice_id: '.$transaction->invoice_id);
                // Log::info('$user: '.$user->id);
                  $invoice = Invoice::where('id', $transaction->invoice_id)->first();
                  $invoiceList = DB::table('invoices')->whereIn('id', $invoices)->get();

                if(empty($invoice)){
                    DB::$transaction->update(
                        ['gateway_response' => $status,
                            'status' => $status,
                            'transaction_date' => date("Y-m-d H:i:s", strtotime($createdAt)),
                            'charge_attempted' => 1,
                            'updated_at' => $dt,
                        ]);
                    DB::commit();
                    return;
                }else{
                    $receipt =  new Receipt();

                    $receipt->invoice_id = $transaction->invoice_id;
                    $receipt->invoice_no =  $invoice->invoice_no;
                    $receipt->currency =  $invoice->currency;
                    $receipt->amount_paid =  $transaction->amount;
                    $receipt->paid_by_user_id =  $transaction->customer_user_id;
                    $receipt->payment_date =  new \DateTime($dt);
                    $receipt->save();
                    
                     $totalPaid =  $this->updateInvoiceTotalPaid($invoice->invoice_no);
                    Log::info('$totalPaid: '.$totalPaid);
                    Log::info('$invoice->payment_amount: '.$invoice->payment_amount);
                    DB::table('invoices')->where('id', $transaction->invoice_id)->update(
                        ['updated_at' => $dt,]);
    
                    DB::table('transactions')->where('id', $transaction->id)->update(
                        ['gateway_response' => $status,
                        'status' => $status,
                        'transaction_date' => date("Y-m-d H:i:s", strtotime($createdAt)),
                        'charge_attempted' => 1,
                        'updated_at' => $dt,
                    ]);
                    DB::commit(); 
                    if($totalPaid >= $invoice->payment_amount) return;
    
        
       
 
                }   
            }elseif($transaction && $transaction->invoice_ids){
                $invoice = Invoice::whereIn('id', json_decode($transaction->invoice_ids))->get();
                   
                
                if(empty($invoice)){
                    DB::$transaction->update(
                        ['gateway_response' => $status,
                            'status' => $status,
                            'transaction_date' => date("Y-m-d H:i:s", strtotime($createdAt)),
                            'charge_attempted' => 1,
                            'updated_at' => $dt,
                        ]);
                        DB::commit();
                        return;
                    }else{
                       foreach ($invoice as $key => $value) {
                        $receipt =  new Receipt();
                            $receipt->invoice_id = $value->id;
                            $receipt->invoice_no =  $value->invoice_no;
                            $receipt->currency =  $value->currency;
                            $receipt->amount_paid =  $transaction->amount;
                            $receipt->paid_by_user_id =  $transaction->customer_user_id;
                            $receipt->payment_date =  new \DateTime($dt);
                            $receipt->save();
                       }
                       
                        $totalPaid =  $this->updateInvoiceTotalPaidmutiple($invoice);
                        Log::info('$totalPaid: '.$totalPaid);
                        // Log::info('$invoice->payment_amount: '.$invoice->payment_amount);
                        
                        $payment_amount = Invoice::whereIn('id', json_decode($transaction->invoice_ids))->sum('payment_amount');
                      
                        DB::table('invoices')->whereIn('id', json_decode($transaction->invoice_ids))->update(
                            ['updated_at' => $dt,]);
        
                        DB::table('transactions')->where('id', $transaction->id)->update(
                            ['gateway_response' => $status,
                            'status' => $status,
                            'transaction_date' => date("Y-m-d H:i:s", strtotime($createdAt)),
                            'charge_attempted' => 1,
                            'updated_at' => $dt,
                        ]);
                        
                        DB::commit();
                    if($totalPaid >= $payment_amount) return;
            }
    }
    }
    public function demo(Request $request){
         DB::beginTransaction();
              
                  $transaction = DB::table('transactions')->first();
                      $clientFullname = ''; 
                    $user = User::find($transaction->client_id);

                 $clientFullname = $user->first_name.' '.$user->last_name;
            
                    
                    $payment_name = $transaction->payment_name;
                    
            
                    
                    $transRef = $transaction->reference;
                    $transDate = $transaction->transaction_date;
                    $amountPaid = $transaction->amount;
                    $currency = $transaction->currency;
                      $staff = StaffAssignment::where('client_id', $request->client_id)
                    ->with('staff', 'staff.user')
                    ->get();
                
                $staffDetails = [];
                
                foreach ($staff as $assignment) {
                    if ($assignment->staff->user) {
                        $staffDetails[] = [
                            'first_name' => $assignment->staff->user->first_name,
                            'last_name' => $assignment->staff->user->last_name,
                            'file_path' => $assignment->staff->user->file_img,
                            // Add more staff details as needed
                        ];
                    }
                }
 $staffDetails;
        // Send the receipt email
                Mail::to($transaction->customer_email)->send(new ReceiptAfterPayMail($payment_name, $clientFullname, $transRef, $transDate, $amountPaid, $currency,$staffDetails));
                return response()->json([
        'success' => true,
        'message' => 'Demo mail has been Sent'
    ]);
        
    }
  
    protected function updateInvoiceTotalPaidmutiple($invoice){
        foreach ($invoice as $key => $value) {
            
        $invoice =  Invoice::where('invoice_no', $value->invoice_no)->first();
        $totalPaid =  Receipt::where('invoice_no', $value->invoice_no)->sum('amount_paid');
        $invoice->total_amount_paid =  $totalPaid;
        $invoice->save();
    }
    $totalPaid =  Receipt::where('invoice_no', $value->invoice_no)->sum('amount_paid');
        DB::commit();
        return $totalPaid;
    }
    protected function updateInvoiceTotalPaid($invoice_no){
        $invoice =  Invoice::where('invoice_no', $invoice_no)->first();

        $totalPaid =  Receipt::where('invoice_no', $invoice_no)->sum('amount_paid');
        $invoice->total_amount_paid =  $totalPaid;
        $invoice->save();
        DB::commit();
        return $totalPaid;
    }

    protected function initialize($invoice, $user, $email)
    {
        try {
            $dt = date('Y-m-d H:i:s');
            $amount_in_kobo = $invoice->payment_amount  * 100;
            $currency = $invoice->currency;
            $client = Client::where('id', $invoice->client_id)->first();

            if(!$email)
                $email = $user->email;
            if(!$currency)
                $currency = "NGN";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'amount' => $amount_in_kobo,
                    'email' => $email,
                    // 'phone' => 234868965986,
                    'firstname' => $user->first_name,
                    'lastname' => $user->last_name,



                ]),
                CURLOPT_HTTPHEADER => [
                    "authorization: Bearer sk_test_c9b326fc0f60f878d988bf648234fd6b2a7b05cd", //replace this with your own test key
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                // there was an error contacting the Paystack API
                return response()->json([
                    'success' => false,
                    'message' => $err]);
                // die('Curl returned error: ' . $err);
            }

            $tranx = json_decode($response, true);


            if ($tranx['status'] != true) {
                // there was an error from the API
                return response()->json([
                    'success' => false,
                    'message' => $tranx['message']]);
                // print_r('API returned error: ' . $tranx['message']);
            }

            $data = $tranx['data'];
            $data['invoice_id'] = $invoice->id;
            $data['customer_user_id'] = $user->id;
            $data['customer_email'] = $email;
            $data['amount'] = $invoice->payment_amount;
            $data['payment_name'] = $invoice->payment_name;
            $data['currency'] = $currency;
            $data['client_id'] = $client->id;
            $data['created_at'] = new \DateTime($dt);
            $data['updated_at'] = new \DateTime($dt);
            $userOwner = User::where('id', $client->user_id)->first();
            if(!empty($userOwner)){
                $data['client_name'] = $userOwner->middle_name.' '.$userOwner->first_name.' '.$userOwner->last_name;
            }

            DB::table('transactions')->insert($data);
            return response()->json([
                'success' => true,
                'message' => $data]);
        }catch (\Exception $e){
            Log::debug($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()]);
        }
    }
    protected function initialize_multiple($invoices, $user, $email)
    {
       
        try {
            
            $dt = date('Y-m-d H:i:s');
            $payment_amount = DB::table('invoices')->whereIn('id', $invoices)->sum('payment_amount');
            $invoiceList = DB::table('invoices')->whereIn('id', $invoices)->get();
            $invoice = $invoiceList->first();
            $amount_in_kobo = $payment_amount  * 100;
            $currency = $invoice->currency;
            $client = Client::where('id', $invoice->client_id)->first();

            if(!$email)
                $email = $user->email;
            if(!$currency)
                $currency = "NGN";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'amount' => $amount_in_kobo,
                    'email' => $email,
                    // 'phone' => 234868965986,
                    'firstname' => $user->first_name,
                    'lastname' => $user->last_name,



                ]),
                CURLOPT_HTTPHEADER => [
                    "authorization: Bearer sk_test_c9b326fc0f60f878d988bf648234fd6b2a7b05cd", //replace this with your own test key
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                // there was an error contacting the Paystack API
                return response()->json([
                    'success' => false,
                    'message' => $err]);
                // die('Curl returned error: ' . $err);
            }

            $tranx = json_decode($response, true);


            if ($tranx['status'] != true) {
                // there was an error from the API
                return response()->json([
                    'success' => false,
                    'message' => $tranx['message']]);
                // print_r('API returned error: ' . $tranx['message']);
            }
                
                // $payment_name = "";
                // foreach ($invoiceList as $key => $value) {
                //             $payment_name = $value->payment_name . ', ';
             
                //       }
                   $last_invoice = $invoiceList->last();
                      if (!empty($last_invoice->due_date)) {
                 $payment_name = date("jS F, Y",strtotime($last_invoice->due_date))." UnPaid Bill";
             } else {
                 $payment_name = $last_invoice->created_at->format('jS F, Y')." UnPaid Bill";
             }
               

            $data = $tranx['data'];
            $data['invoice_ids'] = $invoices;
            $data['customer_user_id'] = $user->id;
            $data['customer_email'] = $email;
            $data['amount'] = $payment_amount;
            $data['payment_name'] = $payment_name;
            $data['currency'] = $currency;
            $data['client_id'] = $client->id;
            $data['created_at'] = new \DateTime($dt);
            $data['updated_at'] = new \DateTime($dt);
            $userOwner = User::where('id', $client->user_id)->first();
            if(!empty($userOwner)){
                $data['client_name'] = $userOwner->middle_name.' '.$userOwner->first_name.' '.$userOwner->last_name;
            }

            DB::table('transactions')->insert($data);
            return response()->json([
                'success' => true,
                'message' => $data]);
        }catch (\Exception $e){
            Log::debug($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()]);
        }
    }
    public function verify_payment(Request $request){
        if($request->has('reference'))
        return $this->verify_payment_rave($request->get('reference'));
        return $this->verify_payment_rave($request->get('tx_ref'));
    }

    protected function verify_payment_rave($reference){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            // CURLOPT_URL => "https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify",
            CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'txref'=> $reference,
                // 'SECKEY' => 'FLWSECK_TEST-1bf52708f0d3dd1274a2be427db26a86-X',
                // 'SECKEY' => 'FLWSECK_TEST-614a9ac329df5b084299593cd07a208e-X',
                'SECKEY' => 'FLWSECK-0e97bf227795aa8e7bca67923f6b1f25-X',
                // 'SECKEY' => 'FLWSECK_TEST-1bf52708f0d3dd1274a2be427db26a86-X',
            ]),
            CURLOPT_HTTPHEADER => [
                // "authorization: Bearer sk_test_c9b326fc0f60f878d988bf648234fd6b2a7b05cd", //replace this with your own test key
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            // there was an error contacting the Paystack API
            return response()->json([
                'success' => false,
                'message' => $err]);
           // die('Curl returned error: ' . $err);
        }

$tranx = json_decode($response, true); 
$tranx['data']['created'] = date("Y-m-d H:i:s");
 $tranx1 = Transaction::where('reference', $reference)->first();

if ($tranx1 == null) {
    return response()->json([
        'success' => false,
        'message' => 'Transaction not found',
        'transaction' => null
    ]);
}

// return $tranx['status'];
if ($tranx1['gateway_response'] != 'successful' && $tranx['status']=='success') {
    $this->update_transaction_as_paid($reference, $tranx['data']['status'], $tranx['data']['created']);
    $tranx1 = Transaction::where('reference', $reference)->first();

    // Log the updated transaction data
    Log::info('Updated transaction data: ' . json_encode($tranx1));
}

// Return the response
return response()->json([
    'success' => $tranx1['gateway_response'] == 'successful',
    'message' => $tranx1,
]);





    }
    protected function initialize_rave($invoice, $user, $email)
    {
        $dt = date('Y-m-d H:i:s');

        $amount_in_kobo = $invoice->payment_amount  * 100;
        $txref = "rave" . uniqid();
        $currency = $invoice->currency;
        $client = Client::where('id', $invoice->client_id)->first();
        if(!$email)
            $email = $user->email;
            if(!$currency)
                $currency = "NGN";

        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS => json_encode([
        //         'amount' => $invoice->payment_amount, //$amount_in_kobo,
        //         'email' => 'ogbonnagideon5@gmail.com',
        //         'currency' => $currency,
        //         'txref'=> $txref,
        //         'custom_title' => $invoice->payment_name,
        //         'PBFPubKey' => 'FLWPUBK_TEST-c20714b252f9a3ec74d95865df564375-X',
        //         'firstname' => $user->first_name,
        //         'lastname' => $user->last_name,
        //     ]),
        //     CURLOPT_HTTPHEADER => [
        //         // "authorization: Bearer sk_test_c9b326fc0f60f878d988bf648234fd6b2a7b05cd", //replace this with your own test key
        //         "content-type: application/json",
        //         "cache-control: no-cache"
        //     ],
        // ));

        // $response = curl_exec($curl);
        // $err = curl_error($curl);

        // if ($err) {
        //     // there was an error contacting the Paystack API
        //     return response()->json([
        //         'success' => false,
        //         'message' => $err]);
        //    // die('Curl returned error: ' . $err);
        // }

        // $tranx = json_decode($response, true);


        // if ($tranx['status'] != "success") {
        //     // there was an error from the API
        //     return response()->json([
        //         'success' => false,
        //         'message' => $tranx['message']]);
        //     // print_r('API returned error: ' . $tranx['message']);
        // }
//  $last_invoice = $client->invoices->last();
// $payment_name = !empty($last_invoice->due_date) ? $last_invoice->due_date : $last_invoice->payment_name;

        $payment_name = $client->payment_name;
        if(!$payment_name){
            $payment_name = 'Invoice Payment';
        }
        // $data = $tranx['data'];
        $data = json_decode("{}", true);
        $data['reference'] = $txref;
        $data['is_flutterwave'] = true;
        // $data['link'] = true;
        $data['invoice_id'] = $invoice->id;
        $data['customer_user_id'] = $user->id;
        $data['customer_email'] = $email;
        $data['amount'] = $invoice->payment_amount;
        $data['payment_name'] = $payment_name;
        $data['currency'] = $currency;
        $data['client_id'] = $client->id;
        $data['currency'] = $currency;
        $data['created_at'] = $dt;
        $data['updated_at'] = $dt;
        $userOwner = User::where('id', $client->user_id)->first();
        if(!empty($userOwner)){
            $data['client_name'] = $userOwner->middle_name.' '.$userOwner->first_name.' '.$userOwner->last_name;
        }

        DB::table('transactions')->insert($data);
        return response()->json([
            'success' => true,
            'message' => $data]);
    }
    protected function initialize_rave_multiple($invoices, $user, $email)
    {
        
        $dt = date('Y-m-d H:i:s');
        $payment_amount = DB::table('invoices')->whereIn('id', $invoices)->sum('payment_amount');
        $invoiceList = DB::table('invoices')->whereIn('id', $invoices)->get();
        $invoice = $invoiceList->first();
        $amount_in_kobo = $payment_amount  * 100;
        $txref = "rave" . uniqid();
        $currency = $invoice->currency;
        $client = Client::where('id', $invoice->client_id)->first();
        if(!$email)
            $email = $user->email;
            if(!$currency)
                $currency = "NGN";
                
                $payment_name = "";
                    $last_invoice = $invoiceList->last();
                      if (!empty($last_invoice->due_date)) {
                 $payment_name = date("jS F, Y",strtotime($last_invoice->due_date))." UnPaid Bill";
             } else {
                 $payment_name = date("jS F, Y",strtotime($last_invoice->created_at))." UnPaid Bill";//$last_invoice->created_at->format('jS F, Y')." UnPaid Bill";
             }
    //   $user = auth()->user();

                // foreach ($invoiceList as $key => $value) {
                //             $payment_name = $value->payment_name . ', ';
                //       }
                
        if(!$payment_name){
            $payment_name = 'Invoice Payment';
        }

        // $data = $tranx['data'];
        $data = json_decode("{}", true);
        $data['reference'] = $txref;
        $data['is_flutterwave'] = true;
        // $data['link'] = true;
        $data['invoice_ids'] = $invoices;
        $data['customer_user_id'] = $client->user_id;//$user->id;
        $data['customer_email'] = $email;
        $data['amount'] = $payment_amount;
        $data['payment_name'] = $payment_name;
        $data['currency'] = $currency;
        $data['client_id'] = $client->id;
        $data['currency'] = $currency;
        $data['created_at'] = $dt;
        $data['updated_at'] = $dt;
        $userOwner = User::where('id', $client->user_id)->first();
        if(!empty($userOwner)){
            $data['client_name'] = $userOwner->middle_name.' '.$userOwner->first_name.' '.$userOwner->last_name;
        }
        DB::table('transactions')->insert($data);
        $status = "success";
        return response()->json([
            'success' => true,
            'message' => $data]);
    }
  protected function initialize_rave_multiple2($invoices, $user, $email, $currency)
    {
        
        $dt = date('Y-m-d H:i:s');
        $invoiceList = DB::table('invoices')->whereIn('id', $invoices)->get();
        $txref = "rave" . uniqid();
        if(!$email)
            $email = $user->email;
            
            
        if(!$currency)
            $currency = "NGN";
            
        $payment_name = "";
        $last_invoice = $invoiceList->last();
        $client = Client::where('id', $last_invoice->client_id)->first();
         if (!empty($last_invoice->due_date)) {
             $payment_name = date("jS F, Y",strtotime($last_invoice->due_date))." UnPaid Bill";
         } else {
             $payment_name = date("jS F, Y",strtotime($last_invoice->created_at))." UnPaid Bill";
         }
         $total_ngn = 0;
         $total_usd = 0;
          $sum_of_payment_NGN = 0;
    $sum_total_amount_paid_NGN = 0;
    $sum_of_payment_USD = 0;
    $sum_total_amount_paid_USD = 0;
// return $invoiceList;
         
         foreach ($invoiceList as $key => $invoice) {
            if ($invoice->currency == 'NGN') {
                $sum_of_payment_NGN += $invoice->payment_amount;
                $sum_total_amount_paid_NGN += $invoice->total_amount_paid;
            } elseif ($invoice->currency == 'USD') {
                $sum_of_payment_USD += $invoice->payment_amount;
                $sum_total_amount_paid_USD += $invoice->total_amount_paid;
            }
         }
    
        
        $total_amount_NGN = $sum_of_payment_NGN - $sum_total_amount_paid_NGN;
        $convert_ngnto_usd = Helper::ngnToUsd($total_amount_NGN);
        $total_amount_USD = $sum_of_payment_USD - $sum_total_amount_paid_USD;
        $convert_usdto_ngn = Helper::usdToNgn($total_amount_USD);
         $total_ngn += $total_amount_NGN + $convert_usdto_ngn;
        $total_usd += $total_amount_USD + $convert_ngnto_usd;
        $payment_amount=0;
        if($currency == 'USD'){
            $payment_amount += $total_usd;
        }else{
            $payment_amount += $total_ngn;
        }
    
 
        // $data = $tranx['data'];
        $data = json_decode("{}", true);
        $data['reference'] = $txref;
        $data['is_flutterwave'] = true;
        // $data['link'] = true;
        $data['invoice_ids'] = $invoices;
        $data['customer_user_id'] = $client->user_id;//$user->id;
        $data['customer_email'] = $email;
        $data['amount'] = $payment_amount;
        $data['payment_name'] = $payment_name;
        $data['currency'] = $currency;
        $data['client_id'] = $client->id;
        $data['currency'] = $currency;
        $data['created_at'] = $dt;
        $data['updated_at'] = $dt;
        $userOwner = User::where('id', $client->user_id)->first();
        if(!empty($userOwner)){
            $data['client_name'] = $userOwner->middle_name.' '.$userOwner->first_name.' '.$userOwner->last_name;
        }
        DB::table('transactions')->insert($data);
        $status = "success";
        return response()->json([
            'success' => true,
            'message' => $data]);
     }
public function send_invoice_email(Request $request){
        set_time_limit(0);
       
               $s_date = $request->start_date;
              $e_date =$request->end_date;
            //  $currency_code = $request->currency_code ?? 'NGN';
             $today = date('Y-m-d');
            if(empty($request->client_id)){
                if ($s_date && $e_date) {
                    $invoice_data = Client::whereHas('invoices' , function ($query) use($s_date,$e_date,$today){
                       $query->whereBetween('created_at', [$s_date, $e_date])
                       ->whereRaw('total_amount_paid < payment_amount');
                   })->with('invoices', function ($query) use ($request,$today) {
                      $query->where('client_id', $request->client_id)
                      ->where(function ($query) use ($today) {
                                $query->where('total_amount_paid', 0)
                                 ->whereRaw('total_amount_paid < payment_amount')
                                      ->orWhereNull('total_amount_paid')
                                      ->where('due_date', '>=', $today);
                            });
            
                           
                  })->with('user','friends')->get();
                } else {
                     
                   $invoice_data = Client::whereHas('invoices' , function ($query) use ($today) {
                       $query->whereRaw('total_amount_paid < payment_amount');
                   })->with('invoices', function ($query) use ($request,$today) {
                      $query->where('client_id', $request->client_id)
                            ->where(function ($query) use ($today) {
                                $query->where('total_amount_paid', 0)
                                ->whereRaw('total_amount_paid < payment_amount')
                                      ->orWhereNull('total_amount_paid')
                                      ->where('due_date', '>=', $today);
                            });
              //         if ($request->currency_code) {
              //     $query->where('currency', $request->currency_code);
              // } else {
              //     $query->where('currency', 'NGN');
              // }
                  })->with('user','friends')->get();
                }
            }else{
                if ($s_date && $e_date) {
                    $invoice_data = Client::whereHas('invoices' , function ($query) use($s_date,$e_date,$request,$today){
                       $query->whereBetween('created_at', [$s_date, $e_date])
                       ->where('client_id', $request->client_id)
                      ->where(function ($query) use ($today) {
                                $query->where('total_amount_paid', 0)
                                ->whereRaw('total_amount_paid < payment_amount')
                                ->orWhereNull('total_amount_paid')
                                ->where('due_date', '>=', $today);
                            });
                   })->with('invoices', function ($query) use ($request,$today) {
                      $query->where('client_id', $request->client_id)
                           ->where(function ($query) use ($today) {
                                $query->where('total_amount_paid', 0)
                                ->whereRaw('total_amount_paid < payment_amount')
                                      ->orWhereNull('total_amount_paid')
                                      ->where('due_date', '>=', $today);
                            });
                       
                  })->with('user','friends')->get();
                } else {
             
                        $invoice_data = Client::whereHas('invoices', function ($query) use ($request,$today) {
                      $query->where('client_id', $request->client_id)
                          ->where(function ($query) use ($today) {
                                $query->where('total_amount_paid', 0)
                                ->whereRaw('total_amount_paid < payment_amount')
                                      ->orWhereNull('total_amount_paid')
                                      ->where('due_date', '>=', $today);
                            });

                  })->with('invoices', function ($query) use ($request,$today) {
                      $query->where('client_id', $request->client_id)
                          ->where(function ($query) use ($today){
                                $query->where('total_amount_paid', 0)
                                ->whereRaw('total_amount_paid < payment_amount')
                                      ->orWhereNull('total_amount_paid')
                                      ->where('due_date', '>=', $today);
                            });
                       
                  })->with('user', 'friends')->get();
                }
            }
         
             foreach ($invoice_data as $key => $client) {
                // $friendEmail;
               
                  $client->id;
                  $invoice_ids = $client->invoices->pluck('id')->toArray();
                //   if(count($invoice_ids)> 0){
                       
                //  $data = [
                //      'client_id' => $client->id,
                //      'client_name' => $client->user->last_name. ' '. $client->user->first_name,
                //      'invoice_ids' => $invoice_ids,
                //      'is_multiple_invoice' => true,
                //      'email' => $client->user->email,
                //      'is_rave' => true,
                //  ];
                
 
                //  $new_request = $request->create('/send-invoice-email', 'POST', $data);
                //  $results = $this->init_transaction($new_request);
                //   $init_response =  $results->getData();
                 //   if ($dev->status != 'success') {
                 //     return response()->json(['message' => 'Error occurred'], 500);
                 //     break;
                 // }
                //  $invoice_reference =  $init_response->message->reference;
 
 
                 // }
                 
              
                  $inv = $client->invoices;
                   $currency = $client->invoices->first()->currency;
                  $inv_user = $client->user;
                
                  $payment_name = $client->invoices->last();
 
              
                //for Due date
                $due_datee = null;
                if (!empty($payment_name->due_date)) {
                 $due_datee = $payment_name->due_date;
             } else {
                 $due_datee = $payment_name->created_at->format('jS F, Y');
             }
              $due_datee;
                             $sum_of_payment_NGN = 0;
            $sum_total_amount_paid_NGN = 0;
            $sum_of_payment_USD = 0;
            $sum_total_amount_paid_USD = 0;
            
            foreach ($inv as $key => $invoice) {
                if ($invoice->currency == 'NGN') {
                    $sum_of_payment_NGN += $invoice->payment_amount;
                    $sum_total_amount_paid_NGN += $invoice->total_amount_paid;
                } elseif ($invoice->currency == 'USD') {
                    $sum_of_payment_USD += $invoice->payment_amount;
                    $sum_total_amount_paid_USD += $invoice->total_amount_paid;
                }
            }
            
             $total_amount_NGN = $sum_of_payment_NGN - $sum_total_amount_paid_NGN;
             $convert_ngnto_usd = Helper::ngnToUsd($total_amount_NGN);
             
             $total_amount_USD = $sum_of_payment_USD - $sum_total_amount_paid_USD;
             $convert_usdto_ngn = Helper::usdToNgn($total_amount_USD);
            
              $total_ngn = $total_amount_NGN + $convert_usdto_ngn;
              $total_usd = $total_amount_USD + $convert_ngnto_usd;
                // if ($total_amount < 0) {
                //     $total_amount = 0;
                // }
             //    $sum_of_payment = $client->invoices
              
             
             foreach ($client->friends as $friend) {
                $friendEmail = $friend->email;

                $invoicedata = [
                    'payment' => $payment_name,
                    'total_pay_ngn' => $total_amount_NGN,
                    'total_pay_usd' => $total_amount_USD,
                    'convert_ngnto_usd' => $convert_ngnto_usd,
                    'convert_usdto_ngn' => $convert_usdto_ngn,
                    'total_ngn' => $total_ngn,
                    'total_usd' => $total_usd,
                    'due_date' => $due_datee,
                    'currency' => $currency,
                    'friend_email' => $friendEmail,
                    // 'invoice_reference' => $invoice_reference,
                ];

                $this->email_invoice($client, $invoicedata);
            }
        }
    

    return response()->json([
        'success' => true,
        'message' => 'Invoice mail has been Sent'
    ]);
             
            //  return (new InvoiceEmail($subject, $body, $attachment));
}

public function email_invoice($client,$invoivedata){
     
       
    
     $client_id = $client->id;  
     $encodedClient_id = Helper::encodeId($client_id);
    $inv = $client->invoices;
    $inv_user = $client->user;
    $f_email = $invoivedata['friend_email'];
    $total_amount_ngn = $invoivedata['total_pay_ngn'];
    $total_amount_usd = $invoivedata['total_pay_usd'];
    $convert_ngnto_usd = $invoivedata['convert_ngnto_usd'];
    $convert_usdto_ngn = $invoivedata['convert_usdto_ngn'];
    $total_ngn = $invoivedata['total_ngn'];
    $total_usd = $invoivedata['total_usd'];
    $currency = $invoivedata['currency'];
    // $due_datee = $invoivedata['due_datee'];
    $recipient = $f_email;
    $subject = 'Invoice';
    $body = 'Please find your invoice attached.';
 
    $paymentLink = "https://rockgardenehr.com/pay.html?c={$encodedClient_id}&email={$recipient}";
    $button = '<a href="'.$paymentLink.'">Click here to pay</a>';

             
    $details = [
        'subject' => $subject,
        'body' => $body,
        'invoice_data' => $client->invoices,
        'user_data' =>$client->user,
        'payment' => $invoivedata['payment'],
        'total_pay_ngn'=>$invoivedata['total_pay_ngn'],
        'due_date' => $invoivedata['due_date'],
        'total_pay_usd'=>$invoivedata['total_pay_usd'],
        'convert_ngnto_usd'=>$invoivedata['convert_ngnto_usd'],
        'convert_usdto_ngn'=>$invoivedata['convert_usdto_ngn'],
        'total_ngn'=>$invoivedata['total_ngn'],
        'total_usd'=>$invoivedata['total_usd'],
        'button' => $button,
        'currency' => $invoivedata['currency'],
        'invoice_reference'=>$paymentLink,
        'category' =>$client->category,
        
    ];

    $pdfView = ($client->category == 'Homes') ? 'emails.attached_invoice_new' : 'emails.attach_invoice';
    
    if ($client->category == 'Homes') {
         $pdf = Pdf::loadHtml(view($pdfView, compact('inv', 'inv_user', 'total_ngn','total_usd', 'currency'))->render());
    } else {
         $pdf = Pdf::loadHtml(view($pdfView, compact('inv', 'inv_user', 'total_ngn','total_usd', 'currency'))->render());
    }

             
    $pdf->setOptions([
        'isPhpEnabled' => true,
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'enable_php' => true,
        'width' => '20px',
        'fontCache' => storage_path('fonts'),
        'dpi' => 150,
    ]);
              
    $pdf->render();
    $pdf = $pdf->output();
         
             
    $attachment = [
        'data' => $pdf,
        'name' => 'invoice.pdf',
    ];
 
    // Mail::to($recipient)->send(new InvoiceEmail($details,$attachment));
    //  Helper::sendEmail($recipient, $subject, $details, $attachment);
   Helper::sendEmail($recipient,  new InvoiceEmail($details, $attachment));


     return response()->json([
    'success' => true,
    'message' => 'Invoice MAil has been Sent'
]);
  


}

public function sendinvoiceEmailUsdNgn(Request $request)
{
    $client_id = $request->input('client_id');
        
    // First, send USD invoices
    $requestUsd = new Request([
        'client_id' => $client_id,
        'currency_code' => 'USD'
    ]);

    try {
        $this->send_invoice_email($requestUsd);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send USD invoice: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Then, send NGN invoices
    $requestNgn = new Request([
        'client_id' => $client_id,
        'currency_code' => 'NGN'
    ]);

    try {
        $this->send_invoice_email($requestNgn);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send NGN invoice: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return response()->json([
        'success' => true,
        'message' => 'Invoices sent successfully'
    ]);
}

public function getunpaidinvoices(Request $request)
{
   $client_id = $request->client_id;
     $invoiceData = Client::with([ 'user'])
        ->when($client_id, function ($query, $client_id) {
            return $query->where('id', $client_id);
        })
        ->with('invoices', function ($query) {
            $query->where('total_amount_paid', '<', DB::raw('payment_amount'))
                ->orWhereNull('total_amount_paid');
        })
        ->whereHas('invoices', function ($query) {
            $query->where('total_amount_paid', '<', DB::raw('payment_amount'))
                ->orWhereNull('total_amount_paid');
        })
        ->get();
    $result = [];
    foreach ($invoiceData as $client) {
        $unpaidInvoices = $client->invoices()
            ->where(function ($query) {
                    $query->whereNull('total_amount_paid')
                      ->orWhere('total_amount_paid', '<', DB::raw('payment_amount'));
            })
            ->get();
        $unpaidAmounts = $unpaidInvoices->groupBy('currency')->map(function ($invoices) {
            return $invoices->sum('payment_amount') - $invoices->sum('total_amount_paid');
        });
  
 $ngn = $unpaidAmounts->get('NGN', 0);
$usd = $unpaidAmounts->get('USD', 0);
if ($ngn > 0 && $usd > 0) {
    $ngnInUsd = Helper::ngnToUsd($ngn);
    $usdInNgn = Helper::usdToNgn($usd);
     $totalNgn = $ngn  + $usdInNgn;
    $totalUsd = $usd + $ngnInUsd;
} elseif ($ngn > 0) {
    $totalNgn = $ngn;
    $totalUsd = Helper::ngnToUsd($ngn);
} elseif ($usd > 0) {
    $totalNgn = Helper::usdToNgn($usd);
    $totalUsd = $usd;
}
$clientData = [
    'invoice' => [],
    'client' => [
        'id' => $client->id,
        'category' => $client->category,
        'user' => [
            'id' => $client->user->id,
            'last_name' => $client->user->last_name,
            'first_name' => $client->user->first_name,
            'middle_name' => $client->user->middle_name,
            'file_img' => $client->user->file_img,
            'active' => $client->user->active
        ],
        'room_location' => $client->room_location,
        'room_number' => $client->room_number,
        'room_suffix' => $client->room_suffix,
        'client_type' => $client->client_type
    ]
];
$clientData['invoice']['NGN'] = [
    'amount' => $totalNgn,
    'invoice_ids' => $unpaidInvoices->where('currency', 'NGN')->pluck('id')->toArray()
];
$clientData['invoice']['USD'] = [
    'amount' => $totalUsd,
    'invoice_ids' => $unpaidInvoices->where('currency', 'USD')->pluck('id')->toArray()
];
        $result[] = $clientData;
    }
    return response()->json([
        'success' => true,
        'message' => ['data' => $result]
    ]);
}

public function getunpaidinvoicesnew(Request $request)
{
    $client_id = $request->client_id;

    $invoiceData = Client::with(['user'])
        ->when($client_id, function ($query, $client_id) {
            return $query->where('id', $client_id);
        })
        ->with('invoices', function ($query) {
            $query->where('total_amount_paid', 0)
                ->whereRaw('total_amount_paid < payment_amount')
                ->orWhereNull('total_amount_paid');
        })
        ->whereHas('invoices', function ($query) {
            $query->where('total_amount_paid', 0)
                ->whereRaw('total_amount_paid < payment_amount')
                ->orWhereNull('total_amount_paid');
        })
        ->get();

    $result = [];
    foreach ($invoiceData as $client) {
        $unpaidInvoices = $client->invoices()
            ->where(function ($query) {
                $query->where('total_amount_paid', 0)
                    ->orWhereNull('total_amount_paid');
            })
            ->get();

        $unpaidAmounts = $unpaidInvoices->groupBy(function ($date) {
            return Carbon::parse($date->updated_at)->format('Y-m');
        })->map(function ($invoices) {
            return [
                'amount' => $invoices->sum('payment_amount'),
                'invoice_ids' => $invoices->pluck('id')->toArray(),
                'dates' => $invoices->pluck('updated_at')->map(function ($date) {
                    return $date->format('Y-m-d');
                })->toArray()
            ];
        });

        $clientData = [
            'invoice' => $unpaidAmounts->toArray(),
            'client' => [
                'id' => $client->id,
                'category' => $client->category,
                'user' => [
                    'id' => $client->user->id,
                    'last_name' => $client->user->last_name,
                    'first_name' => $client->user->first_name,
                    'middle_name' => $client->user->middle_name,
                    'file_img' => $client->user->file_img,
                    'active' => $client->user->active
                ],
                'room_location' => $client->room_location,
                'room_number' => $client->room_number,
                'room_suffix' => $client->room_suffix,
                'client_type' => $client->client_type
            ]
        ];

        $result[] = $clientData;
    }

    return response()->json([
        'success' => true,
        'message' => ['data' => $result]
    ]);
}


public function getinvoiceattachment(Request $request)
{
   $decodedClientId = Helper::decodeId($request->client_id);
   $client_id = $decodedClientId;

   $client = Client::find($client_id);

if (!$client) {
    return response()->json([
        'success' => false,
        'message' => 'Record Not Found',
    ]);
}

 $invoiceData = Client::where('id', $client_id)
    ->with(['invoices' => function ($query) {
        $query->where(function ($query) {
                $query->where('total_amount_paid', 0)
                    ->whereRaw('total_amount_paid < payment_amount')
                    ->orWhereNull('total_amount_paid');
            });
    }])
    ->with(['user', 'friends'])
    ->first();
    

$invoicePath = null;
$total_ngn = 0;
$total_usd = 0;

if(!empty($invoiceData)) {
    
    $item = $invoiceData;
    
    $inv_user = $item->user;
    $inv = $item->invoices;

    $sum_of_payment_NGN = 0;
    $sum_total_amount_paid_NGN = 0;
    $sum_of_payment_USD = 0;
    $sum_total_amount_paid_USD = 0;

    foreach ($inv as $key => $invoice) {
        if ($invoice->currency == 'NGN') {
            $sum_of_payment_NGN += $invoice->payment_amount;
            $sum_total_amount_paid_NGN += $invoice->total_amount_paid;
        } elseif ($invoice->currency == 'USD') {
            $sum_of_payment_USD += $invoice->payment_amount;
            $sum_total_amount_paid_USD += $invoice->total_amount_paid;
        }
    }

    $total_amount_NGN = $sum_of_payment_NGN - $sum_total_amount_paid_NGN;
    $convert_ngnto_usd = Helper::ngnToUsd($total_amount_NGN);
    $total_amount_USD = $sum_of_payment_USD - $sum_total_amount_paid_USD;
    $convert_usdto_ngn = Helper::usdToNgn($total_amount_USD);
    $total_ngn += $total_amount_NGN + $convert_usdto_ngn;
    $total_usd += $total_amount_USD + $convert_ngnto_usd;
    
    $encodedClientId = Helper::encodeId($client_id);
    $directory = public_path('public/uploads/client/' . $encodedClientId);
    File::makeDirectory($directory, 0755, true, true);

    $pdfPath = $directory . '/invoice.pdf';

    try {
        if ($item->category == 'Homes') {
            $pdf = PDF::loadView('invoice.attchment_invoice_new', compact('invoiceData', 'inv_user',  'inv', 'item', 'total_ngn', 'total_usd'));
        } else {
            $pdf = PDF::loadView('invoice.attchment_invoice', compact('invoiceData', 'inv_user', 'inv', 'item', 'total_ngn', 'total_usd'));
        }
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error generating PDF',
        ]);
    }
    
    $pdf->save($pdfPath);

        // Remove the absolute path and get the relative path starting from the "public" directory
        $relativePath = str_replace(public_path(), '', $pdfPath);
        $invoicePath = $relativePath;
    }

    return response()->json([
        'success' => true,
        'message' => $invoicePath,
    ]);
}


public function getunpaidinvoicesclient(Request $request)
{
   
  

   $decodedClientId = Helper::decodeId($request->client_id);
   $client_id = $decodedClientId;

   $client = Client::find($client_id);

if (!$client) {
    return response()->json([
        'success' => false,
        'message' => 'Record Not Found',
    ]);
}

$invoiceData = Client::with(['user'])
    ->when($client_id, function ($query, $client_id) {
        return $query->where('id', $client_id);
    })
    ->get();

$result = [];
foreach ($invoiceData as $client) {
    $unpaidInvoices = $client->invoices()
        ->where(function ($query) {
            $query->where('total_amount_paid', 0)
                ->orWhereNull('total_amount_paid');
        })
        ->get();

    if ($unpaidInvoices->isEmpty()) {
        $clientData = [
            'invoice' => (object)[] ,
            'client' => [
                'id' => $client->id,
                'category' => $client->category,
                'user' => [
                    'id' => $client->user->id,
                    'last_name' => $client->user->last_name,
                    'first_name' => $client->user->first_name,
                    'middle_name' => $client->user->middle_name,
                    'file_img' => $client->user->file_img,
                    'active' => $client->user->active
                ],
                'room_location' => $client->room_location,
                'room_number' => $client->room_number,
                'room_suffix' => $client->room_suffix,
                'client_type' => $client->client_type
            ]
        ];
    } else {
        $unpaidAmounts = $unpaidInvoices->groupBy('currency')->map(function ($invoices) {
            return $invoices->sum('payment_amount');
        });
        $ngn = $unpaidAmounts->get('NGN', 0);
        $usd = $unpaidAmounts->get('USD', 0);

        if ($ngn > 0 && $usd > 0) {
            $ngnInUsd = Helper::ngnToUsd($ngn);
            $usdInNgn = Helper::usdToNgn($usd);
            $totalNgn = $ngn + $usdInNgn;
            $totalUsd = $usd + $ngnInUsd;
        } elseif ($ngn > 0) {
            $totalNgn = $ngn;
            $totalUsd = Helper::ngnToUsd($ngn);
        } elseif ($usd > 0) {
            $totalNgn = Helper::usdToNgn($usd);
            $totalUsd = $usd;
        } else {
            $totalUsd = 0;
            $totalNgn = 0;
        }
         $clientData = [
            'invoice' => $unpaidAmounts->isEmpty() ? (object)[] : $unpaidAmounts->map(function ($total, $currency) use ($unpaidInvoices) {
                return [
                    'amount' => $total,
                    'invoice_ids' => $unpaidInvoices->where('currency', $currency)->pluck('id')->toArray()
                ];
            })->all(),
                    'client' => [
                    'id' => $client->id,
                    'category' => $client->category,
                    'user' => [
                    'id' => $client->user->id,
                    'last_name' => $client->user->last_name,
                    'first_name' => $client->user->first_name,
                    'middle_name' => $client->user->middle_name,
                    'file_img' => $client->user->file_img,
                    'active' => $client->user->active
                    ],
                    'room_location' => $client->room_location,
                    'room_number' => $client->room_number,
                    'room_suffix' => $client->room_suffix,
                    'client_type' => $client->client_type
                    ]
                    ];
                    
                    
                            $clientData['invoice']['NGN'] = [
                            'amount' => $totalNgn,
                            'invoice_ids' => $unpaidInvoices->where('currency', 'NGN')->pluck('id')->toArray()
                        ];
                    
                        $clientData['invoice']['USD'] = [
                            'amount' => $totalUsd,
                            'invoice_ids' => $unpaidInvoices->where('currency', 'USD')->pluck('id')->toArray()
                        ];
                    }

                    $result = $clientData;
                    }
                    
                    return response()->json([
                    'success' => true,
                    'message' => $result
                    ]);
}

public function getunpaidinvoicesclientnew(Request $request)
{
    $decodedClientId = Helper::decodeId($request->client_id);
    $client_id = $decodedClientId;

    $client = Client::find($client_id);

    if (!$client) {
        return response()->json([
            'success' => false,
            'message' => 'Record Not Found',
        ]);
    }

    $invoiceData = Client::with(['user'])
        ->when($client_id, function ($query, $client_id) {
            return $query->where('id', $client_id);
        })
        ->get();

    $result = [];
    foreach ($invoiceData as $client) {
        $unpaidInvoices = $client->invoices()
            ->where(function ($query) {
                $query->where('total_amount_paid', 0)
                    ->orWhereNull('total_amount_paid');
            })
            ->get();

        if ($unpaidInvoices->isEmpty()) {
            $clientData = [
                'invoice' => (object)[],
                'client' => [
                    'id' => $client->id,
                    'category' => $client->category,
                    'user' => [
                        'id' => $client->user->id,
                        'last_name' => $client->user->last_name,
                        'first_name' => $client->user->first_name,
                        'middle_name' => $client->user->middle_name,
                        'file_img' => $client->user->file_img,
                        'active' => $client->user->active
                    ],
                    'room_location' => $client->room_location,
                    'room_number' => $client->room_number,
                    'room_suffix' => $client->room_suffix,
                    'client_type' => $client->client_type
                ]
            ];
        } else {
            $unpaidAmounts = $unpaidInvoices->groupBy('currency')->map(function ($invoices) {
                return $invoices->sum('payment_amount');
            });
            $ngn = $unpaidAmounts->get('NGN', 0);
            $usd = $unpaidAmounts->get('USD', 0);

            if ($ngn > 0 && $usd > 0) {
                $ngnInUsd = Helper::ngnToUsd($ngn);
                $usdInNgn = Helper::usdToNgn($usd);
                $totalNgn = $ngn + $usdInNgn;
                $totalUsd = $usd + $ngnInUsd;
            } elseif ($ngn > 0) {
                $totalNgn = $ngn;
                $totalUsd = Helper::ngnToUsd($ngn);
            } elseif ($usd > 0) {
                $totalNgn = Helper::usdToNgn($usd);
                $totalUsd = $usd;
            } else {
                $totalUsd = 0;
                $totalNgn = 0;
            }
            
            $clientData = [
                'invoice' => $unpaidAmounts->isEmpty() ? (object)[] : $unpaidAmounts->map(function ($total, $currency) use ($unpaidInvoices) {
                    return [
                        'amount' => $total,
                        'invoice_ids' => $unpaidInvoices->where('currency', $currency)->pluck('id')->toArray(),
                        'dates' => $unpaidInvoices->where('currency', $currency)->pluck('updated_at')->map(function ($date) {
                            return $date->format('Y-m-d');
                        })->toArray()
                    ];
                })->all(),
                'client' => [
                    'id' => $client->id,
                    'category' => $client->category,
                    'user' => [
                        'id' => $client->user->id,
                        'last_name' => $client->user->last_name,
                        'first_name' => $client->user->first_name,
                        'middle_name' => $client->user->middle_name,
                        'file_img' => $client->user->file_img,
                        'active' => $client->user->active
                    ],
                    'room_location' => $client->room_location,
                    'room_number' => $client->room_number,
                    'room_suffix' => $client->room_suffix,
                    'client_type' => $client->client_type
                ]
            ];
            
            $clientData['invoice']['NGN'] = [
                'amount' => $totalNgn,
                'invoice_ids' => $unpaidInvoices->where('currency', 'NGN')->pluck('id')->toArray(),
                'dates' => $unpaidInvoices->where('currency', 'NGN')->pluck('updated_at')->map(function ($date) {
                    return $date->format('Y-m-d');
                })->toArray()
            ];

            $clientData['invoice']['USD'] = [
                'amount' => $totalUsd,
                'invoice_ids' => $unpaidInvoices->where('currency', 'USD')->pluck('id')->toArray(),
                'dates' => $unpaidInvoices->where('currency', 'USD')->pluck('updated_at')->map(function ($date) {
                    return $date->format('Y-m-d');
                })->toArray()
            ];
        }

        $result = $clientData;
    }

    return response()->json([
        'success' => true,
        'message' => $result
    ]);
}











     

    
}
