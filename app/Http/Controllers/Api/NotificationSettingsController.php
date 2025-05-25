<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationSettingCollection;
use App\Http\Resources\NotificationSettingResource;
use App\Models\NotificationSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class NotificationSettingsController extends Controller
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
                'message' => new NotificationSettingCollection(NotificationSettings::all())
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
            'message' => NotificationSettings::paginate((int) $request->get('limit'))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeAll(Request $request): JsonResponse
    {
        try {
            if(!empty($request->get('data'))){
                $datas = $request->get('data');
                $updates = [];
                foreach ($datas as $data){
                    $entry['trigger_name'] = $data['trigger_name'];
                    $entry['send_sms'] = $data['send_sms'];
                    $entry['send_email'] = $data['send_email'];
                    $entry['send_inapp'] = $data['send_inapp'];
                    $entry['system_contact_id'] = $data['system_contact_id'];
                    $updates[] = $entry;
                }

                if(!empty($updates) && count($updates) > 0){
                    foreach ($updates as $updata)
                    {
                        $matchThese = ['trigger_name'=> $updata['trigger_name']];
                        NotificationSettings::updateOrCreate($matchThese, $updata);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Setting successfully processed."
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
    public function store(Request $request)
    {
        try {

            $notificationSettings = NotificationSettings::where('trigger_name', $request->get('trigger_name'))->first();

            if (empty($notificationSettings)){
                /// Create new NotificationSettings
                $notificationSettings = new NotificationSettings();
            }
            $notificationSettings->trigger_name = $request->get('trigger_name');
            $notificationSettings->send_sms = $request->get('send_sms');
            $notificationSettings->send_email = $request->get('send_email');
            $notificationSettings->send_inapp = $request->get('send_inapp');
            $notificationSettings->system_contact_id = $request->get('system_contact_id');
            $notificationSettings->save();
            return response()->json([
                'success' => true,
                'message' => $notificationSettings->id
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
        $notificationSettings = NotificationSettings::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new NotificationSettingResource($notificationSettings)
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
            $validator = Validator::make($request->all(),[
                'id' => 'required|integer',
                'trigger_name' => 'required|unique:notification_settings'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid notification settings update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $notificationSettings = NotificationSettings::where('id', $request->get('id'))->firstOrFail();
            if(empty($notificationSettings)){
                return response()->json([
                    'success' => false,
                    'message' => 'Notification Settings not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing NotificationSettings
            $notificationSettings->trigger_name = $request->get('trigger_name');
            $notificationSettings->send_sms = $request->get('send_sms');
            $notificationSettings->send_email = $request->get('send_email');
            $notificationSettings->send_inapp = $request->get('send_inapp');
            $notificationSettings->system_contact_id = $request->get('system_contact_id');
            $notificationSettings->save();

            return response()->json([
                'success' => true,
                'message' => $notificationSettings
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

        NotificationSettings::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "NotificationSettings successfully deleted"
        ], Response::HTTP_OK);
    }
}
