<?php

namespace App\Http\Controllers\Api;

use App\Dto\EventType;
use App\Dto\IncidentDto;
use App\Events\IncidentEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Resources\IncidentCollection;
use App\Http\Resources\IncidentResource;
use App\Models\Client;
use App\Models\Employee;
use App\Models\SystemContacts;
use App\Helpers\Helper;
use App\Models\NotificationSettings;
use App\Mail\NewIncidentAdminMail;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class IncidentController extends Controller
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
        $staff_present_id = $request->get('staff_present_id');
        $limit = $request->get('limit');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        if (!empty($limit)){
            if (!empty($staff_present_id) && !empty($client_id) && !empty($staff_id)){
                $res = Incident::where('client_id', $client_id)
                    ->where('staff_present_id', $staff_present_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->where('staff_id', $staff_id)
                    ->paginate($limit);
            }
            elseif (!empty($staff_present_id) && !empty($client_id)){
                $res = Incident::where('client_id', $client_id)
                    ->where('staff_present_id', $staff_present_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->paginate($limit);
            }
            elseif (!empty($client_id) && !empty($staff_id)){
                $res = Incident::where('client_id', $client_id)
                    ->where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->paginate($limit);
            }
            elseif (!empty($staff_present_id) && !empty($staff_id)){
                $res = Incident::where('staff_id', $staff_id)
                    ->where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->paginate($limit);
            }
            elseif(!empty($staff_present_id)){
                $res = Incident::where('staff_present_id', $staff_present_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->paginate($limit);
            }
            elseif(!empty($staff_id)){
                $res = Incident::where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->paginate($limit);
            }
            elseif (!empty($client_id)){
                $res = Incident::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->paginate($limit);
            }else{
                return \response()->json([
                    'success' => true,
                    'message' => new IncidentCollection(Incident::orderBy('created_at', 'DESC')->paginate($limit))
                ], Response::HTTP_OK);
            }
        }
        else
        {
            if (!empty($staff_present_id) && !empty($client_id) && !empty($staff_id)){
                $res = Incident::where('client_id', $client_id)
                    ->where('staff_present_id', $staff_present_id)
                    ->where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->get();
            }
            elseif (!empty($staff_present_id) && !empty($client_id)){
                $res = Incident::where('client_id', $client_id)
                    ->where('staff_present_id', $staff_present_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->get();
            }
            elseif (!empty($client_id) && !empty($staff_id)){
                $res = Incident::where('client_id', $client_id)
                    ->where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->get();
            }
            elseif (!empty($staff_present_id) && !empty($staff_id)){
                $res = Incident::where('staff_id', $staff_id)
                    ->where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->get();
            }
            elseif(!empty($staff_present_id)){
                $res = Incident::where('staff_present_id', $staff_present_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->get();
            }
            elseif(!empty($staff_id)){
                $res = Incident::where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->get();
            }
            elseif (!empty($client_id)){
                $res = Incident::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->get();
            }else{
                return \response()->json([
                    'success' => true,
                    'message' => new IncidentCollection(Incident::orderBy('created_at', 'DESC')->get())
                ], Response::HTTP_OK);
            }
        }
        if (!empty($from_date) && !empty($to_date)) {
        $res = Incident::whereBetween('created_at', [$from_date, $to_date])->get();
    }

        return \response()->json([
            'success' => true,
            'message' => new IncidentCollection($res)
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
            'message' => new IncidentCollection(Incident::paginate((int) $request->get('limit')))
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
            if (!empty($client_id)){
                $client = Client::where('id', $client_id)->first();
                if(empty($client)){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Client not found for incident."
                        ], Response::HTTP_BAD_REQUEST);
                }
            }

            // Create new Incident
            $incident = new Incident();

            $incident->client_id = $client->id;
            $incident->description = $request->get('description');
            $incident->title = $request->get('title');
            $incident->media1 = $request->get('media1');
            $incident->media2 = $request->get('media2');
            $incident->media3 = $request->get('media3');
            $incident->media4 = $request->get('media4');
            $incident->report_date = new \DateTime($request->get('report_date'));
            $staff_present_id = $request->get('staff_present_id');

            if(!empty($staff_present_id)){
                $staff_present = Employee::where('id', $staff_present_id)->first();
                if(empty($staff_present)){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Staff present not found for incident."
                        ], Response::HTTP_BAD_REQUEST);
                }
                $incident->staff_present_id = $request->get('staff_present_id');
            }

            $staff = Employee::where('user_id', Auth::user()->id)->first();
            if(empty($staff)){
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Staff not found."
                    ], Response::HTTP_BAD_REQUEST);
            }

            $incident->staff_id = $staff->id;

            $incident->save();

            // Raise Events
            if(!empty($client)){
             
                $emailNotifications = NotificationSettings::where('trigger_name', 'INCIDENT_ADMIN')
                    ->where('send_email', 1)
                    ->get();

                $smsNotifications = NotificationSettings::where('trigger_name', 'INCIDENT_ADMIN')
                    ->where('send_sms', 1)
                    ->get();
       
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'INCIDENT_ADMIN')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                        $name = $client->user->first_name . " " . $client->user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
                        
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new NewIncidentAdminMail($name, $url);
                                Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::newIncidentAdminMessage($name, $url);
                                Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
                    
                    
                    //For send notification to friends,
                    // return  $client->friends;
                //     $emailNotification = NotificationSettings::where('trigger_name', 'INCIDENT_FRIEND')
                //     ->where('send_email', 1)
                //     ->first();
                
                // $smsNotification = NotificationSettings::where('trigger_name', 'INCIDENT_FRIEND')
                //     ->where('send_sms', 1)
                //     ->first();
               
                // if ($emailNotification) {
                //     foreach ( $client->friends as $friend) {
                //         $email = $friend->email;
                //       $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                //     $mail = new HealthIssueFriendMail($name, $familyfriend_name);
                //         Helper::sendEmail($email, $mail);
                //     }
                    
                //        
                    
                    
                
                // }
                
                // if ($smsNotification) {
                //     foreach ( $client->friends as $contact) {
                //           $phoneNumber = $contact->phone_num;
                //          $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                //          $message = TwilioSMSController::healthIssueAdminMessage($name, $familyfriend_name);
                //         Helper::sendSms($phoneNumber, $message);
                //     }
                
                        
                   
                // }
                // $incidentDto = new IncidentDto($client->user->first_name.' '.$client->user->last_name, EventType::INCIDENT);
                // event(new IncidentEvent($incidentDto, $client->user->email, $client->user->id, $client->user->phone_num));
            }

            return response()->json([
                'success' => true,
                'message' => $incident->id
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
        $incident = Incident::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new IncidentResource($incident)
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
            if (!empty($client_id)){
                $client = Client::where('id', $client_id)->first();
                if(empty($client)){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Client not found for incident."
                        ], Response::HTTP_BAD_REQUEST);
                }
            }

            // Create new Incident
            $incident = Incident::where('id', $request->get('id'))->first();
            if(empty($incident)){
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Health issue not found."
                    ], Response::HTTP_BAD_REQUEST);
            }

            $incident->client_id = $client->id;
            $incident->description = $request->get('description');
            $incident->title = $request->get('title');
            $incident->media1 = $request->get('media1');
            $incident->media2 = $request->get('media2');
            $incident->media3 = $request->get('media3');
            $incident->media4 = $request->get('media4');
            $incident->report_date = new \DateTime($request->get('report_date'));
            $staff_present_id = $request->get('staff_present_id');

            if(!empty($staff_present_id)){
                $staff_present = Employee::where('id', $staff_present_id)->first();
                if(empty($staff_present)){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Staff present not found for incident."
                        ], Response::HTTP_BAD_REQUEST);
                }
                $incident->staff_present_id = $request->get('staff_present_id');
            }

            $staff = Employee::where('user_id', Auth::user()->id)->first();
            if(empty($staff)){
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Staff not found."
                    ], Response::HTTP_BAD_REQUEST);
            }

            $incident->staff_id = $staff->id;

            $incident->save();
            return response()->json([
                'success' => true,
                'message' => $incident->id
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

            Incident::where('id', $request->get('id'))->delete();
            return response()->json([
                'success' => true,
                'message' => "Incident successfully deleted"
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_OK);
        }
    }
}
