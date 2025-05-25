<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ContactController extends Controller
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
                'message' => Contact::all()
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
            'message' => Contact::paginate((int) $request->get('limit'))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only('phone'),[
                'phone' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact invalid data.'
                ], 400);
            }
            // Create new Contact
            $contact = new Contact();
            $contact->name = $request->get('name');
            $contact->phone = $request->get('phone');
            $contact->email = $request->get('email');
            $contact->address = $request->get('address');
            $contact->save();
            return response()->json([
                'success' => true,
                'message' => $contact->id
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
        $contact = Contact::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $contact
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
            $validator = Validator::make($request->only('id', 'phone'),[
                'id' => 'required|integer',
                'phone' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid contact update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $contact = Contact::where('id', $request->get('id'))->firstOrFail();
            if(empty($contact)){
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Contact
            $contact->name = $request->get('name');
            $contact->phone = $request->get('phone');
            $contact->email = $request->get('email');
            $contact->address = $request->get('address');
            $contact->save();

            return response()->json([
                'success' => true,
                'message' => $contact
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

        Contact::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Contact successfully deleted"
        ], Response::HTTP_OK);
    }
}
