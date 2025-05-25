<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RotaCollection;
use App\Http\Resources\RotaResource;
use App\Models\Employee;
use App\Models\Rota;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class RotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $from = empty($request->get('date_from')) ? null : new \DateTime($request->get('date_from'));
        $to = empty($request->get('date_to')) ? null : new \DateTime($request->get('date_to'));
        $date = empty($request->get('date')) ? null : new \DateTime($request->get('date'));
        $limit =  $request->get('limit');
        $staff_id = $request->get('staff_id');

        $res = [];

        if(!empty($limit)){
            if (!empty($staff_id)){
                if(!empty($date)){
                    $res = Rota::where('staff_id', $staff_id)->where('rota_date', $date)->orderBy('rota_date', 'DESC')->paginate($limit);
                }elseif (!empty($from) && !empty($to)){
                    $res = Rota::where('staff_id', $staff_id)->whereBetween('rota_date', [$from, $to])->orderBy('rota_date', 'DESC')->paginate($limit);
                }else{
                    $res = Rota::where('staff_id', $staff_id)->orderBy('rota_date', 'DESC')->paginate($limit);
                }
            }else{
                if(!empty($date)){
                    $res = Rota::where('rota_date', $date)->orderBy('rota_date', 'DESC')->paginate($limit);
                }elseif (!empty($from) && !empty($to)){
                    $res = Rota::whereBetween('rota_date', [$from, $to])->orderBy('rota_date', 'DESC')->paginate($limit);
                }else{
                    $res = Rota::orderBy('rota_date', 'DESC')->paginate($limit);
                }
            }
        }else{
            if (!empty($staff_id)){
                if(!empty($date)){
                    $res = Rota::where('staff_id', $staff_id)->where('rota_date', $date)->orderBy('rota_date', 'DESC')->get();
                }elseif (!empty($from) && !empty($to)){
                    $res = Rota::where('staff_id', $staff_id)->whereBetween('rota_date', [$from, $to])->orderBy('rota_date', 'DESC')->get();
                }else{
                    $res = Rota::where('staff_id', $staff_id)->orderBy('rota_date', 'DESC')->get();
                }
            }else{
                if(!empty($date)){
                    $res = Rota::where('rota_date', $date)->orderBy('rota_date', 'DESC')->get();
                }elseif (!empty($from) && !empty($to)){
                    $res = Rota::whereBetween('rota_date', [$from, $to])->orderBy('rota_date', 'DESC')->get();
                }else{
                    $res = Rota::orderBy('rota_date', 'DESC')->get();
                }
            }
        }
        return \response()->json([
                'success' => true,
                'message' => new RotaCollection($res)
            ]
            , Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function indexDate(Request  $request)
    {
        if (empty($request->get('staff_id'))){
            return \response()->json([
                    'success' => true,
                    'message' => new RotaCollection(Rota::where('rota_date', new \DateTime($request->get('date')))->get())
                ]
                , Response::HTTP_OK);
        }
        return \response()->json([
                'success' => true,
                'message' => new RotaCollection(Rota::where('rota_date', new \DateTime($request->get('date')))
                    ->where('staff_id', $request->get('staff_id'))
                    ->get()
                )
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
        $from = new \DateTime($request->get('date_from'));
        $to = new \DateTime($request->get('date_to'));

        if (empty($request->get('staff_id'))){
            return \response()->json([
                    'success' => true,
                    'message' => RotaResource::collection(Rota::whereBetween('rota_date', [$from, $to])->get())
                ]
                , Response::HTTP_OK);
        }

        return \response()->json([
                'success' => true,
                'message' => new RotaCollection(Rota::whereBetween('rota_date', [$from, $to])
                    ->where('staff_id', $request->get('staff_id'))
                    ->get()
                )
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
                'message' => new RotaCollection(Rota::paginate((int) $request->get('limit')))
            ]
            , Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'datas' => 'required|array'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data.'
                ], 400);
            }

            $datas = $request->get('datas');

            $rotas =  [];
            if(!empty($datas)){
                foreach ($datas as $data){
                    $user = User::where('email', $data['email'])->first();
                    $dates = $data['dates'];
                    $shifts = $data['shifts'];
                    if(!empty($user) && count($shifts) === count($dates) && (count($shifts) <= 7 && count($shifts) > 0)){
                        $staff =  Employee::where('user_id', $user->id)->first();
                        if(!empty($staff)){
                            foreach ($dates as $key => $date){
                                if(empty($shifts[$key])){
                                    Rota::where('staff_id', $staff->id)
                                        ->where('rota_date', $date)
                                        ->delete();
                                }else{
                                    $rotas[] =  [
                                        'rota_date' => $date,
                                        'shift' => $shifts[$key],
                                        'staff_id' => $staff->id
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            // Store All
            if(!empty($rotas)){
                foreach ($rotas as $rota){
                    $matchThese = ['rota_date'=> $rota['rota_date'], 'shift'=> $rota['shift'], 'staff_id'=> $rota['staff_id']];
                    Rota::updateOrCreate($matchThese, $rota);
                }
            }

            // Create new Rota
            return response()->json([
                'success' => true,
                'message' => 'Rotas successfully added'
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
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only('staff_id', 'date'),[
                'staff_id' => 'required|integer',
                'date' => 'required|date'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Rota data.'
                ], 400);
            }

            if(empty($request->get('shift'))){
                Rota::where('staff_id', $request->get('staff_id'))
                    ->where('rota_date', $request->get('date'))
                    ->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Rota successfully deleted.'
                ], Response::HTTP_OK);
            }

            $shift =   $request->get('shift');
            $staff =  Employee::where('id', $request->get('staff_id'))->first();

            if(empty($shift) || empty($staff)){
                return response()->json([
                    'success' => false,
                    'message' => 'Shift or Rota not found.'
                ], 400);
            }

            $updata =  [
                'rota_date' => $request->get('date'),
                'shift' => $shift,
                'is_present' => $request->get('is_present') ,
                'staff_id' => $staff->id
            ];

            $matchThese = ['rota_date'=> $request->get('date'), 'shift'=> $shift, 'staff_id'=> $staff->id];
            $rotas = Rota::updateOrCreate($matchThese, $updata);

            // Create new Rota
            return response()->json([
                'success' => true,
                'message' => $rotas
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
        $rota = Rota::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new RotaResource($rota)
            ]
            , Response::HTTP_OK);
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

        Rota::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Rota successfully deleted"
        ], Response::HTTP_OK);
    }
}
