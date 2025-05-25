<?php

namespace App\Http\Controllers\Api;

use App\Dto\DocumentDto;
use App\Dto\EventType;
use App\Events\DocumentClientEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\NotificationSettings;
use App\Helpers\Helper;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Mail\DocumentClientCreatedMail;
use App\Mail\DocumentClientUpdatedMail;
use App\Models\Client;
use App\Models\User;
use App\Models\Document;
use App\Models\FamilyFriendAssignment;
use App\Models\StaffAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // $client_id = $request->get('client_id');
        // $staff_id =  $request->get('staff_id');
        // $user_id = $request->get('user_id');
        // $from_date = $request->get('from_date');
        // $to_date = $request->get('to_date');

        // if(!empty($user_id)){
        //     $clientIds = FamilyFriendAssignment::where('familyfriend_id', $user_id)->pluck('client_id');
        // }

        // $limit = $request->get('limit');

        // $res = [];
        // if(!empty($staff_id)){
        //     $clientIds = StaffAssignment::where('staff_id', $staff_id)->pluck('client_id');
        // }

        // if(!empty($limit)){
        //     if(!empty($client_id)){
        //         $res = Document::where('client_id', $client_id)->orderBy('created_at', 'DESC')->paginate($limit);
        //     }elseif (!empty($clientIds)){
        //         $res = Document::whereIn('client_id', $clientIds)->orderBy('created_at', 'DESC')->paginate($limit);
        //     }else{
        //         $res = Document::orderBy('created_at', 'DESC')->paginate($limit);
        //     }
        // }else{
        //     if(!empty($client_id)){
        //         $res = Document::where('client_id', $client_id)->orderBy('created_at', 'DESC')->get();
        //     }elseif (!empty($clientIds)){
        //         $res = Document::whereIn('client_id', $clientIds)->orderBy('created_at', 'DESC')->get();
        //     }else{
        //         $res = Document::orderBy('created_at', 'DESC')->get();
        //     }
        // }

        // return \response()->json([
        //         'success' => true,
        //         'message' => new DocumentCollection($res)
        //     ]
        //     , Response::HTTP_OK);
        $client_id = $request->get('client_id');
$staff_id =  $request->get('staff_id');
$user_id = $request->get('user_id');
$from_date = $request->get('from_date');
$to_date = $request->get('to_date');
$limit = $request->get('limit');

if (!empty($user_id)) {
    $clientIds = FamilyFriendAssignment::where('familyfriend_id', $user_id)->pluck('client_id');
}

$res = [];
if (!empty($staff_id)) {
    $clientIds = StaffAssignment::where('staff_id', $staff_id)->pluck('client_id');
}

