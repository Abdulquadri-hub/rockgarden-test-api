<?php

namespace App\Http\Services;

use App\Http\Controllers\Api\TwilioSMSController;

use App\Models\User;
use App\Helpers\Helper;
use App\Http\Resources\SaleOrderCollection;
use App\Http\Resources\SaleOrderResource;
use App\Http\Services\UserService;
use App\Models\NotificationSettings;
use App\Mail\LowStockMail;
use App\Mail\ProcedureAdminMail;
use App\Mail\ProcedureFriendMail;
use App\Mail\MedicationAdminMail;
use App\Mail\MedicationFriendsMail;
use App\Models\Client;
use App\Models\SystemContacts;
use App\Models\Invoice;
use App\Models\Employee; #add_n

use App\Models\Item;
use App\Models\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SaleOrderService implements SaleOrderServiceInterface
{
    public function createSaleOrder(Request $request){
        try {

            $validator = Validator::make($request->all(),[
                'client_id' => 'required|integer',
                'item_name' => 'required|string',
                'order_date' => 'required',
                'invoiced' => 'required|boolean',
            ]);
            $total_order = 0;
            if($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'Invalid Sale Order data.'
                ];
            }

            $item_name =   $request->get('item_name');
            if(empty($item_name)){
                return [
                    'success' => false,
                    'message' => 'No item found.'
                ];
            }

            $item = Item::where('name', $item_name)->first();
            if(empty($item)){
                return [
                    'success' => false,
                    'message' => 'No item found.'
                ];
            }
            
            $staff =  Employee::where('id', $request->get('staff_id'))->first(); #add_n
            $department = "";
            if (!empty($staff)) {
                
                $department = $staff->department; 
            }

            if($item->type !== "Service"){
                
                if($department !== "RHA Team 1" && $department !== "RHA Team 2")
                {
                    if ($item->current_stock_level <= 0) {
                        return [
                           'success' => false,
                           'message' => 'Item out of stock.'
                        ];
                    }

                    $total_order = $request->get('total_order');

                    if ($item->current_stock_level < $total_order) {
                        return [
                            'success' => false,
                            'message' => 'Item is low in stock.'
                        ];
                    }
                
                    $item->current_stock_level = $item->current_stock_level - $total_order;
                    
                }else {

                    $total_order = $request->get('total_order');
                }
                
            }else {
                
                $total_order = $request->get('total_order');
                if(is_null($total_order) && $total_order == 0)
                {
                    $total_order = 1;
                }
   
            }

            $userService = new UserService();

            $invoiced =  $request->get('invoiced');
            $client = Client::where('id', $request->get('client_id'))->first();
            if(empty($client)){
                return [
                    'success' => false,
                    'message' => 'Client not found.'
                ];
            }

            // Create new Sale Order
            $saleOrder = new SaleOrder();

            if($invoiced)
            {
                $invoice =  new Invoice();
               
                if($item->type !== "Service")
                {
                    
                    if($department !== "RHA Team 1" && $department !== "RHA Team 2")
                    {
                        $invoice->payment_name =  'Payment for '. $item->name;
                        // $invoice->total_amount_paid =  $total_order * $item->sale_price;
                        $invoice->currency =  $item->currency;
                        $lastInv = Invoice::orderBy('id','DESC')->first();
                        $invoice->invoice_no = 'INV'.$userService->generateCode($lastInv->id ?? 0 + 1);
                        $invoice->is_monthly_recurrent = false;
                        $invoice->client_id =  $request->get('client_id');
                        $invoice->payment_amount = $total_order * $item->sale_price;// $request->get('payment_amount');
                        $invoice->save();
                        $saleOrder->invoice_no = $invoice->invoice_no;
                    }
                    
                }else {
                    
                    if($department !== "RHA Team 1" && $department !== "RHA Team 2")
                    {
                        $invoice->payment_name =  'Payment for '. $item->name;
                        // $invoice->total_amount_paid =  $total_order * $item->sale_price;
                        $invoice->currency =  $item->currency;
                        $lastInv = Invoice::orderBy('id','DESC')->first();
                        $invoice->invoice_no = 'INV'.$userService->generateCode($lastInv->id ?? 0 + 1);
                        $invoice->is_monthly_recurrent = false;
                        $invoice->client_id =  $request->get('client_id');
                        $invoice->payment_amount = $total_order * $item->sale_price;// $request->get('payment_amount');
                        $invoice->save();
                        $saleOrder->invoice_no = $invoice->invoice_no;
                    }
                }

            }

            $latestOrder = SaleOrder::orderBy('created_at','DESC')->first();
            $saleOrder->order_no = 'SLO'.$userService->generateCode($latestOrder === null ? 1 : ($latestOrder->id + 1));
            $saleOrder->client_id = $request->get('client_id');
            $saleOrder->order_date = new \DateTime($request->get('order_date'));
            $saleOrder->invoiced = $request->get('invoiced');
            $saleOrder->created_by_user_id = Auth::user()->id;
            $saleOrder->total_amount = $total_order * $item->sale_price;
            $saleOrder->item_id = $item->id;
            $saleOrder->item_currency = $item->currency;
            $saleOrder->price_per_unit = $item->sale_price;
            $saleOrder->item_name = $item->name;
            $saleOrder->item_unit = $item->unit;
            $saleOrder->order_details = $request->get('order_details');
            
            $emailNotifications = NotificationSettings::where('trigger_name', 'LOW_STOCK')
    ->where('send_email', 1)
    ->get();

$smsNotifications = NotificationSettings::where('trigger_name', 'LOW_STOCK')
    ->where('send_sms', 1)
    ->get();
if ($item->type != 'Service' && ($item->current_stock_level <= $item->reorder_level || $item->current_stock_level == 0)) {
    $systemContacts = SystemContacts::all();

    foreach ($systemContacts as $systemContact) {
        $systemContactEmails = explode(',', $systemContact->email);
        $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);

        $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
            ->where('trigger_name', 'LOW_STOCK')
            ->get();

        foreach ($notificationSettings as $notificationSetting) {
            $url = 'https://admin.rockgardenehr.com';

            if ($notificationSetting->send_email) {
                foreach ($systemContactEmails as $email) {
                    $mail = new LowStockMail($item->name, $item->current_stock_level, $url);
                    // Helper::sendEmail($email, $mail);
                }
            }

            if ($notificationSetting->send_sms) {
                foreach ($systemContactPhoneNumbers as $phoneNumber) {
                    $message = TwilioSMSController::lowStockMessage($item->name, $item->current_stock_level, $url);
                    // Helper::sendSms($phoneNumber, $message);
                }
// \Log::info('Right now im here');
            }
        }
    }
}

if($item->type != 'Service'){
       //For Admin Notification For Medication,
            $emailNotifications = NotificationSettings::where('trigger_name', 'MEDICATION_ADMIN')
            ->where('send_email', 1)
            ->get();

        $smsNotifications = NotificationSettings::where('trigger_name', 'MEDICATION_ADMIN')
            ->where('send_sms', 1)
            ->get();
       
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'MEDICATION_ADMIN')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                        $name = $client->user->first_name . " " . $client->user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
                        $medicationDate = $saleOrder->order_date->format('Y-m-d');
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new MedicationAdminMail($name, $url,$medicationDate);
                                // Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::medicationAdminMessage($name, $url,$medicationDate);
                                // Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
                
    
    //Friend Notifications,
  
       $name = $client->user->first_name . " " . $client->user->last_name;
                $emailNotifications = NotificationSettings::where('trigger_name', 'MEDICATION_FRIEND')
                    ->where('send_email', 1)
                    ->get();

                $smsNotifications = NotificationSettings::where('trigger_name', 'MEDICATION_FRIEND')
                    ->where('send_sms', 1)
                    ->get();
                    $medicationDate = $saleOrder->order_date->format('Y-m-d');
               
                if ($emailNotifications) {
                    foreach ( $client->friends as $friend) {
                        $email = $friend->email;
                      $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new MedicationFriendsMail($name, $familyfriend_name,$medicationDate);
                        // Helper::sendEmail($email, $mail);
                    }
                    
                       
                    
                    
                
                }
                
                if ($smsNotifications) {
                    foreach ( $client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::medicationFriendMessage($name, $familyfriend_name,$medicationDate);
                        // Helper::sendSms($phoneNumber, $message);
                    }
                
                        
                   
                }
    
}else{
    //For Admin Notification for Procedure,
            $emailNotifications = NotificationSettings::where('trigger_name', 'PROCEDURE_ADMIN')
            ->where('send_email', 1)
            ->get();

        $smsNotifications = NotificationSettings::where('trigger_name', 'PROCEDURE_ADMIN')
            ->where('send_sms', 1)
            ->get();
       
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'PROCEDURE_ADMIN')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                        $name = $client->user->first_name . " " . $client->user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
            
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new ProcedureAdminMail($systemContact->name,$item->name,$name, $url);
                                // Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::procedureAdminMessage($systemContact->name,$item->name,$name, $url);
                                // Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
                
    
    //Friend Notifications,
  
       $name = $client->user->first_name . " " . $client->user->last_name;
                $emailNotifications = NotificationSettings::where('trigger_name', 'PROCEDURE_FRIEND')
                    ->where('send_email', 1)
                    ->get();

                $smsNotifications = NotificationSettings::where('trigger_name', 'PROCEDURE_FRIEND')
                    ->where('send_sms', 1)
                    ->get();
               
                if ($emailNotifications) {
                    foreach ( $client->friends as $friend) {
                        $email = $friend->email;
                      $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new ProcedureFriendMail($familyfriend_name,$item->name,$name);
                        // Helper::sendEmail($email, $mail);
                    }
                    
                       
                    
                    
                
                }
                
                if ($smsNotifications) {
                    foreach ( $client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::procedureFriendMessage( $familyfriend_name,$item->name,$name);
                        // Helper::sendSms($phoneNumber, $message);
                    }
                
                        
                   
                }
    }





            $item->save();
            $saleOrder->save();
            return [
                'success' => true,
                'message' => $saleOrder->id
            ];
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ] ;
        }
    }
}
