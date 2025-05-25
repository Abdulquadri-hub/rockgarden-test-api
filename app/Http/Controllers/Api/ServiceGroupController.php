<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceGroupCustomCollection;
use App\Http\Resources\ServiceGroupCustomResource;
use App\Http\Resources\ServiceGroupResource;
use App\Http\Services\ServiceGroupClientStaffService;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Http\Controllers\TwilioSMSController;
use App\Models\ServiceGroup;
use App\Models\ServiceGroupClient;
use App\Models\ServiceGroupStaff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ServiceGroupController extends Controller
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
                'message' => ServiceGroupCustomResource::collection(ServiceGroup::all())
            ]
            , Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function indexGroup(Request $request)
    {
        $client_id = $request->get('client_id');
        $staff_id = $request->get('staff_id');
        if (!empty($staff_id) && !empty($client_id)){
            $groupIds = DB::table('service_groups')
                ->join('service_group_clients', 'service_groups.id', '=', 'service_group_clients.group_id')
                ->join('service_group_staff', 'service_groups.id', '=', 'service_group_staff.group_id')
                ->where('service_group_clients.client_id', '=', (int)$client_id)
                ->where('service_group_staff.staff_id', '=', (int)$staff_id)
                ->select('service_groups.id')
                ->groupBy('service_groups.id')
                ->pluck('id');
               
            $res = ServiceGroup::whereIn('id', $groupIds)->get();
        }else if(!empty($staff_id)){
        
            $groupIds = DB::table('service_group_staff')->where('staff_id', $staff_id)->pluck('group_id');
            $res = ServiceGroup::whereIn('id', $groupIds)->get();
        }else if (!empty($client_id)){
               
            $groupIds = DB::table('service_group_clients')->where('client_id', $client_id)->pluck('group_id');
            $res = ServiceGroup::whereIn('id', $groupIds)->get();
        }else{
        //     $data = User::where('active',0)->pluck('id');
        //       $res = new ServiceGroupCustomCollection(ServiceGroup::all());
             
        //       $filtered_data = [];
        // foreach ($res as $value) {
        //     $client_active = 0;
        //     return $value->client;
        //     foreach ($value->data->clients as $value){
        //         return \response()->json([
        //             'success' => true,
        //             'message' => $value->clients
        //         ], Response::HTTP_OK);
        //         return $value;
        //     }
        //     if(isset($value['clients']['user']) ) {
        //         $client_active = $value['clients']["user"]["active"];
                
        //     }
        //     if($client_active == 1 )
        //     array_push($filtered_data, $value);
        // }
            return \response()->json([
                    'success' => true,
                    'message' => new ServiceGroupCustomCollection(ServiceGroup::all())
                ], Response::HTTP_OK);
        }
      
        return \response()->json([
                'success' => true,
                'message' => new ServiceGroupCustomCollection($res)
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
                'message' => ServiceGroupResource::collection(ServiceGroup::paginate((int) $request->get('limit')))
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
            $validator = Validator::make($request->only( 'group_name', 'staff_id', 'client_id'),[
                'group_name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Service Group data.'
                ], 400);
            }

            $clients =  Client::whereIn('id', $request->get('client_id'))->get();

            $staffs =  Employee::whereIn('id', $request->get('staff_id'))->get();

            $group =  ServiceGroup::firstOrCreate(
                [
                    'group_name' => $request->get('group_name'),
                ],
            );

            // Add Staff and CLient to Group

            $service = new ServiceGroupClientStaffService();

            $resStaff = $service->addStaffToGroup($group->id, $request->get('staff_id'));
            $resClient = $service->addClientToGroup($group->id, $request->get('client_id'));
            if(!$resStaff['success']){
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not added to group.'
                ], 400);
            }

            if(!$resClient['success']){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not added to group.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $group->id
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
    public function addStaffToGroups(Request $request)
    {
        try {
            $validator = Validator::make($request->only( 'service_group', 'staff_id'),[
                'service_group' => 'required|array',
                'staff_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Service Group data.'
                ], 400);
            }

            $staff =  Employee::where('id', $request->get('staff_id'))->get();

            $groups =  ServiceGroup::whereIn('group_name', $request->get('service_group'))->get();


            if(empty($staff) || empty($staffs)){
                return response()->json([
                    'success' => false,
                    'message' => '$staff or Groups not found.'
                ], 400);
            }
            $res = [];

            // Add CLient to Groups

            foreach ($groups as $group){
                $res[] = ServiceGroupStaff::firstOrCreate(
                    [
                        'group_id' => $group->id,
                        'staff_id' => $staff->id
                    ],
                );
            }

            return response()->json([
                'success' => true,
                'message' => $res
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
    public function addClientToGroups(Request $request)
    {
        try {
            $validator = Validator::make($request->only( 'service_group', 'client_id'),[
                'service_group' => 'required|array',
                'client_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Service Group data.'
                ], 400);
            }

            $client =  Client::where('id', $request->get('client_id'))->get();

            $groups =  ServiceGroup::whereIn('group_name', $request->get('service_group'))->get();


            if(empty($client) || empty($staffs)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client or Groups not found.'
                ], 400);
            }
            $res = [];

            // Add CLient to Groups

            foreach ($groups as $group){
                $res[] = ServiceGroupClient::firstOrCreate(
                    [
                        'group_id' => $group->id,
                        'client_id' => $client->id
                    ],
                );
            }

            return response()->json([
                'success' => true,
                'message' => $res
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

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->only( 'id', 'group_name', 'staff_id', 'client_id'),[
                'id' => 'required|integer',
                'group_name' => 'required|string',
                'staff_id' => 'required|array',
                'client_id' => 'required|array'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Service Group data.'
                ], 400);
            }

            $clients =  Client::whereIn('id', $request->get('client_id'))->get();

            $staffs =  Employee::whereIn('id', $request->get('staff_id'))->get();


            if(empty($clients) && empty($staffs)){
                return response()->json([
                    'success' => false,
                    'message' => 'Clients and Staff not found.'
                ], 400);
            }
            $group =  ServiceGroup::where('id', $request->get('id'))->first();
            if(empty($group)){
                return response()->json([
                    'success' => false,
                    'message' => 'Group not found.'
                ], 400);
            }
            $group->group_name = $request->get('group_name');
            $group->save();

            // Update Staff and CLient to Group

            $service = new ServiceGroupClientStaffService();

            $resStaff = $service->updateStaffToGroup($group->id, $request->get('staff_id'));
            $resClient = $service->updateClientToGroup($group->id, $request->get('client_id'));
            if(!$resStaff['success']){
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not added to group.'
                ], 400);
            }

            if(!$resClient['success']){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not added to group.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $group->id
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
        $serviceGroup = ServiceGroup::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new ServiceGroupCustomResource($serviceGroup)
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

        ServiceGroup::where('id', $request->get('id'))->delete();
        ServiceGroupClient::where('group_id', $request->get('id'))->delete();
        ServiceGroupStaff::where('group_id', $request->get('id'))->delete();
        return response()->json([
            'success' => true,
            'message' => "Service Group successfully deleted"
        ], Response::HTTP_OK);
    }
}
