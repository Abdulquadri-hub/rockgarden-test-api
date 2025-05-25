<?php

namespace App\Http\Controllers\Api;

use App\Dto\DeathRecordDto;
use App\Dto\EventType;
use App\Events\DeathRecordEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeathRecordCollection;
use App\Http\Resources\DeathRecordResource;
use App\Mail\DeathRecordAdminMail;
use App\Models\Client;
use App\Models\DeathRecord;
use App\Helpers\Helper;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\NotificationSettings;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class DeathRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return \response()->json([
                'success' => true,
                'message' => new DeathRecordCollection(DeathRecord::all())
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
        // return \response()->json([
        //         'success' => true,
        //         'message' => new DeathRecordCollection(DeathRecord::orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
        //     ]
        //     , Response::HTTP_OK);
        $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    if (!empty($from_date) && !empty($to_date)) {
        $res = DeathRecord::whereBetween('created_at', [$from_date, $to_date])->orderBy('created_at', 'DESC')->paginate((int) $request->get('limit'));
    } else {
        $res = DeathRecord::orderBy('created_at', 'DESC')->paginate((int) $request->get('limit'));
    }

    return response()->json([
        'success' => true,
        'message' => new DeathRecordCollection($res)
    ], Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only('cause_of_death', 'date_of_death', 'time_of_death', 'client_id'),[
                'cause_of_death' => 'required|string',
                'date_of_death' => 'required|date',
                'time_of_death' => 'required',
                'client_id' => 'required'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Death Record data.'
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

            // Create new DeathRecord name
            $deathRecord = new DeathRecord();
            $deathRecord->cause_of_death = $request->get('cause_of_death');
            $deathRecord->date_of_death = new \DateTime($request->get('date_of_death'));
            $deathRecord->time_of_death =  $request->get('time_of_death');
            $deathRecord->client_id = $request->get('client_id');
            $deathRecord->save();

                           $user = User::where('id', $client->user_id)->first();
                $url = 'https://admin.rockgardenehr.com';
                $name = $user->first_name . " " . $user->last_name;
                
                 $emailNotification = NotificationSettings::where('trigger_name', 'DEATH_RECORD')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'DEATH_RECORD')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    $mail = new DeathRecordAdminMail($name, $url);
                    
                    foreach ($client->friends as $friend) {
                        $email = $friend->email;
                        Helper::sendEmail($email, $mail);
                    }
                    
                
                }
                
                if ($smsNotification) {
                    $message = TwilioSMSController::deathRecordAdminMessage($name, $url);
                
                    // Send SMS to client's family and friends
                    $familyAndFriends = $client->friends;
                
                    foreach ($familyAndFriends as $contact) {
                        //  $phoneNumber = $contact->phone_num;
                         $phoneNumber = "+2348097175974";
                        Helper::sendSms($phoneNumber, $message);
                    }
                }
                
                // Raise Events
                    // Raise Events
            $deathRecordDto = new DeathRecordDto($client->user->first_name.' '.$client->user->last_name,  EventType::DEATH_RECORD);
            event(new DeathRecordEvent($deathRecordDto, null, null, null));

            return response()->json([
                'success' => true,
                'message' => $deathRecord->id
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
        $deathRecord = DeathRecord::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new DeathRecordResource($deathRecord)
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
            // validate request data
            $validator = Validator::make($request->only('id','cause_of_death', 'date_of_death', 'time_of_death', 'client_id'),[
                'id' => 'required|integer',
                'cause_of_death' => 'required|string',
                'date_of_death' => 'required|date',
                'time_of_death' => 'required',
                'client_id' => 'required'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Death Record data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new DeathRecord name
            $deathRecord = DeathRecord::where('id', $request->get('id'))->first();
            if(empty($deathRecord)){
                return response()->json([
                    'success' => false,
                    'message' => 'Death Record not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $deathRecord->cause_of_death = $request->get('cause_of_death');
            $deathRecord->date_of_death = new \DateTime($request->get('date_of_death'));
            $deathRecord->time_of_death =  $request->get('time_of_death');
            $deathRecord->client_id = $request->get('client_id');
            $deathRecord->save();
            return response()->json([
                'success' => true,
                'message' => $deathRecord
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

        DeathRecord::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Death Record successfully deleted"
        ], Response::HTTP_OK);
    }
}
