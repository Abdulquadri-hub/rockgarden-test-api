<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceCollection;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Rota;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit');

        if(!empty($limit)){
            return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::orderBy('updated_at', 'DESC')->paginate($limit)->get())
                ]
                , Response::HTTP_OK);
        }
        return \response()->json([
                'success' => true,
                'message' => new AttendanceCollection(Attendance::orderBy('updated_at', 'DESC')->paginate(50)->get())
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
                'message' => new AttendanceCollection(Attendance::paginate((int) $request->get('limit')))
            ]
            , Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function indexDateBetween(Request  $request)
    {
        $from = date($request->get('date_from'));
        $to = date($request->get('date_to'));
        $limit = $request->get('limit');
        $category = $request->get('category');

        $employee = Employee::where('user_id', Auth::user()->id)->first();

        if(!empty($category)){
            if(empty($from) || empty($to)){
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::where('staff_id', $employee->id)->where('category', $category)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::where('staff_id', $employee->id)->where('category', $category)->paginate($limit))
                ], Response::HTTP_OK);
            }else{
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                            ->where('staff_id', $employee->id)->where('category', $category)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                        ->where('staff_id', $employee->id)->where('category', $category)->paginate($limit))
                ], Response::HTTP_OK);
            }
        }else{
            if(empty($from) || empty($to)){
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::where('staff_id', $employee->id)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::where('staff_id', $employee->id)->paginate($limit))
                ], Response::HTTP_OK);
            }else{
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                            ->where('staff_id', $employee->id)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                        ->where('staff_id', $employee->id)->paginate($limit))
                ], Response::HTTP_OK);
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function indexDateBetweenNoAuth(Request  $request)
    {
        $from = date($request->get('date_from'));
        $to = date($request->get('date_to'));
        $limit = $request->get('limit');
        $staff_id = $request->get('staff_id');
        $category = $request->get('category');

        if (!empty($staff_id) && !empty($category)){
            if(empty($from) || empty($to)){
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::where('staff_id', $staff_id)->where('category', $category)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::where('staff_id', $staff_id)->where('category', $category)->paginate($limit))
                ], Response::HTTP_OK);
            }else{
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                            ->where('staff_id', $staff_id)->where('category', $category)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                        ->where('staff_id', $staff_id)->where('category', $category)->paginate($limit))
                ], Response::HTTP_OK);
            }
        }else if(!empty($category)){
            if(empty($from) || empty($to)){
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::where('category', $category)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::where('category', $category)->paginate($limit))
                ], Response::HTTP_OK);
            }else{
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                            ->where('category', $category)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                        ->where('category', $category)->paginate($limit))
                ], Response::HTTP_OK);
            }
        }else if(!empty($staff_id)){
            if(empty($from) || empty($to)){
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::where('staff_id', $staff_id)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::where('staff_id', $staff_id)->paginate($limit))
                ], Response::HTTP_OK);
            }else{
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                            ->where('staff_id', $staff_id)->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])
                        ->where('staff_id', $staff_id)->paginate($limit))
                ], Response::HTTP_OK);
            }
        }else{
            if(empty($from) || empty($to)){
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::orderBy('created_at', 'DESC')->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::orderBy('created_at', 'DESC')->paginate($limit))
                ], Response::HTTP_OK);
            }else{
                if (empty($limit)){
                    return \response()->json([
                        'success' => true,
                        'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])->get())
                    ], Response::HTTP_OK);
                }
                return \response()->json([
                    'success' => true,
                    'message' => new AttendanceCollection(Attendance::whereBetween('created_at', [$from, $to])->paginate($limit))
                ], Response::HTTP_OK);
            }
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkin(Request $request)
    {
        try {
            $validator = Validator::make($request->only('longitude_checkin', 'latitude_checkin', 'device'),[
                'device' => 'required|string',
                'longitude_checkin' => 'required|numeric',
                'latitude_checkin' => 'required|numeric'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Attendance data.'
                ], 400);
            }

            // Create new Attendance
            $attendance = new Attendance();

            $staff = Employee::where('user_id', Auth::user()->id)->first();
            if(empty($staff)){
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found.'
                ], 404);
            }
            $attendance->staff_id = $staff->id;
            $attendance->device_checkin = $request->get('device');
            $attendance->longitude_checkin = $request->get('longitude_checkin');
            $attendance->latitude_checkin = $request->get('latitude_checkin');
            $attendance->time_checkin = new \DateTime('now');
            $attendance->category = $request->get('category');
            $attendance->save();
            $now = date('Y-m-d');
            $rota =  Rota::where('staff_id', $attendance->staff_id)
                ->where('rota_date', $now)->first();

            if(!empty($rota) && $rota->is_present != true){
                $rota->is_present = true;
                $rota->save();
            }


            return response()->json([
                'success' => true,
                'message' => $attendance->id
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => true,
                    'message' => $e->getMessage()
                ], Response::HTTP_CONFLICT
            );
        }
    }
      public function updateAttendanceRecords()
    {
        set_time_limit(0);
         $attendances = Attendance::all(); // Fetch all attendance records
    
        foreach ($attendances as $attendance) {
            $now = date('Y-m-d', strtotime($attendance->time_checkin));
    
             $rota = Rota::where('staff_id', $attendance->staff_id)
                ->where('rota_date', $now)
                ->first();
    
            if (!empty($rota) && !$rota->is_present) {
                $rota->is_present = true;
                $rota->save();
            }
        }
        return response()->json(
            [
                'success' => false,
                'message' => 'Rota Records are updated'
            ], Response::HTTP_OK
        );
    } 


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $attendance = Attendance::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $attendance
            ]
            , Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkout(Request $request)
    {
        try {
            // validate request data
            $validator = Validator::make($request->only('id', 'longitude_checkout', 'latitude_checkout', 'device'),[
                'id' => 'required|integer',
                'device' => 'required|string',
                'longitude_checkout' => 'required|numeric',
                'latitude_checkout' => 'required|numeric'
            ]);
            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Attendance data.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create new Attendance name
            $attendance = Attendance::where('id', $request->get('id'))->firstOrFail();
            if(empty($attendance)){
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            $attendance->device_checkout = $request->get('device');
            $attendance->longitude_checkout = $request->get('longitude_checkout');
            $attendance->latitude_checkout = $request->get('latitude_checkout');
            $attendance->time_checkout = new \DateTime('now');
            $attendance->save();
            $now = date('Y-m-d');
            $rota =  Rota::where('staff_id', $attendance->staff_id)
                ->where('rota_date', $now)->first();

            if(!empty($rota) && $rota->is_present != true){
                $rota->is_present = true;
                $rota->save();
            }
            return response()->json([
                'success' => true,
                'message' => $attendance
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

        Attendance::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Attendance successfully deleted"
        ], Response::HTTP_OK);
    }
}
