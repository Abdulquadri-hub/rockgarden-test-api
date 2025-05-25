<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllowanceCollection;
use App\Http\Resources\AllowanceResource;
use App\Models\Allowance;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class AllowanceController extends Controller
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
                'message' => new AllowanceCollection(Allowance::all())
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
            'message' => Allowance::paginate((int) $request->get('limit'))
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
                    'message' => 'Allowance invalid data.'
                ], 400);
            }
            $designation = Designation::where('designation_name', $request->get('designation_name'))->first();
            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Designation not found.'
                ], 400);
            }
            // Create new Allowance
            $updata['name'] = $request->get('name');
            $updata['amount'] = $request->get('amount');
            $updata['currency'] = $request->get('currency');
            $updata['designation_name'] = $designation->designation_name;

            $allowance = Allowance::where('name', $updata['name'])->where('designation_name', $updata['designation_name'])->first();

            if(!empty($allowance)){
                return response()->json([
                    'success' => false,
                    'message' => 'Record exists.'
                ], 400);
            }

            $matchThese = ['name'=> $updata['name'], 'designation_name' => $updata['designation_name']];
            Allowance::updateOrCreate($matchThese, $updata);

            $allowance = Allowance::where('name', $updata['name'])->where('designation_name', $updata['designation_name'])->first();
            return response()->json([
                'success' => true,
                'message' => $allowance->id
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
        $allowance = Allowance::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new AllowanceResource($allowance)
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
                    'message' => 'Invalid allowance update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $designation = Designation::where('designation_name', $request->get('designation_name'))->first();
            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Designation not found.'
                ], 400);
            }

            $allowance = Allowance::where('id', $request->get('id'))->firstOrFail();
            if(empty($allowance)){
                return response()->json([
                    'success' => false,
                    'message' => 'Allowance not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Replace existing Allowance
            $allowance->name = $request->get('name');
            $allowance->amount = $request->get('amount');
            $allowance->currency = $request->get('currency');
            $allowance->designation_name = $designation->designation_name;
            $allowance->save();

            return response()->json([
                'success' => true,
                'message' => $allowance
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

        Allowance::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Allowance successfully deleted"
        ], Response::HTTP_OK);
    }
}
