<?php

namespace App\Http\Controllers\Api;

use App\Dto\DocumentDto;
use App\Dto\EventType;
use App\Events\DocumentStaffEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentStaffRequest;
use App\Http\Requests\UpdateDocumentStaffRequest;
use App\Http\Resources\DocumentStaffCollection;
use App\Http\Resources\DocumentStaffResource;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\DocumentStaff;
use App\Models\Employee;
use App\Helpers\Helper;
use App\Models\FamilyFriendAssignment;
use App\Models\StaffAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Mail\DocumentStaffCreatedMail;
use App\Mail\DocumentStaffUpdatedMail;
use App\Models\NotificationSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DocumentStaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $staff_id = $request->get('staff_id');
        $limit = $request->get('limit');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query = DocumentStaff::query();

        if (!empty($staff_id)) {
            $query->where('staff_id', $staff_id);
        }

        if (!empty($from_date)) {
    $query->whereDate('created_at', '>=', $from_date);
}

        if (!empty($to_date)) {
    $query->whereDate('created_at', '<=', $to_date);
}

        if (!empty($limit)) {
    $res = $query->orderBy('created_at', 'DESC')->paginate($limit);
        } else {
            $res = $query->orderBy('created_at', 'DESC')->get();
        }

        return \response()->json([
            'success' => true,
            'message' => new DocumentStaffCollection($res)
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
                'staff_id' => 'required|integer',
                'file_url' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Document data.'
                ], 400);
            }

            // Create new DocumentStaff
            $document = new DocumentStaff();

            $staff = null;
            $staff_id = $request->get('staff_id');
            if(!empty($staff_id)){
                $staff =  Employee::where('id', $staff_id)->first();
                if(empty($staff)){
                    return response()->json([
                        'success' => false,
                        'message' => 'Staff not found.'
                    ], 400);
                }
                $document->staff_id = $staff->id;
            }

            $document->doc_title = $request->get('doc_title');
            $document->doc_desc = $request->get('doc_desc');
            $document->file_url = $request->get('file_url');
            $document->save();

            // Raise Events
           
            // return $name = $staff->user;
            if(!empty($staff)){
             
            $name = $staff->user->first_name.' '.$staff->user->last_name;
          
            $emailNotification = NotificationSettings::where('trigger_name', 'DOCUMENT_STAFF_CREATED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'DOCUMENT_STAFF_CREATED')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    
                    $mail = new DocumentStaffCreatedMail($name);
                        Helper::sendEmail($staff->user->email, $mail);
                    
                    
                
                }
                
                if ($smsNotification) {
                    
                
                         $message = TwilioSMSController::documentStaffCreatedMessage($name);
                          $phoneNumber = $staff->user->phone_num;
                        Helper::sendSms($phoneNumber, $message);
                        \Log::info('SMS is sent');
                   
                }
                $documentDto = new DocumentDto($staff->user->first_name.' '.$staff->user->last_name, null, $staff->user->first_name.' '.$staff->user->last_name, EventType::DOCUMENT_STAFF_CREATED);
                event(new DocumentStaffEvent($documentDto, $staff->user->email, $staff->user->id, $staff->user->phone_num));
}
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
                    $staff_id =$data['staff_id'];
                    $staff = null;
                    if(!empty($staff_id)){
                        $staff =  Employee::where('id', $staff_id)->first();
                        if(!empty($staff)){
                            $documents[] =  [
                                'doc_title' => $data['doc_title'],
                                'doc_desc' => $data['doc_desc'],
                                'file_url' =>$data['file_url'],
                                'staff_id' => $staff->id,
                                'staff' => $staff
                            ];
                        }
                    }
                }
            }

            // Store All
            if(!empty($documents)){
                foreach ($documents as $document){
                    $matchThese = ['staff_id'=> $document['staff_id'], 'file_url'=> $document['file_url']];
                    $toSave = [];
                    $toSave['doc_title'] = $document['doc_title'];
                    $toSave['doc_desc'] = $document['doc_desc'];
                    $toSave['file_url'] =$document['file_url'];
                    $toSave['staff_id'] = $document['staff_id'];

                    DocumentStaff::updateOrCreate($matchThese, $toSave);

                    // Raise Events
                    if(!empty($document['staff'])){
                        $staff =  $document['staff'];
                        $documentDto = new DocumentDto($staff->user->first_name.' '.$staff->user->last_name, null, $staff->user->first_name.' '.$staff->user->last_name, EventType::DOCUMENT_STAFF_CREATED);
                        event(new DocumentStaffEvent($documentDto, $staff->user->email, $staff->user->id, $staff->user->phone_num));
                    }
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
        $document = DocumentStaff::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new DocumentStaffResource($document)
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
                    'message' => 'Invalid DocumentStaff data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new DocumentStaff

            $document = DocumentStaff::where('id', $request->get('id'))->firstOrFail();
            if(empty($document)){
                return response()->json([
                    'success' => false,
                    'message' => 'DocumentStaff not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $staff_id = $request->get('staff_id');
            $staff =  null;

            if(!empty($staff_id)){
                $staff =  Employee::where('id', $staff_id)->first();
                if(empty($staff)){
                    return response()->json([
                        'success' => false,
                        'message' => 'Staff not found.'
                    ], 400);
                }
                $document->staff_id = $staff->id;
            }

            $document->doc_title = $request->get('doc_title');
            $document->doc_desc = $request->get('doc_desc');
            $document->file_url = $request->get('file_url');
            $document->save();

           
                    // Raise Events
           
            // return $name = $staff->user;
            if(!empty($staff)){
             
            $name = $staff->user->first_name.' '.$staff->user->last_name;
          
            $emailNotification = NotificationSettings::where('trigger_name', 'DOCUMENT_STAFF_UPDATE')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'DOCUMENT_STAFF_UPDATE')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    
                    $mail = new DocumentStaffUpdatedMail($name);
                        Helper::sendEmail($staff->user->email, $mail);
                    
                    
                
                }
                
                if ($smsNotification) {
                    
                
                          $phoneNumber = $staff->user->phone_num;
                         $message = TwilioSMSController::documentStaffUpdatedMessage($name);
                        Helper::sendSms($phoneNumber, $message);
                        // \Log::info('SMS is sent');
                   
                }
                $documentDto = new DocumentDto($staff->user->first_name.' '.$staff->user->last_name, null, $staff->user->first_name.' '.$staff->user->last_name, EventType::DOCUMENT_STAFF_CREATED);
                event(new DocumentStaffEvent($documentDto, $staff->user->email, $staff->user->id, $staff->user->phone_num));
}

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

        DocumentStaff::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Document successfully deleted"
        ], Response::HTTP_OK);
    }
}
