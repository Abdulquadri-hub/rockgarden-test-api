<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KeyContactCollection;
use App\Models\Client;
use App\Models\KeyContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class KeyContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $client_id =  $request->get('client_id');
        if(!empty($client_id)){
            return \response()->json([
                    'success' => true,
                    'message' => new KeyContactCollection(KeyContact::where('client_id', $client_id)->get())
                ]
                , Response::HTTP_OK);
        }
        return \response()->json([
                'success' => true,
                'message' => new KeyContactCollection(KeyContact::orderBy('client_id', 'ASC')->get())
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
        $client_id =  $request->get('client_id');
        if(!empty($client_id)){
            return \response()->json([
                    'success' => true,
                    'message' => new KeyContactCollection(KeyContact::where('client_id', $client_id)->orderBy('client_id', 'ASC')->paginate((int) $request->get('limit')))
                ]
                , Response::HTTP_OK);
        }

        return \response()->json([
                'success' => true,
                'message' => new KeyContactCollection(KeyContact::orderBy('client_id', 'ASC')->paginate((int) $request->get('limit')))
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
            $validator = Validator::make($request->only('client_id', 'fullname', 'relationship', 'home_address', 'phone_number', 'is_primary'),
                [
                    'fullname' => 'required|string',
                    'relationship' => 'required|string',
                    'home_address' => 'required|string',
                    'phone_number' => 'required|string',
                    'is_primary' => 'required|boolean',
                    'client_id' => 'required|integer'
                ]
            );

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Key Contact data.'
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

            // Create new KeyContact
            $keyContact = new KeyContact();
            $keyContact->fullname = $request->get('fullname');
            $keyContact->relationship = $request->get('relationship');
            $keyContact->home_address = $request->get('home_address');
            $keyContact->email_address = $request->get('email_address');
            $keyContact->phone_number =  $request->get('phone_number');
            $keyContact->is_primary = $request->get('is_primary');
            $keyContact->client_id = $request->get('client_id');
            $keyContact->save();
            return response()->json([
                'success' => true,
                'message' => $keyContact->id
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
        $keyContact = KeyContact::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $keyContact
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
            $validator = Validator::make($request->only( 'id', 'client_id', 'fullname', 'relationship', 'home_address', 'phone_number', 'is_primary'),
                [
                    'id' => 'required|integer',
                    'fullname' => 'required|string',
                    'relationship' => 'required|string',
                    'home_address' => 'required|string',
                    'phone_number' => 'required|string',
                    'is_primary' => 'required|boolean',
                    'client_id' => 'required|integer'
                ]
            );


            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Key Contact data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new KeyContact name
            $keyContact = KeyContact::where('id', $request->get('id'))->first();
            if(empty($keyContact)){
                return response()->json([
                    'success' => false,
                    'message' => 'Key Contact not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $keyContact->fullname = $request->get('fullname');
            $keyContact->relationship = $request->get('relationship');
            $keyContact->home_address = $request->get('home_address');
            $keyContact->email_address = $request->get('email_address');
            $keyContact->phone_number =  $request->get('phone_number');
            $keyContact->is_primary = $request->get('is_primary');
            $keyContact->client_id = $request->get('client_id');
            $keyContact->save();
            return response()->json([
                'success' => true,
                'message' => $keyContact
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

        KeyContact::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Key Contact successfully deleted"
        ], Response::HTTP_OK);
    }
}
