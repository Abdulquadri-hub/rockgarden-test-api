<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InAppNotificationsCollection;
use App\Http\Resources\InAppNotificationsResource;
use App\Models\Designation;
use App\Models\InAppNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InAppNotificationsController extends Controller
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
                'message' => new InAppNotificationsCollection(InAppNotifications::all())
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
            'message' => InAppNotifications::paginate((int) $request->get('limit'))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            if (empty($inAppNotifications)){
                // Create new InAppNotifications
                $inAppNotifications = new InAppNotifications();
            }

            $inAppNotifications->title = $request->get('title');
            $inAppNotifications->message = $request->get('message');
            $inAppNotifications->owner_ids = (array)$request->get('owner_ids');
            $inAppNotifications->save();
            return response()->json([
                'success' => true,
                'message' => $inAppNotifications->id
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
        $inAppNotifications = InAppNotifications::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new InAppNotificationsResource($inAppNotifications)
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
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid inAppNotifications update information'
                ], Response::HTTP_BAD_REQUEST);
            }


            $inAppNotifications = InAppNotifications::where('id', $request->get('id'))->firstOrFail();
            if(empty($inAppNotifications)){
                return response()->json([
                    'success' => false,
                    'message' => 'InApp notification not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Replace existing InAppNotifications
            $inAppNotifications->title = $request->get('title');
            $inAppNotifications->message = $request->get('message');
            $inAppNotifications->owner_ids = (array)$request->get('owner_ids');
            $inAppNotifications->save();

            return response()->json([
                'success' => true,
                'message' => $inAppNotifications
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

        InAppNotifications::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "InAppNotifications successfully deleted"
        ], Response::HTTP_OK);
    }
}