if (!empty($from_date) && !empty($to_date)) {
    if (!empty($client_id)) {
        $res = Document::where('client_id', $client_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    } elseif (!empty($clientIds)) {
        $res = Document::whereIn('client_id', $clientIds)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    } else {
        $res = Document::whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    }
} else {
    if (!empty($client_id)) {
        $res = Document::where('client_id', $client_id)
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    } elseif (!empty($clientIds)) {
        $res = Document::whereIn('client_id', $clientIds)
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    } else {
        $res = Document::orderBy('created_at', 'DESC')
            ->paginate($limit);
    }
}

return \response()->json([
    'success' => true,
    'message' => new DocumentCollection($res)
], Response::HTTP_OK);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'client_id' => 'required|integer',
                'file_url' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Document data.'
                ], 400);
            }

            // Create new Document
            $document = new Document();

            $client_id = $request->get('client_id');
            $client = null;
            if(!empty($client_id)){
                $client =  Client::where('id', $client_id)->first();
                if(empty($client)){
                    return response()->json([
                        'success' => false,
                        'message' => 'Client not found.'
                    ], 400);
                }
                $document->client_id = $client->id;
            }

            $document->doc_title = $request->get('doc_title');
            $document->doc_desc = $request->get('doc_desc');
            $document->file_url = $request->get('file_url');
            $document->save();

            // Raise Events
            // if(!empty($client)){
            //      $friends = UserService::familyFriendUsers($client_id);
            //     $email = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_CREATED','AND')->where('send_email','1')->First();
            //     if ($email) {
            //         # code...
            //         Mail::to($friends->email)->send(new DocumentClientCreatedMail($friends->first_name.' '.$friends->last_name, $client->user->first_name.' '.$client->user->last_name));
            //     }
            //     $sms = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_CREATED','AND')->where('send_sms','1')->First();
            //     if($sms){
            //         TwilioSMSController::sendSMS($friends->phone_num, TwilioSMSController::documentClientCreatedMessage($friends->first_name.' '.$friends->last_name, $client->user->first_name.' '.$client->user->last_name));
            //     }
            //     $documentDto = new DocumentDto(null, null, $client->user->first_name.' '.$client->user->last_name, EventType::DOCUMENT_CLIENT_CREATED);
            //     event(new DocumentClientEvent($documentDto,  $client->user->email, $client->user->id, $client->user->phone_num, $client->id));
            // }
            
               
             
            
            
            $user = User::where('id', $client->user_id)->first();
            $name=$user->first_name.' '.$user->last_name;
              $client_friends=Client::where('id',$client_id)->with('friends')->get();
            
            
            
            $emailNotification = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_CREATED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_CREATED')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    $familyfriend_name = '';
                    foreach ($client->friends as $friend) {
                        $email = $friend->email;
                         $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new DocumentClientCreatedMail($familyfriend_name, $name);
                        Helper::sendEmail($email, $mail);
                    }
                    
                
                }
                
                if ($smsNotification) {
                    
                
                    // Send SMS to client's family and friends
                    // $familyAndFriends = $client->friends;
                
                    foreach ($client->friends as $contact) {
                         $phoneNumber = $contact->phone_num;
                          $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::documentClientCreatedMessage('$familyfriend_name', $name);
                        Helper::sendSms($phoneNumber, $message);
                    }
                }
                $documentDto = new DocumentDto(null, null, $client->user->first_name.' '.$client->user->last_name, EventType::DOCUMENT_CLIENT_CREATED);
                event(new DocumentClientEvent($documentDto,  $client->user->email, $client->user->id, $client->user->phone_num, $client->id));

            return response()->json([
                'success' => true,
                'message' => $document->id
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
    public function storeAll(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(),[
                'datas' => 'required|array',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Document data.'
                ], 400);
            }

            $datas = $request->get('datas');

            $documents =  [];

            if(!empty($datas)){
                foreach ($datas as $data){
                    $client_id =$data['client_id'];
                    $client = null;
                    if(!empty($client_id)){
                        $client =  Client::where('id', $client_id)->first();
                        if(!empty($client)){
                            $documents[] =  [
                                'doc_title' => $data['doc_title'],
                                'doc_desc' => $data['doc_desc'],
                                'file_url' =>$data['file_url'],
                                'client_id' => $client->id,
                                'client' => $client
                            ];
                        }
                    }
                }
            }

            // Store All
            if(!empty($documents)){
                foreach ($documents as $document){
                    $matchThese = ['client_id'=> $document['client_id'], 'file_url'=> $document['file_url']];
                    $toSave = [];
                    $toSave['doc_title'] = $document['doc_title'];
                    $toSave['doc_desc'] = $document['doc_desc'];
                    $toSave['file_url'] =$document['file_url'];
                    $toSave['client_id'] = $document['client_id'];

                    Document::updateOrCreate($matchThese, $toSave);

                    // Raise Events
                    // if(!empty($document['client'])){
                    //   $client =  $document['client'];
                    //     $friends = UserService::familyFriendUsers($document['client_id']);
                    //     $email = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_CREATED','AND')->where('send_email','1')->First();
                    //     if($email){

                    //         Mail::to($friends->email)->send(new DocumentClientCreatedMail($friends->first_name.' '.$friends->last_name, $client->user->first_name.' '.$client->user->last_name));
                    //     }
                    //      $sms = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_CREATED','AND')->where('send_sms','1')->First();
                    //     if($sms){
                    //         TwilioSMSController::sendSMS($friends->phone_num, TwilioSMSController::documentClientCreatedMessage($friends->first_name.' '.$friends->last_name, $client->user->first_name.' '.$client->user->last_name));
                    //     }
                    //     $documentDto = new DocumentDto(null, null, $client->user->first_name.' '.$client->user->last_name, EventType::DOCUMENT_CLIENT_CREATED);
                    //     event(new DocumentClientEvent($documentDto,  $client->user->email, $client->user->id, $client->user->phone_num, $client->id));
                    // }
                }
            }

            return response()->json([
                'success' => true,
                'message' => $documents
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
        $document = Document::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new DocumentResource($document)
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
            $validator = Validator::make($request->only('id'),[
                'id' => 'required|integer',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Document data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new Document

            $document = Document::where('id', $request->get('id'))->firstOrFail();
            if(empty($document)){
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $client_id = $request->get('client_id');
            $client = null;
            if(!empty($client_id)){
                $client =  Client::where('id', $client_id)->first();
                if(empty($client)){
                    return response()->json([
                        'success' => false,
                        'message' => 'Client not found.'
                    ], 400);
                }
                $document->client_id = $client->id;
            }

            $document->doc_title = $request->get('doc_title');
            $document->doc_desc = $request->get('doc_desc');
            $document->file_url = $request->get('file_url');
            $document->save();

            // Raise Events
            // if(!empty($client)){
            //       $friends = UserService::familyFriendUsers($document['client_id']);
            //     $email = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_UPDATE','AND')->where('send_email','1')->First();
            //     if($email){

            //         Mail::to($friends->email)->send(new DocumentClientUpdatedMail($friends->first_name.' '.$friends->last_name, $client->user->first_name.' '.$client->user->last_name));
            //     }
            //      $sms = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_UPDATE','AND')->where('send_sms','1')->First();
            //     if($sms){
            //         TwilioSMSController::sendSMS($friends->phone_num, TwilioSMSController::documentClientUpdatedMessage($friends->first_name.' '.$friends->last_name, $client->user->first_name.' '.$client->user->last_name));
            //     }
            //     $documentDto = new DocumentDto(null, null, $client->user->first_name.' '.$client->user->last_name, EventType::DOCUMENT_CLIENT_UPDATE);
            //     event(new DocumentClientEvent($documentDto,  $client->user->email, $client->user->id, $client->user->phone_num, $client->id));
            // }
               $user = User::where('id', $client->user_id)->first();
            $name=$user->first_name.' '.$user->last_name;
              $client_friends=Client::where('id',$client_id)->with('friends')->get();
            
            
            
            $emailNotification = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_UPDATE')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'DOCUMENT_CLIENT_UPDATE')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    $familyfriend_name = '';
                    foreach ($client->friends as $friend) {
                        $email = $friend->email;
                         $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new DocumentClientUpdatedMail($familyfriend_name, $name);
                        Helper::sendEmail($email, $mail);
                    }
                    
                
                }
                
                if ($smsNotification) {
                    
                $familyfriend_name = '';
                    // Send SMS to client's family and friends
                    // $familyAndFriends = $client->friends;
                
                    foreach ($client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::documentClientUpdatedMessage('$familyfriend_name', $name);
                        Helper::sendSms($phoneNumber, $message);
                    }
                }
                $documentDto = new DocumentDto(null, null, $client->user->first_name.' '.$client->user->last_name, EventType::DOCUMENT_CLIENT_CREATED);
                event(new DocumentClientEvent($documentDto,  $client->user->email, $client->user->id, $client->user->phone_num, $client->id));

            return response()->json([
                'success' => true,
                'message' => $document
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

        Document::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Document successfully deleted"
        ], Response::HTTP_OK);
    }
}
