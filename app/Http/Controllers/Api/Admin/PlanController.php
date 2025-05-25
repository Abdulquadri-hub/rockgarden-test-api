<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class PlanController extends Controller
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
                'message' => Service::all()
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
                'message' => Service::paginate((int) $request->get('limit'))
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
            $validator = Validator::make($request->only('plan_name', 'price_from', 'price_to', 'currency'),[
                'plan_name' => 'required|string',
                'price_from' => 'required',
                'price_to' => 'required',
                'currency' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Plan data.'
                ], 400);
            }

            // Create new Service Plan
            $service = new Service();
            $service->plan_id = $request->get('plan_id');
            $service->plan_name = $request->get('plan_name');
            $service->description = $request->get('description');
            $service->price_from = (float)$request->get('price_from');
            $service->price_to = (float)$request->get('price_to');
            $service->currency = $request->get('currency');
            $service->save();
            return response()->json([
                'success' => true,
                'message' => $service->id
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
        $service = Service::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $service
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
            $validator = Validator::make($request->only('id', 'plan_name', 'price_from', 'price_to', 'currency'),[
                'id' => 'required|integer',
                'plan_name' => 'required|string',
                'price_from' => 'required',
                'price_to' => 'required',
                'currency' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Plan data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new Service Plan
            $service = Service::where('id', $request->get('id'))->firstOrFail();
            if(empty($service)){
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $service->plan_id = $request->get('plan_id');
            $service->plan_name = $request->get('plan_name');
            $service->description = $request->get('description');
            $service->price_from = (float)$request->get('price_from');
            $service->price_to = (float)$request->get('price_to');
            $service->currency = $request->get('currency');
            $service->save();
            return response()->json([
                'success' => true,
                'message' => $service
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

        Service::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Plan successfully deleted"
        ], Response::HTTP_OK);
    }
}
