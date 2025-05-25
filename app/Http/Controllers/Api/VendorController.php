<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorCollection;
use App\Http\Resources\VendorResource;
use App\Http\Services\UserService;
use App\Models\Vendor;
use App\Models\VendorContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class VendorController extends Controller
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
                'message' => new VendorCollection(Vendor::all())
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
                'message' => new VendorCollection(Vendor::paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Create new Vendor
            $vendor = new Vendor();
            $vendor->company_name =  $request->get('company_name');
            $vendor->vendor_email = $request->get('vendor_email');
            $vendor->vendor_phone =  $request->get('vendor_phone');
            $vendor->vendor_web_site =  $request->get('vendor_web_site');
            $vendor->remarks = $request->get('remarks');
            $vendor->contact_person = $request->get('contact_person');
            $vendor->vendor_address = $request->get('vendor_address');
            $vendor->save();

            return response()->json([
                'success' => true,
                'message' => $vendor->id
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
        $vendor = Vendor::where('id', $request->get('id'))->first();
        return \response()->json([
            'success' => true,
            'message' => new VendorResource($vendor)
        ], Response::HTTP_OK);
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
                    'message' => 'Invalid Vendor data.'
                ], 400);
            }

            // Create new Vendor name
            $vendor = Vendor::where('id', $request->get('id'))->first();
            if(empty($vendor)){
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $vendor->company_name =  $request->get('company_name');
            $vendor->vendor_email = $request->get('vendor_email');
            $vendor->vendor_phone =  $request->get('vendor_phone');
            $vendor->vendor_web_site =  $request->get('vendor_web_site');
            $vendor->remarks = $request->get('remarks');
            $vendor->contact_person = $request->get('contact_person');
            $vendor->vendor_address = $request->get('vendor_address');
            $vendor->save();

            return response()->json([
                'success' => true,
                'message' => new VendorResource($vendor)
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

        Vendor::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Vendor successfully deleted"
        ], Response::HTTP_OK);
    }
}
