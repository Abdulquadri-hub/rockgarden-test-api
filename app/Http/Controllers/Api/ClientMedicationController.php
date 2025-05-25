<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientMedicationCollection;
use App\Http\Resources\ClientMedicationResource;
use App\Http\Services\SaleOrderService;
use App\Models\Client;
use App\Models\ClientMedication;
use App\Models\FamilyFriendAssignment;
use App\Models\Item;
use App\Models\StaffAssignment;
use App\Mail\PrescriptionAdminCreatedMail;
use App\Mail\PrescriptionFriendCreatedMail;
use App\Mail\PrescriptionFriendUpdatedMail;
use App\Mail\PrescriptionAdminUpdatedMail;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\SystemContacts;
use App\Models\NotificationSettings;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ClientMedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $client_id = $request->get('client_id');
        $staff_id =  $request->get('staff_id');
        $user_id = $request->get('user_id');

        if(!empty($user_id)){
            $clientIds = FamilyFriendAssignment::where('familyfriend_id', $user_id)->pluck('client_id');
        }

        $limit = $request->get('limit');

        $res = [];
        if(!empty($staff_id)){
            $clientIds = StaffAssignment::where('staff_id', $staff_id)->pluck('client_id');
        }
        if(!empty($limit)){
            if(!empty($client_id)){
                $res = ClientMedication::where('client_id', $client_id)->orderBy('created_at', 'DESC')->paginate($limit);
            }elseif (!empty($clientIds)){
                $res = ClientMedication::whereIn('client_id', $clientIds)->orderBy('created_at', 'DESC')->paginate($limit);
            }
        }else{
            if(!empty($client_id)){
                $res = ClientMedication::where('client_id', $client_id)->orderBy('created_at', 'DESC')->get();
            }elseif (!empty($clientIds)){
                $res = ClientMedication::whereIn('client_id', $clientIds)->orderBy('created_at', 'DESC')->get();
            }
        }

        return \response()->json([
                'success' => true,
                'message' => new ClientMedicationCollection($res)
            ]
            , Response::HTTP_OK);
    //     $client_id = $request->get('client_id');
    // $staff_id = $request->get('staff_id');
    // $user_id = $request->get('user_id');
    // $from_date = $request->get('from_date');
    // $to_date = $request->get('to_date');

    // if (!empty($user_id)) {
    //     $clientIds = FamilyFriendAssignment::where('familyfriend_id', $user_id)->pluck('client_id');
    // }

    // $limit = $request->get('limit');

    // $res = [];
    // if (!empty($staff_id)) {
    //     $clientIds = StaffAssignment::where('staff_id', $staff_id)->pluck('client_id');
    // }

    // if (!empty($limit)) {
    //     $query = ClientMedication::orderBy('created_at', 'DESC');

    //     if (!empty($client_id)) {
    //         $query->where('client_id', $client_id);
    //     } elseif (!empty($clientIds)) {
    //         $query->whereIn('client_id', $clientIds);
    //     }

    //     if (!empty($from_date) && !empty($to_date)) {
    //         $query->whereBetween('created_at', [$from_date, $to_date]);
    //     }

    //     $res = $query->paginate($limit);
    // } else {
    //     $query = ClientMedication::orderBy('created_at', 'DESC');

    //     if (!empty($client_id)) {
    //         $query->where('client_id', $client_id);
    //     } elseif (!empty($clientIds)) {
    //         $query->whereIn('client_id', $clientIds);
    //     }

    //     if (!empty($from_date) && !empty($to_date)) {
    //         $query->whereBetween('created_at', [$from_date, $to_date]);
    //     }

    //     $res = $query->get();
    // }

    // return response()->json([
    //     'success' => true,
    //     'message' => new ClientMedicationCollection($res)
    // ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexPaged(Request $request)
    {
        $client_id = $request->get('client_id');
        $limit = $request->get('limit');

        $res = [];
        if(!empty($limit)){
            if(!empty($client_id)){
                $res = ClientMedication::where('client_id', $client_id)->orderBy('created_at', 'DESC')->paginate($limit);
            }else{
                $res = ClientMedication::orderBy('created_at', 'DESC')->paginate($limit);
            }
        }else{
            if(!empty($client_id)){
                $res = ClientMedication::where('client_id', $client_id)->orderBy('created_at', 'DESC')->get();
            }else{
                $res = ClientMedication::orderBy('created_at', 'DESC')->get();
            }
        }

        return \response()->json([
                'success' => true,
                'message' => new ClientMedicationCollection($res)
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
            $validator = Validator::make($request->all(),[
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Client Medication data.'
                ], 400);
            }

            // Check Client Existence
            $client =  Client::where('id', $request->get('client_id'))->first();
            if(empty($client)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not fount.'
                ], 400);
            }

            // Create new ClientMedication name
            $clientMedication = new ClientMedication();
            $clientMedication->start_date = new \DateTime($request->get('start_date'));
            $clientMedication->strength = $request->get('strength');
            $clientMedication->is_prn = $request->get('is_prn');
            $clientMedication->dosage_morning = $request->get('dosage_morning');
            $clientMedication->dosage_morning_when = $request->get('dosage_morning_when');
            $clientMedication->dosage_afternoon = $request->get('dosage_afternoon');
            $clientMedication->dosage_afternoon_when = $request->get('dosage_afternoon_when');
            $clientMedication->dosage_evening = $request->get('dosage_evening');
            $clientMedication->dosage_evening_when = $request->get('dosage_evening_when');
            $clientMedication->reason_for_medication = $request->get('reason_for_medication');
            $clientMedication->other_intake_guide = $request->get('other_intake_guide');
            $clientMedication->medication_type = $request->get('medication_type');
            $clientMedication->medicine_name = $request->get('medicine_name');
            $clientMedication->client_id = $request->get('client_id');
            $clientMedication->unit = $request->get('unit');
            $clientMedication->end_date = empty($request->get('end_date')) ? null :new \DateTime($request->get('end_date'));
            if(!empty($client)){
                //For Admin Notification For Medication,
            $emailNotifications = NotificationSettings::where('trigger_name', 'PRESCRIPTION_ADMIN_CREATED')
            ->where('send_email', 1)
            ->get();

           $smsNotifications = NotificationSettings::where('trigger_name', 'PRESCRIPTION_ADMIN_CREATED')
            ->where('send_sms', 1)
            ->get();
       
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'PRESCRIPTION_ADMIN_CREATED')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                        $name = $client->user->first_name . " " . $client->user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
                        // $medicationMissedDate = $saleOrder->order_date->format('Y-m-d');
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new PrescriptionAdminCreatedMail($name, $url,$clientMedication->medicine_name);
                                Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::prescriptionAdminCreatedMessage($name, $url,$clientMedication->medicine_name);
                                Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
                
    
    //Friend Notifications,
  
       $name = $client->user->first_name . " " . $client->user->last_name;
                $emailNotifications = NotificationSettings::where('trigger_name', 'PRESCRIPTION_FRIEND_CREATED')
                    ->where('send_email', 1)
                    ->get();

                $smsNotifications = NotificationSettings::where('trigger_name', 'PRESCRIPTION_FRIEND_CREATED')
                    ->where('send_sms', 1)
                    ->get();
                    
               
                if ($emailNotifications) {
                    foreach ( $client->friends as $friend) {
                        $email = $friend->email;
                      $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new PrescriptionFriendCreatedMail($name, $familyfriend_name,$clientMedication->medicine_name);
                        Helper::sendEmail($email, $mail);
                    }
                    
                       
                    
                    
                
                }
                
                if ($smsNotifications) {
                    foreach ( $client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::medicationMissedFriendMessage($name, $familyfriend_name,$clientMedication->medicine_name);
                        Helper::sendSms($phoneNumber, $message);
                    }
                
                        
                   
                }
            }

            $clientMedication->save();
            return response()->json([
                'success' => true,
                'message' => $clientMedication->id
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
        $clientMedication = ClientMedication::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $clientMedication
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
            $validator = Validator::make($request->all( ),[
                'id' => 'required|integer',
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Client Medication data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new ClientMedication name
            $clientMedication = ClientMedication::where('id', $request->get('id'))->first();
            if(empty($clientMedication)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client Medication not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $clientMedication->start_date = new \DateTime($request->get('start_date'));
            $clientMedication->strength = $request->get('strength');
            $clientMedication->is_prn = $request->get('is_prn');
            $clientMedication->dosage_morning = $request->get('dosage_morning');
            $clientMedication->dosage_morning_when = $request->get('dosage_morning_when');
            $clientMedication->dosage_afternoon = $request->get('dosage_afternoon');
            $clientMedication->dosage_afternoon_when = $request->get('dosage_afternoon_when');
            $clientMedication->dosage_evening = $request->get('dosage_evening');
            $clientMedication->dosage_evening_when = $request->get('dosage_evening_when');
            $clientMedication->reason_for_medication = $request->get('reason_for_medication');
            $clientMedication->other_intake_guide = $request->get('other_intake_guide');
            $clientMedication->medication_type = $request->get('medication_type');
            $clientMedication->medicine_name = $request->get('medicine_name');
            $clientMedication->client_id = $request->get('client_id');
            $clientMedication->unit = $request->get('unit');
            $clientMedication->end_date = empty($request->get('end_date')) ? null :new \DateTime($request->get('end_date'));
            $client =  Client::where('id', $request->get('client_id'))->first();
             if(!empty($client)){
                //For Admin Notification For Medication,
            $emailNotifications = NotificationSettings::where('trigger_name', 'PRESCRIPTION_ADMIN_UPDATED')
            ->where('send_email', 1)
            ->get();

           $smsNotifications = NotificationSettings::where('trigger_name', 'PRESCRIPTION_ADMIN_UPDATED')
            ->where('send_sms', 1)
            ->get();
       
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'PRESCRIPTION_ADMIN_UPDATED')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                        $name = $client->user->first_name . " " . $client->user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
                        // $medicationMissedDate = $saleOrder->order_date->format('Y-m-d');
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new PrescriptionAdminUpdatedMail($name, $url,$clientMedication->medicine_name);
                                Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::prescriptionAdminUpdatedMessage($name, $url,$clientMedication->medicine_name);
                                Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
                
    
    //Friend Notifications,
  
       $name = $client->user->first_name . " " . $client->user->last_name;
                $emailNotifications = NotificationSettings::where('trigger_name', 'PRESCRIPTION_FRIEND_UPDATED')
                    ->where('send_email', 1)
                    ->get();

                $smsNotifications = NotificationSettings::where('trigger_name', 'PRESCRIPTION_FRIEND_UPDATED')
                    ->where('send_sms', 1)
                    ->get();
                    
               
                if ($emailNotifications) {
                    foreach ( $client->friends as $friend) {
                        $email = $friend->email;
                      $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new PrescriptionFriendUpdatedMail($name, $familyfriend_name,$clientMedication->medicine_name);
                        Helper::sendEmail($email, $mail);
                    }
                    
                       
                    
                    
                
                }
                
                if ($smsNotifications) {
                    foreach ( $client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::medicationMissedFriendMessage($name, $familyfriend_name,$clientMedication->medicine_name);
                        Helper::sendSms($phoneNumber, $message);
                    }
                
                        
                   
                }
            }
            
            $clientMedication->save();
            return response()->json([
                'success' => true,
                'message' => $clientMedication
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

        ClientMedication::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Client Medication successfully deleted"
        ], Response::HTTP_OK);
    }
}
