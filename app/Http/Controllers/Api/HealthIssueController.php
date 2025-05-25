<?php

namespace App\Http\Controllers\Api;

use App\Dto\EventType;
use App\Dto\HealthIssueDto;
use App\Events\HealthIssueAdminEvent;
use App\Events\HealthIssueFriendEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHealthIssueRequest;
use App\Http\Requests\UpdateHealthIssueRequest;
use App\Http\Resources\HealthIssueCollection;
use App\Http\Resources\HealthIssueResource;
use App\Models\Client;
use App\Models\SystemContacts;
use App\Models\Employee;
use App\Mail\HealthIssueAdminMail;
use App\Mail\HealthIssueAdminUpdatedMail;
use App\Mail\HealthIssueFriendMail;
use App\Mail\HealthIssueFriendUpdatedMail;
use App\Helpers\Helper;
use App\Models\NotificationSettings;
use App\Models\FamilyFriendAssignment;
use App\Models\HealthIssue;
use App\Models\StaffAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class HealthIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request  $request)
    {
        $client_id = $request->get('client_id');
        $recorded_by_staff_id = $request->get('recorded_by_staff_id');
        $closed_by_user_id = $request->get('closed_by_user_id');
        $limit = $request->get('limit');
        $staff_id = $request->get('staff_id');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $user_id = $request->get('user_id');
        if(!empty($user_id)){
            $clientIds = FamilyFriendAssignment::where('familyfriend_id', $user_id)->pluck('client_id');
        }

        if(!empty($staff_id)){
            $clientIds = StaffAssignment::where('staff_id', $staff_id)->pluck('client_id');
        }

        if (!empty($limit)){
            if (!empty($closed_by_user_id) && !empty($client_id) && !empty($recorded_by_staff_id)){
                $res = HealthIssue::where('client_id', $client_id)
                    ->where('closed_by_user_id', $closed_by_user_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->where('recorded_by_staff_id', $recorded_by_staff_id)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);
            }
            elseif (!empty($closed_by_user_id) && !empty($client_id)){
                $res = HealthIssue::where('client_id', $client_id)
                    ->where('closed_by_user_id', $closed_by_user_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);
            }
            elseif (!empty($client_id) && !empty($recorded_by_staff_id)){
                $res = HealthIssue::where('client_id', $client_id)
                    ->where('recorded_by_staff_id', $recorded_by_staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);
            }
            elseif (!empty($closed_by_user_id) && !empty($recorded_by_staff_id)){
                $res = HealthIssue::where('recorded_by_staff_id', $recorded_by_staff_id)
                    ->where('recorded_by_staff_id', $recorded_by_staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);
            }
            elseif(!empty($closed_by_user_id)){
                $res = HealthIssue::where('closed_by_user_id', $closed_by_user_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);
            }
            elseif(!empty($recorded_by_staff_id)){
                $res = HealthIssue::where('recorded_by_staff_id', $recorded_by_staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);
            }
            elseif (!empty($client_id)){
                $res = HealthIssue::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($clientIds)){
                $res = HealthIssue::whereIn('client_id', $clientIds)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);
            }else{
                return \response()->json([
                    'success' => true,
                    'message' => new HealthIssueCollection(HealthIssue::orderBy('created_at', 'DESC')->paginate($limit))
                ], Response::HTTP_OK);
            }
        }
        else
        {
            if (!empty($closed_by_user_id) && !empty($client_id) && !empty($recorded_by_staff_id)){
                $res = HealthIssue::where('client_id', $client_id)
                    ->where('closed_by_user_id', $closed_by_user_id)
                    ->where('recorded_by_staff_id', $recorded_by_staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }
            elseif (!empty($closed_by_user_id) && !empty($client_id)){
                $res = HealthIssue::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->where('closed_by_user_id', $closed_by_user_id)
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }
            elseif (!empty($client_id) && !empty($recorded_by_staff_id)){
                $res = HealthIssue::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->where('recorded_by_staff_id', $recorded_by_staff_id)
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }
            elseif (!empty($closed_by_user_id) && !empty($recorded_by_staff_id)){
                $res = HealthIssue::where('recorded_by_staff_id', $recorded_by_staff_id)
                    ->where('recorded_by_staff_id', $recorded_by_staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }
            elseif(!empty($closed_by_user_id)){
                $res = HealthIssue::where('closed_by_user_id', $closed_by_user_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }
            elseif(!empty($recorded_by_staff_id)){
                $res = HealthIssue::where('recorded_by_staff_id', $recorded_by_staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }
            elseif (!empty($client_id)){
                $res = HealthIssue::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }elseif (!empty($clientIds)){
                $res = HealthIssue::whereIn('client_id', $clientIds)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }else{
                return \response()->json([
                    'success' => true,
                    'message' => new HealthIssueCollection(HealthIssue::orderBy('created_at', 'DESC')->get())
                ], Response::HTTP_OK);
            }
        }
             if (!empty($from_date) && !empty($to_date)) {
        $res = HealthIssue::whereBetween('created_at', [$from_date, $to_date])->get();
    }

        return \response()->json([
            'success' => true,
            'message' => new HealthIssueCollection($res)
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
            'message' => new HealthIssueCollection(HealthIssue::paginate((int) $request->get('limit')))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $client_id =  $request->get('client_id');
            $client = null;
            if (!empty($client_id)){
                $client = Client::where('id', $client_id)->first();
                if(empty($client)){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Client not found for health issue."
                        ], Response::HTTP_BAD_REQUEST);
                }
            }else{
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Client not found for health issue."
                    ], Response::HTTP_BAD_REQUEST);
            }

            // Create new HealthIssue
            $healthIssue = new HealthIssue();

            $healthIssue->client_id = $client->id;
            $healthIssue->description = $request->get('description');
            $healthIssue->title = $request->get('title');
            $healthIssue->review_frequency = $request->get('review_frequency');
            $healthIssue->initial_treatment_plan = $request->get('initial_treatment_plan');
            $healthIssue->closed_reason = $request->get('closed_reason');
            $healthIssue->image1 = $request->get('image1');
            $healthIssue->image2 = $request->get('image2');
            $healthIssue->image3 = $request->get('image3');
            $healthIssue->start_date = new \DateTime($request->get('start_date'));
            $healthIssue->closed_date = new \DateTime($request->get('closed_date'));
            $closed_by_user_id = $request->get('closed_by_user_id');

            if(!empty($closed_by_user_id)){
                // $closed_by_user = User::where('id', $closed_by_user_id)->first();
                // if(empty($closed_by_user)){
                //     return response()->json(
                //         [
                //             'success' => false,
                //             'message' => "User closing the health issue not."
                //         ], Response::HTTP_BAD_REQUEST);
                // }
                $healthIssue->closed_by_user_id = $request->get('closed_by_user_id');
            }

            $recorded_by_staff = Employee::where('user_id', Auth::user()->id)->first();

            if(empty($recorded_by_staff)){
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Staff not found."
                    ], Response::HTTP_BAD_REQUEST);
            }

            $healthIssue->recorded_by_staff_id = $recorded_by_staff->id;

            $healthIssue->save();
            //Raise Events
            if(!empty($client)){
                // $issue = new HealthIssueDto($client->user->first_name.' '.$client->user->last_name, $healthIssue->title, null,EventType::HEALTH_ISSUE_FRIEND);
                // return $issuAdmin = new HealthIssueDto($client->user->first_name.' '.$client->user->last_name, $healthIssue->title, null,EventType::HEALTH_ISSUE_ADMIN);
                // event(new HealthIssueFriendEvent($issue, $client->user->email, $client->user->id, $client->user->phone_num, $client->id));
                // event(new HealthIssueAdminEvent($issuAdmin, $client->user->email, $client->user->id, $client->user->phone_num));
            }
            //  $user = User::where('id', $request->get('client_id'))->first();
           
                if(!empty($client)){
                       $emailNotifications = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_ADMIN')
                    ->where('send_email', 1)
                    ->get();

                $smsNotifications = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_ADMIN')
                    ->where('send_sms', 1)
                    ->get();
       
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'HEALTH_ISSUE_ADMIN')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                        $name = $client->user->first_name . " " . $client->user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
                        
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new HealthIssueAdminMail($name, $url);
                                Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::healthIssueAdminMessage($name, $url);
                                Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
                
                    
                    
                    //For send notification to friends,
                    // return  $client->friends;
                    $emailNotification = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_FRIEND')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_FRIEND')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    foreach ( $client->friends as $friend) {
                        $email = $friend->email;
                      $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new HealthIssueFriendMail($name, $familyfriend_name);
                        Helper::sendEmail($email, $mail);
                    }
                    
                        
                    
                    
                
                }
                
                if ($smsNotification) {
                    foreach ( $client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::healthIssueAdminMessage($name, $familyfriend_name);
                        Helper::sendSms($phoneNumber, $message);
                    }
                
                        
                   
                }
                }      
                
            return response()->json([
                'success' => true,
                'message' => $healthIssue->id
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
        $healthIssue = HealthIssue::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new HealthIssueResource($healthIssue)
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
            $client_id =  $request->get('client_id');
            $client = null;
            if (!empty($client_id)){
                $client = Client::where('id', $client_id)->first();
                if(empty($client)){

                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Client not found for health issue."
                        ], Response::HTTP_BAD_REQUEST);
                }
            }

            // Create new HealthIssue
            $healthIssue = HealthIssue::where('id', $request->get('id'))->first();
           
            if(empty($healthIssue)){
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Health issue not found."
                    ], Response::HTTP_BAD_REQUEST);
            }

            $healthIssue->client_id = empty($client) ? null : $client->id ;
            $healthIssue->description = $request->get('description');
            $healthIssue->title = $request->get('title');
            $healthIssue->review_frequency = $request->get('review_frequency');
            $healthIssue->initial_treatment_plan = $request->get('initial_treatment_plan');
            $healthIssue->closed_reason = $request->get('closed_reason');
            $healthIssue->image1 = $request->get('image1');
            $healthIssue->image2 = $request->get('image2');
            $healthIssue->image3 = $request->get('image3');
            $healthIssue->start_date = new \DateTime($request->get('start_date'));
            $healthIssue->closed_date = new \DateTime($request->get('closed_date'));
            $closed_by_user_id = $request->get('closed_by_user_id');
            if(!empty($closed_by_user_id)){
                // $closed_by_user = User::where('id', $closed_by_user_id)->first();
                // if(empty($closed_by_user)){
                //     return response()->json(
                //         [
                //             'success' => false,
                //             'message' => "User closing the health issue not."
                //         ], Response::HTTP_BAD_REQUEST);
                // }
                $healthIssue->closed_by_user_id = $request->get('closed_by_user_id');
            }

            $recorded_by_staff_id = $request->get('recorded_by_staff_id');
            if(!empty($recorded_by_staff_id)){
                $recorded_by_staff = Employee::where('id', $recorded_by_staff_id)->first();
                if(empty($recorded_by_staff)){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Staff not found."
                        ], Response::HTTP_BAD_REQUEST);
                }
                $healthIssue->recorded_by_staff_id = $recorded_by_staff_id;
            }

            $healthIssue->save();
            // Raise Events
            // if(!empty($client)){
            //     $issue = new HealthIssueDto($client->user->first_name.' '.$client->user->last_name, $healthIssue->title, null,EventType::HEALTH_ISSUE_FRIEND_UPDATED);
            //     $issuAdmin = new HealthIssueDto($client->user->first_name.' '.$client->user->last_name, $healthIssue->title, null,EventType::HEALTH_ISSUE_ADMIN_UPDATED);
            //      $name = $client->user->first_name . " " . $client->user->last_name;
            //     $url = 'https://admin.rockgardenehr.com';
            //     $email = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_ADMIN_UPDATED','AND')->where('send_email','1')->First();
            //     if ($email) {
                 
            //         \Mail::to($client->user->email)->send(new \App\Mail\HealthIssueAdminUpdatedMail($name, $url));
            //     }
            //      $sms = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_ADMIN_UPDATED','AND')->where('send_sms','1')->First();
            //     if($sms){
            //         TwilioSMSController::sendSMS($client->user->phone_num, TwilioSMSController::healthIssueAdminUpdatedMessage($name, $url));
            //     }
            //     event(new HealthIssueFriendEvent($issue, $client->user->email, $client->user->id, $client->user->phone_num, $client->id));
            //     event(new HealthIssueAdminEvent($issuAdmin, $client->user->email, $client->user->id, $client->user->phone_num));
            // }
            define('dashboard_link', 'https://admin.rockgardenehr.com');
           
                     if(!empty($client)){
                       $emailNotifications = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_ADMIN_UPDATED')
                    ->where('send_email', 1)
                    ->get();

                $smsNotifications = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_ADMIN_UPDATED')
                    ->where('send_sms', 1)
                    ->get();
       
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'HEALTH_ISSUE_ADMIN_UPDATED')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                        $name = $client->user->first_name . " " . $client->user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
                        
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new HealthIssueAdminUpdatedMail($name,$healthIssue->title, $url);
                                Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::healthIssueAdminUpdatedMessage($name,$healthIssue->title, $url);
                                Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
                    
                    
                    //For send notification to friends,
                    // return  $client->friends;
                    $emailNotification = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_FRIEND_UPDATED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'HEALTH_ISSUE_FRIEND_UPDATED')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    foreach ( $client->friends as $friend) {
                        $email = $friend->email;
                      $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new HealthIssueFriendUpdatedMail($name,$healthIssue->title, $familyfriend_name);
                        Helper::sendEmail($email, $mail);
                    }
                    
                        
                    
                    
                
                }
                
                if ($smsNotification) {
                    foreach ( $client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::healthIssueFriendUpdatedMessage($name,$healthIssue->title, $familyfriend_name);
                        Helper::sendSms($phoneNumber, $message);
                    }
                
                        
                   
                }
                }
            return response()->json([
                'success' => true,
                'message' => $healthIssue->id
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

            HealthIssue::where('id', $request->get('id'))->delete();
            return response()->json([
                'success' => true,
                'message' => "Health Issue successfully deleted"
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }
}
