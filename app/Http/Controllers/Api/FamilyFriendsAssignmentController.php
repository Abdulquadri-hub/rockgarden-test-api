<?php

namespace App\Http\Controllers\Api;

use App\Dto\EventType;
use App\Dto\FamilyFriendDto;
use App\Events\FamilyFriendEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\FriendFamilyAssignmentCollection;
use App\Http\Resources\FriendFamilyAssignmentResource;
use App\Models\Client;
use App\Models\FamilyFriendAssignment;
use App\Models\User;
use App\Models\NotificationSettings;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\FamilyFriendCreatedMail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class FamilyFriendsAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $familyfriend_id = $request->get('familyfriend_id');
        $client_id = $request->get('client_id');
        $limit =  $request->get('limit');
        $res = [];
        if(!empty($limit)){
            if(!empty($familyfriend_id) && !empty($client_id)){
                $res = FamilyFriendAssignment::where('familyfriend_id', $familyfriend_id)->where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($familyfriend_id)){
                $res = FamilyFriendAssignment::where('familyfriend_id', $familyfriend_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($client_id)){
                $res = FamilyFriendAssignment::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }else{
                $res = FamilyFriendAssignment::orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }else{
            if(!empty($familyfriend_id) && !empty($client_id)){
                $res = FamilyFriendAssignment::where('familyfriend_id', $familyfriend_id)->where('client_id', $client_id)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($familyfriend_id)){
                $res = FamilyFriendAssignment::where('familyfriend_id', $familyfriend_id)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($client_id)){
                $res = FamilyFriendAssignment::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->get();
            }else{
                $res = FamilyFriendAssignment::orderBy('updated_at', 'DESC')->get();
            }
        }
        $filtered_data = [];
        foreach (FriendFamilyAssignmentResource::collection($res) as $value) {
            $active = 1;
            if(isset($value['client']) && isset($value['client']['user'])) {
                $active = $value['client']["user"]["active"];
            }
            if($active == 1)
            array_push($filtered_data, $value);
        }
        return \response()->json([
                'success' => true,
                'message' => $filtered_data
            ]
            , Response::HTTP_OK);
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
                'message' => FriendFamilyAssignmentResource::collection(FamilyFriendAssignment::paginate((int) $request->get('limit')))
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
            $validator = Validator::make($request->only('familyfriend_id', 'client_id'),[
                'familyfriend_id' => 'required|integer',
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Family Friend Assignment data.'
                ], 400);
            }
            $client =  Client::where('id', $request->get('client_id'))->first();
            $userFriend =  User::where('id', $request->get('familyfriend_id'))->first();

            if(empty($client) || empty($userFriend)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client or friend not found.'
                ], 400);
            }

            // Create new Family Friend Assignment
            $familyFriendAssignment = new FamilyFriendAssignment();
            $familyFriendAssignment->client_id = $request->get('client_id');
            $familyFriendAssignment->familyfriend_id = $request->get('familyfriend_id');
            $familyFriendAssignment->save();

            // Raise Events
             $familyFriend = User::where('id', $request->get('familyfriend_id'))->first();
            $client = Client::where('id', $request->get('client_id'))->first();
           
            if(!empty($familyFriend) && !empty($client)){
             
            
          
            $emailNotification = NotificationSettings::where('trigger_name', 'FAMILY_FRIEND_CREATED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'FAMILY_FRIEND_CREATED')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    
                    $mail = new FamilyFriendCreatedMail($familyFriend->first_name.' '.$familyFriend->last_name, $client->user->first_name.' '.$client->user->last_name);
                        Helper::sendEmail($familyFriend->email, $mail);
                    
                    
                
                }
                
                if ($smsNotification) {
                    
                
                         $phoneNumber = $familyFriend->phone_num;
                         $message = TwilioSMSController::familyFriendCreatedMessage($familyFriend->first_name.' '.$familyFriend->last_name, $client->user->first_name.' '.$client->user->last_name);
                        Helper::sendSms($phoneNumber, $message);
                        // \Log::info('SMS is sent');
                   
                }
            //   $account = new FamilyFriendDto($familyFriend->first_name.' '.$familyFriend->last_name, $client->user->first_name.' '.$client->user->last_name, EventType::FAMILY_FRIEND_CREATED);
            //     event(new FamilyFriendEvent($account, $familyFriend->email, $familyFriend->id, $familyFriend->phone_num));
}

            return response()->json([
                'success' => true,
                'message' => $familyFriendAssignment->id
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
        $familyFriendAssignment = FamilyFriendAssignment::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new FriendFamilyAssignmentResource($familyFriendAssignment)
            ]
            , Response::HTTP_OK);
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

        FamilyFriendAssignment::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Family Friend Assignment successfully deleted"
        ], Response::HTTP_OK);
    }
}
