<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\NotificationSettings;
use App\Mail\NewAccountByAdminMail;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
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
                'message' => UserResource::collection(User::orderBy('created_at','DESC')->get())
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
                'message' => new UserCollection(User::orderBy('created_at','DESC')->paginate((int) $request->get('limit')))
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
            DB::beginTransaction();
            $userService = new UserService();
            $latestUser = User::orderBy('created_at','DESC')->first();
            // $request['email'] = !empty($request->get('email')) ? $request->get('email')  : '000'.($latestUser === null ? 1 : ($latestUser->id + 1));
               if (!empty($request->get('email'))) {
            $requestEmail = $request->get('email');
        } else {
            // Generate a new email address with the "@rockgardenehr.com" domain
            $requestEmail = '000' . ($latestUser === null ? 1 : ($latestUser->id + 1)) . '@rockgardenehr.com';
        }

        // Set the email in the request
        $request->merge(['email' => $requestEmail]);

            $response = $userService->register($request);
            if(!$response['success']){
                DB::rollBack();
                return response()->json($response, Response::HTTP_BAD_REQUEST);
            }
            
            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        }catch (Exception $e){
            DB::rollBack();
            Log::debug($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $user = User::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new UserResource($user)
            ]
            , Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $userService = new UserService();
        $response = $userService->update($request);
        if($response['success']){
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }
        return response()->json($response, Response::HTTP_OK);
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

        User::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "User successfully deleted"
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function blockUser(Request  $request){
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
                        'message' => 'User id not found.'
                    ], Response::HTTP_BAD_REQUEST
                );
            }


            User::where('id','=',$request->id)->update(
                [
                    'active' => 0
                ]
            );

            return \response()->json(
                [
                    'success' => true,
                    'message' => "User successfully bloked."
                ], Response::HTTP_OK
            );
        }catch (Exception $e){
            Log::error($e->getMessage());
            return \response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], Response::HTTP_OK
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function birthDateList(Request $request){
        $date =  new \DateTime($request->get('date'));
        $limit  = $request->get('limit');
        $limit = $limit> 0 ? $limit : 5;
        $users = User::whereMonth('date_of_birth', date_format($date, "m"))
            ->whereDay('date_of_birth', date_format($date, "d"))
            ->paginate($limit);
        return \response()->json(
            [
                'success' => true,
                'message' => new UserCollection($users)
            ],
            Response::HTTP_OK
        );
    }
}
