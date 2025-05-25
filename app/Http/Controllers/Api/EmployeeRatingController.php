<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRatingRequest;
use App\Http\Requests\UpdateEmployeeRatingRequest;
use App\Http\Resources\EmployeeRatingCollection;
use App\Models\Employee;
use App\Models\EmployeeRating;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EmployeeRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $client_id = $request->get('client_id');
        $staff_id = $request->get('staff_id');
        $limit = $request->get('limit');
         $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

        if (!empty($limit)){
            if (!empty($client_id) && !empty($staff_id)){
                $res = EmployeeRating::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->where('staff_id', $staff_id)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }
            elseif(!empty($staff_id)){
                $res = EmployeeRating::where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }
            elseif (!empty($client_id)){
                $res = EmployeeRating::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                return \response()->json([
                    'success' => true,
                    'message' => new EmployeeRatingCollection(EmployeeRating::orderBy('updated_at', 'DESC')->paginate($limit))
                ], Response::HTTP_OK);
            }
        }
        else
        {
            if (!empty($client_id) && !empty($staff_id)){
                $res = EmployeeRating::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->where('staff_id', $staff_id)
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            }
            elseif(!empty($staff_id)){
                $res = EmployeeRating::where('staff_id', $staff_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            }
            elseif (!empty($client_id)){
                $res = EmployeeRating::where('client_id', $client_id)->whereBetween('created_at', [$from_date, $to_date])
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            }else{
                return \response()->json([
                    'success' => true,
                    'message' => new EmployeeRatingCollection(EmployeeRating::orderBy('updated_at', 'DESC')->get())
                ], Response::HTTP_OK);
            }
        }
        if (!empty($from_date) && !empty($to_date)) {
        $res = EmployeeRating::whereBetween('updated_at', [$from_date, $to_date])->get();
    }

        return \response()->json([
            'success' => true,
            'message' => new EmployeeRatingCollection($res)
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
                'comment' => 'required|string',
                'staff_id'=> 'required|integer',
                'client_id'=> 'required|integer',
                'rating'=> 'required'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found.'
                ], Response::HTTP_CONFLICT);
            }

            if(empty($request->get('reviewer_id'))){
                $reviewer =  User::where('id', Auth::user()->id)->first();
            }else{
                $reviewer =  User::where('id', $request->get('reviewer_id'))->first();
            }


            $staff =  Employee::where('id', $request->get('staff_id'))->first();
            if(empty($staff)){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Employee Rating.'
                ], 200);
            }
            // Create new EmployeeRating
            DB::beginTransaction();
            $rating_data = [];
            $rating_data['staff_id'] = $request->get('staff_id');
            $rating_data['comment'] = $request->get('comment');
            $rating_data['rating'] = $request->get('rating');
            $rating_data['client_id'] = $request->get('client_id');
            $rating_data['reviewer_id'] = $reviewer->id;
            $rating_data['reviewer_name'] = $reviewer->first_name.' '.$reviewer->last_name;

            $matchThese = ['staff_id'=>  $rating_data['staff_id'],'reviewer_id' => $rating_data['reviewer_id']];
            EmployeeRating::updateOrCreate($matchThese, $rating_data);

            $countRating =  EmployeeRating::where('staff_id', $rating_data['staff_id'])->count();
            $totalRate = EmployeeRating::where('staff_id', $rating_data['staff_id'])->sum('rating');

            if($countRating <= 0){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error calculating employee rate.'
                ], Response::HTTP_CONFLICT);
            }

            $staff->average_rating = $totalRate/$countRating;
            $staff->total_ratings = $countRating;

            $staff->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Employee rating successfully submitted.",
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            DB::rollBack();
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
        $EmployeeRating = EmployeeRating::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $EmployeeRating
            ]
            ,
            Response::HTTP_OK);
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

        EmployeeRating::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "EmployeeRating successfully deleted"
        ], Response::HTTP_OK);
    }
}
