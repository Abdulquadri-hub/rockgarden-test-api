<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemContactsCollection;
use App\Http\Resources\SystemContactsResource;
use App\Models\SystemContacts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SystemContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request  $request): JsonResponse
    {
        if(empty($request->get('limit'))){
            return \response()->json([
                'success' => true,
                'message' => SystemContacts::orderBy('created_at', 'DESC')->get()
            ], Response::HTTP_OK);
        }
        return \response()->json([
            'success' => true,
            'message' => SystemContacts::orderBy('created_at', 'DESC')->paginate((int) $request->get('limit'))
        ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexPaged(Request $request)
    {
        if(empty($request->get('limit'))){
            return \response()->json([
                'success' => true,
                'message' => SystemContacts::orderBy('created_at', 'DESC')->get()
            ], Response::HTTP_OK);
        }
        return \response()->json([
            'success' => true,
            'message' => SystemContacts::orderBy('created_at', 'DESC')->paginate((int) $request->get('limit'))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $systemContacts = new SystemContacts();

            $systemContacts->name = $request->get('name');
            $systemContacts->email = $request->get('email');
            $systemContacts->phone = $request->get('phone');
            $systemContacts->is_default = $request->get('is_default');

            $systemContacts->save();
            return response()->json([
                'success' => true,
                'message' => $systemContacts->id
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
        $systemContacts = SystemContacts::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new SystemContactsResource($systemContacts)
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
                'name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid systems contact update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $systemContacts = SystemContacts::where('id', $request->get('id'))->firstOrFail();
            if(empty($systemContacts)){
                return response()->json([
                    'success' => false,
                    'message' => 'System Contact not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Replace existing SystemContacts
            $systemContacts->name = $request->get('name');
            $systemContacts->email = $request->get('email');
            $systemContacts->phone = $request->get('phone');
            $systemContacts->is_default = $request->get('is_default');
            $systemContacts->save();

            return response()->json([
                'success' => true,
                'message' => $systemContacts
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

        SystemContacts::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "System Contact successfully deleted"
        ], Response::HTTP_OK);
    }
}
