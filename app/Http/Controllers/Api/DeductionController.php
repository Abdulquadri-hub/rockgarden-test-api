<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeductionCollection;
use App\Http\Resources\DeductionResource;
use App\Models\Deduction;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class DeductionController extends Controller
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
                'message' => new DeductionCollection(Deduction::all())
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
            'message' => Deduction::paginate((int) $request->get('limit'))
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
                    'message' => 'Deduction invalid data.'
                ], 400);
            }

            $designation = Designation::where('designation_name', $request->get('designation_name'))->first();
            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Designation not found.'
                ], 400);
            }

            // Create new Deduction
            $updata['name'] = $request->get('name');
            $updata['amount'] = $request->get('amount');
            $updata['currency'] = $request->get('currency');
            $updata['designation_name'] = $designation->designation_name;

            $deduction = Deduction::where('name', $updata['name'])->where('designation_name', $updata['designation_name'])->first();

            if(!empty($deduction)){
                return response()->json([
                    'success' => false,
                    'message' => 'Record exists.'
                ], 400);
            }

            $matchThese = ['name'=> $updata['name'], 'designation_name' => $updata['designation_name']];
            Deduction::updateOrCreate($matchThese, $updata);

            $deduction = Deduction::where('name', $updata['name'])->where('designation_name', $updata['designation_name'])->first();

            return response()->json([
                'success' => true,
                'message' => $deduction->id
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
        $deduction = Deduction::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new DeductionResource($deduction)
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
                    'message' => 'Invalid deduction update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $designation = Designation::where('designation_name', $request->get('designation_name'))->first();
            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Designation not found.'
                ], 400);
            }

            $deduction = Deduction::where('id', $request->get('id'))->firstOrFail();
            if(empty($deduction)){
                return response()->json([
                    'success' => false,
                    'message' => 'Deduction not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Deduction
            $deduction->name = $request->get('name');
            $deduction->amount = $request->get('amount');
            $deduction->currency = $request->get('currency');
            $deduction->designation_name = $designation->designation_name;
            $deduction->save();

            return response()->json([
                'success' => true,
                'message' => $deduction
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

        Deduction::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Deduction successfully deleted"
        ], Response::HTTP_OK);
    }
}
