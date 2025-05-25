<?php

namespace App\Http\Controllers\Api;

use App\Dto\AccountDto;
use App\Dto\EventType;
use App\Events\NewAccountEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Http\Services\UserService;
use App\Mail\AccountActivatedMail;
use App\Mail\AccountDeActivatedMail;
use App\Models\Client;
use App\Models\Employee;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\NotificationSettings;
use App\Mail\DocumentStaffCreatedMail;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ClientController extends Controller
{

    public function clientChart(){
        return \response()->json([
                'success' => true,
                'message' => cache()->get('client_chart')
            ]
            , ResponseAlias::HTTP_OK);
    }

    public function clientActive(Request $request){

        $clientIdActive = DB::select( "select DISTINCT ct.id FROM clients AS ct INNER JOIN users AS usr ON ct.user_id = usr.id WHERE usr.active = 1");

        return \response()->json([
                'success' => true,
                'message' => new ClientCollection(Client::whereIn('id', $clientIdActive)->orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }

    public function clientNotActive(Request $request){

        $clientIdActive = DB::select( "select DISTINCT ct.id FROM clients AS ct INNER JOIN users AS usr ON ct.user_id = usr.id WHERE usr.active = 1");

        return \response()->json([
                'success' => true,
                'message' => new ClientCollection(Client::whereNotIn('id', $clientIdActive)->orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }

    public function clientAssigned(Request $request){
        $clientAssigned = DB::select( "select DISTINCT ct.id FROM clients AS ct INNER JOIN staff_assignments AS st ON ct.id = st.client_id");

        return \response()->json([
                'success' => true,
                'message' => new ClientCollection(Client::whereIn('id', $clientAssigned)->orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }

    public function clientNotAssigned(Request $request){
        $clientAssigned = DB::select( "select DISTINCT ct.id FROM clients AS ct INNER JOIN staff_assignments AS st ON ct.id = st.client_id");

        return \response()->json([
                'success' => true,
                'message' => new ClientCollection(Client::whereNotIn('id', $clientAssigned)->orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }
    
    public function clientAssignedStaff_(Request $request) 
    {
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'user_id field is required.'
            ], 404);
        }

        $loggedinUser = $request->get('user_id');
        
        $iSclient = Client::where("user_id", $loggedinUser)->get();
        if(empty($iSclient))
        {
            return response()->json([
                'success' => false,
                'message' => 'This user is not a client.'
            ], 404);
        }
        
        $staffAssigned = Client::join("users", "clients.user_id", "=", "users.id")
              ->join("staff_assignments", "clients.id", "=", "staff_assignments.client_id")
              ->join("employees", "staff_assignments.staff_id", "=", "employees.id")
              ->join('users as staff_user', 'employees.user_id', '=', 'staff_user.id')
              ->where("clients.user_id", $loggedinUser)
              ->select("employees.id as staff_id", "clients.id as client_id", "staff_user.first_name", "staff_user.last_name")
              ->get();
   
        
        if (empty($staffAssigned)) {
            return response()->json([
                'success' => false,
                'message' => 'Staff assigned not found!'
            ], 404);
        }
        


        return response()->json([
            'success' => true,
            "message" => "Assigned staff fetched successfully",
            "data" => $staffAssigned
        ], ResponseAlias::HTTP_OK);
    }
    
    public function clientAssignedStaff(Request $request) 
    {
        
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'client_id field is required.'
            ], 404);
        }

        $loggedinUser = $request->get('client_id');
        
        $iSclient = Client::where("id", $loggedinUser)->get();
        if(empty($iSclient))
        {
            return response()->json([
                'success' => false,
                'message' => 'This user is not a client.'
            ], 404);
        }
        
        $staffAssigned = Client::join("users", "clients.user_id", "=", "users.id")
              ->join("staff_assignments", "clients.id", "=", "staff_assignments.client_id")
              ->join("employees", "staff_assignments.staff_id", "=", "employees.id")
              ->join('users as staff_user', 'employees.user_id', '=', 'staff_user.id')
              ->where("clients.id", $loggedinUser)
              ->select("employees.id as staff_id", "clients.id as client_id", "staff_user.first_name", "staff_user.last_name")
              ->get();
   
        
        if (empty($staffAssigned)) {
            return response()->json([
                'success' => false,
                'message' => 'Staff assigned not found!'
            ], 404);
        }
        


        return response()->json([
            'success' => true,
            "message" => "Assigned staff fetched successfully",
            "data" => $staffAssigned
        ], ResponseAlias::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit');

        if(empty($limit)){
            return \response()->json([
                    'success' => true,
                    'message' => new ClientCollection(Client::orderBy('updated_at', 'DESC')->get())
                ]
                , ResponseAlias::HTTP_OK);
        }
        return \response()->json([
                'success' => true,
                'message' => new ClientCollection(Client::orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
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
                'message' => new ClientCollection(Client::paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Request $request)
    {
        
          
        try {
            DB::beginTransaction();
            $userService = new UserService();
//  return 1; 
            $request['role'] = ['Client'];

            $user_id = $request->get('id');
            $password = $request->get('password');
            $email =  $request->get('email');

            $latestUser = User::orderBy('created_at','DESC')->first();
            $request['email'] = !empty($email) ? $email : '000'.($latestUser === null ? 1 : ($latestUser->id + 1))."@gmail.com";
            $request['password'] = !empty($password) ? $password : strtolower($request->get('last_name'));

            if(!empty($user_id)){
                $client = Client::where('user_id', $user_id)->first();
                if(!empty($client)){
                    return response()->json([
                        'success' => true,
                        'message' => 'Existing client for this user account.'
                    ], 400);
                }
            }else{
                // Create new Users
                
                $userResponse = $userService->register($request);
                if(!$userResponse['success']){
                    return response()->json($userResponse, 400);
                }
                $user_id = (int)$userResponse['message'];
            }

            $user = User::where('id', $user_id)->first();

           
            if(empty($user)){
                return response()->json([
                    'success' => true,
                    'message' => 'User not found.'
                ], 400);
            }

            // Create Client
            $client = new Client();
            $latestClt = Client::orderBy('created_at','DESC')->first();
//            $client->client_no = 'CLT'.$userService->generateCode($latestClt === null ? 1 : ($latestClt->id + 1));
//
            $client_info = $request->get('client_info');
            $client->client_no = $client_info['client_no'] ?? null;
            $client->category = $client_info['category'] ?? null;
            $client->nationality = $client_info['nationality'] ?? null;
            $client->marital_status = $client_info['marital_status'] ?? null;
            $client->religious_pref = $client_info['religious_pref'] ?? null;
            $client->after_death_pref = $client_info['after_death_pref'] ?? null;
            $client->sex_of_carer_pref = $client_info['sex_of_carer_pref']?? null;
            $client->doctors_surgery = $client_info['doctors_surgery'] ?? null;
            $client->gp = $client_info['gp']?? null;
            $client->mental_health_doctor = $client_info['mental_health_doctor']?? null;
            $client->funeral_director = $client_info['funeral_director']?? null;
            $client->allergies = $client_info['allergies'] ?? null;
            $client->medical_diagnosis = $client_info['medical_diagnosis'] ?? null;
            $client->medical_history = $client_info['medical_history'] ?? null;
            $client->current_illness = $client_info['current_illness'] ?? null;
            $client->dietary_needs = $client_info['dietary_needs'] ?? null;
            $client->treatment_guide = $client_info['treatment_guide'] ?? null;
            $client->treatment_guide_info = $client_info['treatment_guide_info'] ?? null;
            $client->height_cm = $client_info['height_cm'] ?? null;
            $client->eye_colour = $client_info['eye_colour'] ?? null;
            $client->hair_colour = $client_info['hair_colour'] ?? null;
            $client->build = $client_info['build'] ?? null;
            $client->hair_length = $client_info['hair_length'] ?? null ;
            $client->eye_wear = $client_info['eye_wear'] ?? null;
            $client->weight_on_admission_kg = $client_info['weight_on_admission_kg'] ?? null;
            $client->uses_hearing_aid = $client_info['uses_hearing_aid'] ?? null;
            $client->maiden_name = $client_info['maiden_name'] ?? null;
            $client->prev_occupation = $client_info['prev_occupation'] ?? null;
            $client->date_of_arrival = new \DateTime($client_info['date_of_arrival']);
            $client->client_type = $client_info['client_type'] ?? null;
            $client->careplan = $client_info['careplan'] ?? null;
            $client->user_id = $user_id;
            $client->room_location = $client_info['room_location'] ?? null;
            $client->room_number = $client_info['room_number'] ?? null;
            $client->room_suffix = $client_info['room_suffix'] ?? null;
            $client->prev_address = $client_info['prev_address'] ?? null;
            $client->postal_code = $client_info['postal_code'] ?? null;
            $client->admitted_from = $client_info['admitted_from'] ?? null;
            $client->admitted_by = $client_info['admitted_by'] ?? null;
            $client->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $client->id
            ], ResponseAlias::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseAlias::HTTP_CONFLICT
            );
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $designation = Client::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new ClientResource($designation)
            ]
            ,
            ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request): JsonResponse
    {
        try {

            DB::beginTransaction();
            // Retrieve Client
            $client_info = $request->get('client_info');

            $client = Client::where('id', $client_info['id'])->first();
            if($client ===  null){
                DB::commit();
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found.'
                ], 404);
            }

            // Update user
            $userService = new UserService();
            $userResponse = $userService->update($request);

            if(!$userResponse['success']){
                DB::commit();
                return response()->json($userResponse, 400);
            }

            // Update Client
            $client->client_no = $client_info['client_no'];
            $client->category = $client_info['category'] ?? null;
            $client->nationality = $client_info['nationality'] ?? null;
            $client->marital_status = $client_info['marital_status'] ?? null;
            $client->religious_pref = $client_info['religious_pref'] ?? null;
            $client->after_death_pref = $client_info['after_death_pref'] ?? null;
            $client->sex_of_carer_pref = $client_info['sex_of_carer_pref'] ?? null;
            $client->doctors_surgery = $client_info['doctors_surgery'] ?? null;
            $client->gp = $client_info['gp'] ?? null;
            $client->mental_health_doctor = $client_info['mental_health_doctor'] ?? null;
            $client->funeral_director = $client_info['funeral_director'] ?? null;
            $client->allergies = $client_info['allergies'] ?? null;
            $client->medical_diagnosis = $client_info['medical_diagnosis'] ?? null;
            $client->medical_history = $client_info['medical_history'] ?? null;
            $client->current_illness = $client_info['current_illness'] ?? null;
            $client->dietary_needs = $client_info['dietary_needs'] ?? null;
            $client->treatment_guide = $client_info['treatment_guide'] ?? null;
            $client->treatment_guide_info = $client_info['treatment_guide_info'] ?? null;
            $client->height_cm = $client_info['height_cm'] ?? null;
            $client->eye_colour = $client_info['eye_colour'] ?? null;
            $client->hair_colour = $client_info['hair_colour'] ?? null;
            $client->build = $client_info['build'] ?? null;
            $client->hair_length = $client_info['hair_length'] ?? null;
            $client->eye_wear = $client_info['eye_wear'] ?? null;
            $client->weight_on_admission_kg = $client_info['weight_on_admission_kg'] ?? null;
            $client->uses_hearing_aid = $client_info['uses_hearing_aid'] ?? null;
            $client->maiden_name = $client_info['maiden_name'] ?? null;
            $client->prev_occupation = $client_info['prev_occupation'] ?? null;
            $client->date_of_arrival = new \DateTime($client_info['date_of_arrival']);
            $client->client_type = $client_info['client_type'] ?? null;
            $client->careplan = $client_info['careplan'] ?? null;
            $client->user_id = (int)$userResponse['message'];
            $client->room_location = $client_info['room_location'] ?? null;
            $client->room_number = $client_info['room_number'] ?? null;
            $client->room_suffix = $client_info['room_suffix'] ?? null;
            $client->prev_address = $client_info['prev_address'] ?? null;
            $client->postal_code = $client_info['postal_code'] ?? null;
            $client->admitted_from = $client_info['admitted_from'] ?? null;
            $client->admitted_by = $client_info['admitted_by'] ?? null;
            $client->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $client->id
            ], ResponseAlias::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseAlias::HTTP_CONFLICT
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reactivate(Request $request)
    {
        try {
            //Validate data
            $validator = Validator::make($request->only('id'), [
                'id' => 'required|integer',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return \response()->json(
                    [
                        'success' => false,
                        'message' => 'Client id not found.'
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );
            }

            $client = Client::where('id','=',$request->id)->first();
            if(empty($client)){
                return \response()->json(
                    [
                        'success' => false,
                        'message' => 'Client not found.'
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );
            }
            User::where('id','=', $client->user_id)->update(
                [
                    'active' => 1
                ]
            );

            $user = User::where('id', $client->user_id)->first();
TwilioSMSController::sendSMS($user->phone_num, TwilioSMSController::newAccountMessage($user->first_name.' '.$user->last_name, $user->otp));
            // Raise Events
            $emailNotification = NotificationSettings::where('trigger_name', 'ACCOUNT_ACTIVATED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'ACCOUNT_ACTIVATED')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    $mail = new AccountActivatedMail($user->first_name.' '.$user->last_name);
                    
                    foreach ($client->friends as $friend) {
                        $email = $friend->email;
                        Helper::sendEmail($email, $mail);
                    }
                    
                
                }
                
                if ($smsNotification) {
                    $message = TwilioSMSController::accountActivatedMessageMessage($user->first_name.' '.$user->last_name);
                
                    // Send SMS to client's family and friends
                    $familyAndFriends = $client->friends;
                
                    foreach ($familyAndFriends as $contact) {
                         $phoneNumber = $contact->phone_num;
                        Helper::sendSms($phoneNumber, $message);
                    }
                }
            $account = new AccountDto($user->first_name.' '.$user->last_name, null, null, $user->first_name.' '.$user->last_name, $user->email, null, EventType::ACCOUNT_ACTIVATED);
            event(new NewAccountEvent($account, $user->email, $user->id, $user->phone_num));

            return \response()->json(
                [
                    'success' => true,
                    'message' => "Account activated successfully."
                ], ResponseAlias::HTTP_OK
            );
        }catch (Exception $e){
            Log::error($e->getMessage());
            return \response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseAlias::HTTP_OK
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deactivate(Request $request)
    {
        try {
            //Validate data
            $validator = Validator::make($request->only('id'), [
                'id' => 'required|integer',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return \response()->json(
                    [
                        'success' => false,
                        'message' => 'Client id not found.'
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );
            }

            $client = Client::where('id','=',$request->id)->first();
            if(empty($client)){
                return \response()->json(
                    [
                        'success' => false,
                        'message' => 'Client not found.'
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );
            }
            User::where('id','=', $client->user_id)->update(
                [
                    'active' => 0
                ]
            );

            $user = User::where('id', $client->user_id)->first();
            TwilioSMSController::sendSMS($user->phone_num, TwilioSMSController::newAccountMessage($user->first_name.' '.$user->last_name, $user->otp));
            DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->delete();
            // Raise Events
           
            $emailNotification = NotificationSettings::where('trigger_name', 'ACCOUNT_DEACTIVATED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'ACCOUNT_DEACTIVATED')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    $user_fullname=$user->first_name.' '.$user->last_name;
                    $mail = new AccountDeActivatedMail($user_fullname);
                    // return $client->friends;
                    foreach ($client->friends as $friend) {
                        $email = $friend->email;
                        Helper::sendEmail($email, $mail);
                    }
                    
                
                }
                
                if ($smsNotification) {
                    $user_fullname=$user->first_name.' '.$user->last_name;
                    $message = TwilioSMSController::accountDeActivatedMessage($user_fullname);
                
                    // Send SMS to client's family and friends
                    $familyAndFriends = $client->friends;
                
                    foreach ($familyAndFriends as $contact) {
                          $phoneNumber = $contact->phone_num;
                        Helper::sendSms($phoneNumber, $message);
                    }
                }
            $account = new AccountDto($user->first_name.' '.$user->last_name, null, null, $user->first_name.' '.$user->last_name, $user->email, null, EventType::ACCOUNT_DEACTIVATED);
            event(new NewAccountEvent($account, $user->email, $user->id, $user->phone_num));

            return \response()->json(
                [
                    'success' => true,
                    'message' => "Account deactivated successfully."
                ], ResponseAlias::HTTP_OK
            );
        }catch (Exception $e){
            Log::error($e->getMessage());
            return \response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseAlias::HTTP_OK
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
                ], ResponseAlias::HTTP_BAD_REQUEST);
            }
            DB::beginTransaction();
            $client  =  Client::where('id', $request->get('id'))->first();
            User::where('id', $client->user_id)->delete();

            Client::where('id', $request->get('id'))->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Client successfully deleted"
            ], ResponseAlias::HTTP_OK);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], ResponseAlias::HTTP_OK);
        }
    }
}
