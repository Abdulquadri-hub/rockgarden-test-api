<?php

namespace App\Http\Controllers\Api;

use App\Dto\EventType;
use App\Dto\StaffAssignmentDto;
use App\Events\StaffAssignmentEvent;
use App\Mail\StaffAssignmentUpdatedMail;
use App\Mail\StaffAssignmentCreatedMail;
use App\Models\NotificationSettings;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffAssignmentAllCollection;
use App\Http\Resources\StaffAssignmentAllResource;
use App\Http\Resources\StaffAssignmentCollection;
use App\Http\Resources\StaffAssignmentResource;
use App\Models\Client;
use App\Models\Employee;
use App\Models\StaffAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class StaffAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // $user = User::where('active',1)->pluck('id');
        $staff_id = $request->get('staff_id');
        $client_id =$request->get('client_id');
        $limit = $request->get('limit');
        // $staff_active = Employee::whereIn('user_id',$user)->get();
        // $staff_active = Employee::whereIn('user_id',$user)->get();
    //   return response()->json ($staff_active = Employee::whereNotIn('user_id',$user)->get());
        // return response()->json( $user = User::where('active',0)->pluck('id'));
         
        $res = [];
        if (empty($limit)){
            $limit = 1000;
        }

       if(!empty($staff_id) && !empty($client_id)){
            $res = new StaffAssignmentAllCollection(StaffAssignment::where('staff_id', $staff_id)->where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit));
        }else if(!empty($staff_id)){
            $res = new StaffAssignmentAllCollection(StaffAssignment::where('staff_id', $staff_id)->orderBy('updated_at', 'DESC')->paginate($limit));
        }else if(!empty($client_id)){
            $res = new StaffAssignmentAllCollection(StaffAssignment::where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit));
        }else{
            $res = new StaffAssignmentCollection(StaffAssignment::orderBy('updated_at', 'DESC')->paginate($limit));
        }
        $filtered_data = [];
        foreach ($res as $value) {
            $client_active = 0;
            $staff_active = 0;
            if(isset($value['client']) && isset($value['client']['user']) && isset($value['staff']) && isset($value['staff']['user'])) {
                $client_active = $value['client']["user"]["active"];
                $staff_active = $value['staff']["user"]["active"];
            }
            if($client_active == 1 && $staff_active == 1)
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
                'message' =>  new StaffAssignmentCollection(StaffAssignment::paginate((int) $request->get('limit')))
            ]
            , Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // return $requ/st;
        try {
            $validator = Validator::make($request->only('staff_id', 'client_id'),[
                'staff_id' => 'required|integer',
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Staff Assignment data.'
                ], 400);
            }
            $client =  Client::where('id', $request->get('client_id'))->first();
            $staff =  Employee::where('id', $request->get('staff_id'))->first();

            if(empty($client) || empty($staff)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client or Staff  not found.'
                ], 400);
            }

            // Create new Staff Assignment
            $updata['client_id'] = $client->id;
            $updata['staff_id'] = $staff->id;

            $matchThese = ['client_id'=> $updata['client_id'],'staff_id'=> $updata['staff_id']];

            StaffAssignment::updateOrCreate($matchThese, $updata);

            $staffAssignment = StaffAssignment::where('staff_id', $staff->id)->where('client_id', $client->id)->first();

            if(!empty($staffAssignment)){
                  $name = $client->user->first_name . " " . $client->user->last_name;
                
                    $emailNotification = NotificationSettings::where('trigger_name', 'STAFF_ASSIGNMENT_CREATED')->where('send_email', 1)->first();
                    $fullname=$staff->first_name.' '.$staff->last_name;
                    if ($emailNotification) {
                        $mail = new StaffAssignmentCreatedMail($fullname,$name);
                       
                        Helper::sendEmail($staff->email, $mail);
                    }

        
                $smsNotification = NotificationSettings::where('trigger_name', 'STAFF_ASSIGNMENT_CREATED')->where('send_sms', 1)->first();
                if ($smsNotification) {
                    $message = TwilioSMSController::staffAssignmentCreatedMessage($fullname,$name);
                    Helper::sendSms($staff->phone_num, $message);
                }
                        // $staffAssignmentDto = new StaffAssignmentDto($client->user->first_name.' '.$client->user->last_name, $staff->user->first_name.' '.$staff->user->last_name, EventType::STAFF_ASSIGNMENT_CREATED);
                        // event(new StaffAssignmentEvent($staffAssignmentDto, $staff->user->email, $staff->user->id, $staff->user->phone_num));
                    }

            return response()->json([
                'success' => true,
                'message' => $staffAssignment->id
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
    public function storeAll(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'datas' => 'required|array',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Staff Assignment data.'
                ], 400);
            }
            $datas = $request->get('datas');

            $staffAssignments = [];

            if(!empty($datas)){
                foreach ($datas as $data){
                    $client =  Client::where('id', $data['client_id'])->first();
                    $staff =  Employee::where('id', $data['staff_id'])->first();
                    if(!empty($client) && !empty($staff)){
                       $staffAssignments[] = [
                           'client_id'=> $data['client_id'],
                           'staff_id'=> $data['staff_id'],
                           'client' => $client,
                           'staff' => $staff
                       ];
                    }
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'No Staff Assignment data.'
                ], 400);
            }

            if(!empty($staffAssignments)){
                foreach ($staffAssignments as $assignment){
                    // Create new Staff Assignment
                    $updata['client_id'] = $assignment['client_id'];
                    $updata['staff_id'] = $assignment['staff_id'];

                    $matchThese = ['client_id'=> $updata['client_id'],'staff_id'=> $updata['staff_id']];
                    StaffAssignment::updateOrCreate($matchThese, $updata);

                    if(!empty(!$client) && !empty($staff)){
                             $name = $client->user->first_name . " " . $client->user->last_name;
                       
                        
                        $staffAssignmentDto = new StaffAssignmentDto($client->user->first_name.' '.$client->user->last_name, $staff->user->first_name.' '.$staff->user->last_name, EventType::STAFF_ASSIGNMENT_CREATED);
                        event(new StaffAssignmentEvent($staffAssignmentDto, $staff->user->email, $staff->user->id, $staff->user->phone_num));
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Staff Assignment successful'
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
        $staffAssignment = StaffAssignment::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new StaffAssignmentAllResource($staffAssignment)
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

        StaffAssignment::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Staff Assignment successfully deleted"
        ], Response::HTTP_OK);
    }
}
