<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaxCollection;
use App\Http\Resources\TaxResource;
use App\Models\Designation;
use App\Models\Tax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class TaxController extends Controller
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
                'message' => new TaxCollection(Tax::all())
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
            'message' => Tax::paginate((int) $request->get('limit'))
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid valid data.'
                ], 400);
            }

            // Create new Tax

            $updata['name'] = $request->get('name');
            $updata['amount'] = $request->get('amount');
            $updata['currency'] = $request->get('currency');
            $updata['is_fixed'] = $request->get('is_fixed');
            $updata['percentage'] = $request->get('percentage');

            $tax = Tax::where('name', $updata['name'])->first();

            if(!empty($tax)){
                return response()->json([
                    'success' => false,
                    'message' => 'Record exists.'
                ], 400);
            }

            $matchThese = ['name'=> $updata['name']];
            Tax::updateOrCreate($matchThese, $updata);

            $tax = Tax::where('name', $updata['name'])->first();

            return response()->json([
                'success' => true,
                'message' => $tax->id
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
        $tax = Tax::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new TaxResource($tax)
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
                    'message' => 'Invalid tax update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $tax = Tax::where('id', $request->get('id'))->firstOrFail();
            if(empty($tax)){
                return response()->json([
                    'success' => false,
                    'message' => 'Tax not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Tax
            $tax->name = $request->get('name');
            $tax->amount = $request->get('amount');
            $tax->currency = $request->get('currency');
            $tax->is_fixed = $request->get('is_fixed');
            $tax->percentage = $request->get('percentage');
            $tax->save();

            return response()->json([
                'success' => true,
                'message' => $tax
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

        Tax::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Tax successfully deleted"
        ], Response::HTTP_OK);
    }
}
