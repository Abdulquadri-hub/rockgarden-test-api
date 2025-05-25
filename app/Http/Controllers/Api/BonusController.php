<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBonusRequest;
use App\Http\Requests\UpdateBonusRequest;
use App\Http\Resources\BonusCollection;
use App\Http\Resources\BonusResource;
use App\Models\Bonus;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BonusController extends Controller
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
                'message' => new BonusCollection(Bonus::all())
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
            'message' => Bonus::paginate((int) $request->get('limit'))
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
                    'message' => 'Bonus invalid data.'
                ], 400);
            }

            $designation = Designation::where('designation_name', $request->get('designation_name'))->first();
            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Designation not found.'
                ], 400);
            }

            // Create new Bonus

            $updata['name'] = $request->get('name');
            $updata['amount'] = $request->get('amount');
            $updata['currency'] = $request->get('currency');
            $updata['designation_name'] = $designation->designation_name;

            $bonus = Bonus::where('name', $updata['name'])->where('designation_name', $updata['designation_name'])->first();

            if(!empty($bonus)){
                return response()->json([
                    'success' => false,
                    'message' => 'Record exists.'
                ], 400);
            }

            $matchThese = ['name'=> $updata['name'], 'designation_name' => $updata['designation_name']];
            Bonus::updateOrCreate($matchThese, $updata);

            $bonus = Bonus::where('name', $updata['name'])->where('designation_name', $updata['designation_name'])->first();

            return response()->json([
                'success' => true,
                'message' => $bonus->id
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
        $bonus = Bonus::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new BonusResource($bonus)
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
                    'message' => 'Invalid bonus update information'
                ], Response::HTTP_BAD_REQUEST);
            }

            $designation = Designation::where('designation_name', $request->get('designation_name'))->first();
            if(empty($designation)){
                return response()->json([
                    'success' => false,
                    'message' => 'Designation not found.'
                ], 400);
            }

            $bonus = Bonus::where('id', $request->get('id'))->firstOrFail();
            if(empty($bonus)){
                return response()->json([
                    'success' => false,
                    'message' => 'Bonus not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing Bonus
            $bonus->name = $request->get('name');
            $bonus->amount = $request->get('amount');
            $bonus->currency = $request->get('currency');
            $bonus->designation_name = $designation->designation_name;
            $bonus->save();

            return response()->json([
                'success' => true,
                'message' => $bonus
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

        Bonus::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Bonus successfully deleted"
        ], Response::HTTP_OK);
    }
}
