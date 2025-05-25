<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffChartCollection;
use App\Http\Resources\StaffChartResource;
use App\Http\Resources\StaffChartSimpleCollection;
use App\Http\Services\SaleOrderService;
use App\Models\Client;
use App\Models\Employee;
use App\Models\FamilyFriendAssignment;
use App\Models\Item;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\NotificationSettings;
use App\Mail\MedicalVisitAdminMail;
use App\Mail\MedicalVisitFriendMail;
use App\Helpers\Helper;
use App\Models\SystemContacts;
use App\Models\MedicineName;
use App\Models\StaffAssignment;
use App\Models\StaffChart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class StaffChartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $staff_id =  $request->get('staff_id');
        $client_id = $request->get('client_id');
        $category = $request->get('category');
        $limit= $request->get('limit');
        $user_id = $request->get('user_id');
        $clientIds = null;

        $start =  new \DateTime($request->get('start_date'));
        $end =  new \DateTime($request->get('end_date'));

        $singleDate = ($start === $end && (!empty($request->get('start_date')) && !empty($request->get('start_date'))) ) ? $start : null;

        if(!empty($user_id)){
            $clientIds = FamilyFriendAssignment::where('familyfriend_id', $user_id)->pluck('client_id');
        }

        if(!empty($staff_id)){
            $clientIds = StaffAssignment::where('staff_id', $staff_id)->pluck('client_id');
        }
        $res = [];

        if (!empty($singleDate)){
            if(!empty($client_id) && !empty($staff_id) && !empty($category)){
                $res = StaffChart::where('category', $category)
                    ->where('client_id', $client_id)
                    ->where('staff_id', $staff_id)
                    ->where('created_at', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($client_id) && !empty($category)){
                $res = StaffChart::where('category', $category)
                    ->where('client_id', $client_id)
                    ->where('created_at', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($staff_id) && !empty($category)) {
                $res = StaffChart::where('category', $category)
                    ->where('staff_id', $staff_id)
                    ->where('created_at', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($category)){
                $res = StaffChart::where('category', $category)
                    ->where('created_at', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('client_id', $client_id)
                    ->where('created_at', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if(!empty($staff_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('created_at', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if (!empty($client_id)){
                $res = StaffChart::where('client_id', $client_id)
                    ->where('created_at', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                $res = StaffChart::where('created_at', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }
        }elseif ((!empty($request->get('start_date')) && !empty($request->get('start_date')))){
            if(!empty($client_id) && !empty($staff_id) && !empty($category)){
                $res = StaffChart::where('category', $category)
                    ->where('client_id', $client_id)
                    ->where('staff_id', $staff_id)
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($client_id) && !empty($category)){
                $res = StaffChart::where('category', $category)
                    ->where('client_id', $client_id)
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($staff_id) && !empty($category)) {
                $res = StaffChart::where('category', $category)
                    ->where('staff_id', $staff_id)
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($category)){
                $res = StaffChart::where('category', $category)
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }elseif (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('client_id', $client_id)
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if(!empty($staff_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if (!empty($client_id)){
                $res = StaffChart::where('client_id', $client_id)
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                $res = StaffChart::whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }
        }else{
            if(!empty($limit)){
                if(!empty($client_id) && !empty($staff_id) && !empty($category)){
                    $res = StaffChart::where('category', $category)
                        ->where('client_id', $client_id)
                        ->where('staff_id', $staff_id)
                        ->orderBy('updated_at', 'DESC')
                        ->paginate($limit);
                }elseif (!empty($client_id) && !empty($category)){
                    $res = StaffChart::where('category', $category)
                        ->where('client_id', $client_id)
                        ->orderBy('updated_at', 'DESC')
                        ->paginate($limit);
                }elseif (!empty($staff_id) && !empty($category)) {
                    $res = StaffChart::where('category', $category)
                        ->where('staff_id', $staff_id)
                        ->orderBy('updated_at', 'DESC')
                        ->paginate($limit);
                }elseif (!empty($category)){
                    $res = StaffChart::where('category', $category)
                        ->orderBy('updated_at', 'DESC')
                        ->paginate($limit);
                }elseif (!empty($staff_id) && !empty($client_id)){
                    $res = StaffChart::where('client_id', $client_id)
                        ->where('staff_id', $staff_id)
                        ->orderBy('updated_at', 'DESC')
                        ->paginate($limit);
                }elseif (!empty($staff_id)){
                    $res = StaffChart::whereIn('client_id', $clientIds)
                        ->orderBy('updated_at', 'DESC')
                        ->paginate($limit);
                }elseif (!empty($client_id)){
                    $res = StaffChart::where('client_id', $client_id)
                        ->orderBy('updated_at', 'DESC')
                        ->paginate($limit);
                }elseif (!empty($clientIds)){
                    $res = StaffChart::whereIn('client_id', $clientIds)
                        ->orderBy('updated_at', 'DESC')
                        ->paginate($limit);
                } else{
                    $res = new StaffChartSimpleCollection(StaffChart::orderBy('updated_at', 'DESC')
                        ->paginate($limit));

                    return \response()->json([
                            'success' => true,
                            'message' => $res
                        ]
                        , Response::HTTP_OK);
                }
            }else{
                if(!empty($client_id) && !empty($staff_id) && !empty($category)){
                    $res = StaffChart::where('category', $category)
                        ->where('client_id', $client_id)
                        ->where('staff_id', $staff_id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                }elseif (!empty($client_id) && !empty($category)){
                    $res = StaffChart::where('category', $category)
                        ->where('client_id', $client_id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                }elseif (!empty($staff_id) && !empty($category)) {
                    $res = StaffChart::where('category', $category)
                        ->where('staff_id', $staff_id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                }elseif (!empty($category)){
                    $res = StaffChart::where('category', $category)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                }elseif (!empty($staff_id) && !empty($client_id)){
                    $res = StaffChart::where('client_id', $client_id)
                        ->where('staff_id', $staff_id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                }elseif (!empty($staff_id)){
                    $res = StaffChart::whereIn('client_id', $clientIds)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                }elseif (!empty($client_id)){
                    $res = StaffChart::where('client_id', $client_id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                }elseif (!empty($clientIds)){
                    $res = StaffChart::whereIn('client_id', $clientIds)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                }else{
                    $res = new StaffChartSimpleCollection(StaffChart::orderBy('updated_at', 'DESC')
                        ->get());

                    return \response()->json([
                            'success' => true,
                            'message' => $res
                        ]
                        , Response::HTTP_OK);
                }
            }
        }
        $filtered_data = [];
        $res = new StaffChartCollection($res);
        foreach ($res as $value) {
            $active = 1;
            if(isset($value['client']) && isset($value['client']['user'])) {
                $active = $value['client']["user"]["active"];
            }
            if($active == 1)
            array_push($filtered_data, $value);
        }

        return \response()->json([
                'success' => true,
                'message' => $filtered_data
            ]
            , Response::HTTP_OK);
    }

   public function indexNotes(Request $request)
    {
        $staff_id =  $request->get('staff_id');
        $client_id = $request->get('client_id');
        $category = $request->get('category');
        $limit = $request->get('limit');

        $user_id = $request->get('user_id');
        $clientIds = null;
        if(!empty($user_id)){
            $clientIds = FamilyFriendAssignment::where('familyfriend_id', $user_id)->pluck('client_id');
        }

        if(!empty($staff_id)){
            $clientIds = StaffAssignment::where('staff_id', $staff_id)->pluck('client_id');
        }

        $res = [];
        if(!empty($limit)){
            if(!empty($client_id) && !empty($staff_id) && !empty($category)){
                $res = StaffChart::where('comment', '<>', null)->where('category', $category)->where('client_id', $client_id)->where('staff_id', $staff_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($client_id) && !empty($category)){
                $res = StaffChart::where('comment', '<>', null)->where('category', $category)->where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($staff_id) && !empty($category)){
                $res = StaffChart::where('comment', '<>', null)->where('category', $category)->where('staff_id', $staff_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('comment', '<>', null)->where('client_id', $client_id)->where('staff_id', $staff_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($staff_id)){
                $res = StaffChart::where('comment', '<>', null)->whereIn('client_id', $clientIds)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($client_id)){
                $res = StaffChart::where('comment', '<>', null)->where('client_id', $client_id)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($category)){
                $res = StaffChart::where('comment', '<>', null)->where('category', $category)->orderBy('updated_at', 'DESC')->paginate($limit);
            }elseif (!empty($clientIds)){
                $res = StaffChart::where('comment', '<>', null)->whereIn('client_id', $clientIds)->orderBy('updated_at', 'DESC')->paginate($limit);
            } else{
                $res = StaffChart::where('comment', '<>', null)->orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }else{
            if(!empty($client_id) && !empty($staff_id) && !empty($category)){
                $res = StaffChart::where('comment', '<>', null)->where('category', $category)->where('client_id', $client_id)->where('staff_id', $staff_id)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($client_id) && !empty($category)){
                $res = StaffChart::where('comment', '<>', null)->where('category', $category)->where('client_id', $client_id)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($staff_id) && !empty($category)){
                $res = StaffChart::where('comment', '<>', null)->where('category', $category)->where('staff_id', $staff_id)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('comment', '<>', null)->where('client_id', $client_id)->where('staff_id', $staff_id)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($staff_id)){
                $res = StaffChart::where('comment', '<>', null)->whereIn('client_id', $clientIds)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($client_id)){
                $res = StaffChart::where('comment', '<>', null)->where('client_id', $client_id)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($category)){
                $res = StaffChart::where('comment', '<>', null)->where('category', $category)->orderBy('updated_at', 'DESC')->get();
            }elseif (!empty($clientIds)){
                $res = StaffChart::where('comment', '<>', null)->whereIn('client_id', $clientIds)->orderBy('updated_at', 'DESC')->get();
            }else{
                $res = StaffChart::where('comment', '<>', null)->orderBy('updated_at', 'DESC')->get();
            }
        }
        $filtered_data = [];
        $res = new StaffChartCollection($res);
        foreach ($res as $value) {
            $active = 1;
            if(isset($value['client']) && isset($value['client']['user'])) {
                $active = $value['client']["user"]["active"];
            }
            if($active == 1)
            array_push($filtered_data, $value);
        }

        return \response()->json([
                'success' => true,
                'message' => $filtered_data
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
                'message' =>  new StaffChartCollection(StaffChart::paginate((int) $request->get('limit')))
            ]
            , Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only( 'category', 'staff_id', 'client_id'),[
                'staff_id' => 'required|integer',
                'client_id' => 'required|integer',
                'category' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Staff Chart data.'
                ], 400);
            }

            $client =  Client::where('id', $request->get('client_id'))->first();
            $staff =  Employee::where('id', $request->get('staff_id'))->first();

            if(empty($client)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found.'
                ], 400);
            }

            if(empty($staff)){
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found.'
                ], 400);
            }

            $staffChart = StaffChart::where('client_id', $request->get('client_id'))
                ->where('staff_id', $request->get('staff_id'))
                ->where('report_date', $request->get('report_date'))
                ->where('report_time', $request->get('report_time'))
                ->where('food_period', $request->get('food_period'))
                ->where('food_type', $request->get('food_type'))
                ->where('category', $request->get('category'))
                ->where('medicine_name', $request->get('medicine_name'))
                ->where('medication_type', $request->get('medication_type'))
                ->first();

            // Create new Staff Chart
            if (empty($staffChart)){
                $staffChart = new StaffChart();
            }

            $staffChart->client_id = $client->id;
            $staffChart->staff_id = $staff->id;
            $staffChart->report_date = $request->get('report_date');
            $staffChart->report_time = $request->get('report_time');
            $staffChart->category = $request->get('category');
            $staffChart->comment = $request->get('comment');
            $staffChart->is_red_flag = $request->get('is_red_flag');
            $staffChart->media1 = $request->get('media1');
            $staffChart->media2 = $request->get('media2');
            $staffChart->media3 = $request->get('media3');
            $staffChart->media4 = $request->get('media4');
            $staffChart->category = $request->get('category');
            $staffChart->health_issue = $request->get('health_issue');
            $staffChart->cream_applied = $request->get('cream_applied');
            $staffChart->consequence_event = $request->get('consequence_event');
            $staffChart->location = $request->get('location');
            $staffChart->reason_for_visit = $request->get('reason_for_visit');
            $staffChart->resultant_action = $request->get('resultant_action');
            $staffChart->is_age6069 = $request->get('is_age6069');
            $staffChart->is_age7079 = $request->get('is_age7079');
            $staffChart->is_age8089 = $request->get('is_age8089');
            $staffChart->is_age90_plus = $request->get('is_age90_plus');
            $staffChart->movement_going_up_stairs = $request->get('movement_going_up_stairs');
            $staffChart->movement_coming_down_stairs = $request->get('movement_coming_down_stairs');
            $staffChart->suggested_equip_resident = $request->get('suggested_equip_resident');
            $staffChart->suggested_equip_staff = $request->get('suggested_equip_staff');
            $staffChart->any_safeguarding_issues = $request->get('any_safeguarding_issues');
            $staffChart->risk_to_children = $request->get('risk_to_children');
            $staffChart->risk_to_offending = $request->get('risk_to_offending');
            $staffChart->any_other_areas_of_risk = $request->get('any_other_areas_of_risk');
            $staffChart->dosage_morning =  $request->get('dosage_morning');
            $staffChart->dosage_morning_when =  $request->get('dosage_morning_when');
            $staffChart->dosage_afternoon_when = $request->get('dosage_afternoon_when');
            $staffChart->dosage_evening = $request->get('dosage_evening');
            $staffChart->dosage_evening_when = $request->get('dosage_evening_when');
            $staffChart->dosage_afternoon = $request->get('dosage_afternoon');
            $staffChart->reason_for_medication = $request->get('reason_for_medication');
            $staffChart->other_intake_guide = $request->get('other_intake_guide');
            $staffChart->is_morning_dose_administered = $request->get('is_morning_dose_administered');
            $staffChart->is_afternoon_dose_administered = $request->get('is_afternoon_dose_administered');
            $staffChart->is_evening_dose_administered = $request->get('is_evening_dose_administered');
            $staffChart->start_date = $request->get('start_date');
            $staffChart->is_positive_covid_19 = $request->get('is_positive_covid_19');
            $staffChart->weight_pounds = $request->get('weight_pounds');
            $staffChart->use_kilogram = $request->get('use_kilogram');
            $staffChart->description = $request->get('description');
            $staffChart->result = $request->get('result');
            $staffChart->score = $request->get('score');
            $staffChart->drink_type = $request->get('drink_type');
            $staffChart->delivery_method = $request->get('delivery_method');
            $staffChart->quantity_ml = $request->get('quantity_ml');
            $staffChart->level_of_need = $request->get('level_of_need');
            $staffChart->present_situation = $request->get('present_situation');
            $staffChart->actions = $request->get('actions');
            $staffChart->family_participant = $request->get('family_participant');
            $staffChart->other_participants = $request->get('other_participants');
            $staffChart->resident_needs = $request->get('resident_needs');
            $staffChart->weight_kg = $request->get('weight_kg');
            $staffChart->review_date = new \DateTime($request->get('review_date'));
            $staffChart->risk_of_falls = $request->get('risk_of_falls');
            $staffChart->risk_of_physical_abuse_to_self = $request->get('risk_of_physical_abuse_to_self');
            $staffChart->risk_of_physical_abuse_to_others = $request->get('risk_of_physical_abuse_to_others');
            $staffChart->risk_of_discrimination = $request->get('risk_of_discrimination');
            $staffChart->risk_of_pressure_sores = $request->get('risk_of_pressure_sores');
            $staffChart->risk_of_manual_handling = $request->get('risk_of_manual_handling');
            $staffChart->risk_of_wandering = $request->get('risk_of_wandering');
            $staffChart->risk_to_property = $request->get('risk_to_property');
            $staffChart->agitation_verbal_physical_aggression = $request->get('agitation_verbal_physical_aggression');
            $staffChart->self_neglect = $request->get('self_neglect');
            $staffChart->vulnerability_from_others = $request->get('vulnerability_from_others');
            $staffChart->indication_of_physical_emotional_abuse = $request->get('indication_of_physical_emotional_abuse');
            $staffChart->is_full_mobile = $request->get('is_full_mobile');
            $staffChart->use_walking_aid = $request->get('use_walking_aid');
            $staffChart->is_wheel_chair_dependant = $request->get('is_wheel_chair_dependant');
            $staffChart->need_assistance_of_one = $request->get('need_assistance_of_one');
            $staffChart->need_assistance_of_two = $request->get('need_assistance_of_two');
            $staffChart->is_fully_dependant = $request->get('is_fully_dependant');
            $staffChart->has_demantia_frail = $request->get('has_demantia_frail');
            $staffChart->has_high_blood_pressure = $request->get('has_high_blood_pressure');
            $staffChart->has_poor_circulation = $request->get('has_poor_circulation');
            $staffChart->has_cva_tia = $request->get('has_cva_tia');
            $staffChart->has_osteo_rheumatoid_arthritis = $request->get('has_osteo_rheumatoid_arthritis');
            $staffChart->has_osteoporosis = $request->get('has_osteoporosis');
            $staffChart->has_poor_vision = $request->get('has_poor_vision');
            $staffChart->has_poor_hearing = $request->get('has_poor_hearing');
            $staffChart->has_diabetes = $request->get('has_diabetes');
            $staffChart->has_amputee = $request->get('has_amputee');
            $staffChart->has_history_of_falls = $request->get('has_history_of_falls');
            $staffChart->admitted_for_investigation = $request->get('admitted_for_investigation');
            $staffChart->has_catheter = $request->get('has_catheter');
            $staffChart->has_inco_urine = $request->get('has_inco_urine');
            $staffChart->has_inco_faeces = $request->get('has_inco_faeces');
            $staffChart->has_incontinent_doubly = $request->get('has_incontinent_doubly');
            $staffChart->requires_assistance = $request->get('requires_assistance');
            $staffChart->uses_diuretics = $request->get('uses_diuretics');
            $staffChart->uses_sedatives = $request->get('uses_sedatives');
            $staffChart->uses_tranquilisers = $request->get('uses_tranquilisers');
            $staffChart->uses_anti_hypertensive = $request->get('uses_anti_hypertensive');
            $staffChart->uses_reg_aperient = $request->get('uses_reg_aperient');
            $staffChart->uses_hypoglycaemic_agents = $request->get('uses_hypoglycaemic_agents');
            $staffChart->drink_alcohol = $request->get('drink_alcohol');
            $staffChart->resident_height = $request->get('resident_height');
            $staffChart->resident_weight = $request->get('resident_weight');
            $staffChart->info_comprehension = $request->get('info_comprehension');
            $staffChart->info_behavior = $request->get('info_behavior');
            $staffChart->info_disability = $request->get('info_disability');
            $staffChart->handling_pain = $request->get('handling_pain');
            $staffChart->handling_skin = $request->get('handling_skin');
            $staffChart->handling_other = $request->get('handling_other');
            $staffChart->movement_walking = $request->get('movement_walking');
            $staffChart->movement_standing = $request->get('movement_standing');
            $staffChart->movement_using_toilet = $request->get('movement_using_toilet');
            $staffChart->movement_going_to_bed = $request->get('movement_going_to_bed');
            $staffChart->movement_getting_from_bed = $request->get('movement_getting_from_bed');
            $staffChart->movement_on_bed = $request->get('movement_on_bed');
            $staffChart->visit_type = $request->get('visit_type');
            $staffChart->visit_location = $request->get('visit_location');
            $staffChart->visitor_name = $request->get('visitor_name');
            $staffChart->bowel_type = $request->get('bowel_type');
            $staffChart->weight_grams = $request->get('weight_grams');
            $staffChart->weight_stone = $request->get('weight_stone');
            $staffChart->food_period = $request->get('food_period');
            $staffChart->food_type = $request->get('food_type');
            $staffChart->review_type = $request->get('review_type');
            $staffChart->service_user_participant = $request->get('service_user_participant');
            $staffChart->pulse = $request->get('pulse');
            $staffChart->temperature = $request->get('temperature');
            $staffChart->blood_pressure_systolic = $request->get('blood_pressure_systolic');
            $staffChart->blood_pressure_diastolic = $request->get('blood_pressure_diastolic');
            $staffChart->blood_sugar = $request->get('blood_sugar');
            $staffChart->oxygen_saturation = $request->get('oxygen_saturation');
            $staffChart->stool_observed = $request->get('stool_observed');
            $staffChart->respiration = $request->get('respiration');
            $staffChart->antecedent_event = $request->get('antecedent_event');

            $medicine_name =  $request->get('medicine_name');
            $category = $request->get('category');

            if(!empty($medicine_name) && (!empty($category) && $category === "Administer Medication")){
                $item =  Item::where('name', $medicine_name)->first();

                if(empty($item)){
                    return response()->json([
                        'success' => false,
                        'message' => 'Product Item '.$medicine_name.' not found'
                    ], 400);
                }
                $totalDosage = 0;
                $is_morning_dose_administered = $request->get('is_morning_dose_administered');
                $is_afternoon_dose_administered = $request->get('is_afternoon_dose_administered');
                $is_evening_dose_administered = $request->get('is_evening_dose_administered');

                $dosage_morning_when = $request->get('dosage_morning_when');
                $dosage_afternoon_when = $request->get('dosage_afternoon_when');
                $dosage_evening_when = $request->get('dosage_evening_when');
                
                // if($dosage_morning_when === "Administered"){
                //     $dosage_morning = $request->get('dosage_morning');
                //     $totalDosage += $dosage_morning;
                //     $staffChart->is_morning_dose_administered = ($dosage_morning > 0);
                // }
                
                // if($dosage_afternoon_when === "Administered"){
                //     $dosage_afternoon = $request->get('dosage_afternoon');
                //     $totalDosage += $dosage_afternoon;
                //     $staffChart->is_afternoon_dose_administered = ($dosage_afternoon > 0);
                // }

                // if($dosage_evening_when === "Administered"){
                //     $dosage_evening = $request->get('dosage_evening');
                //     $totalDosage += $dosage_evening;
                //     $staffChart->is_evening_dose_administered = ($dosage_evening > 0);
                // }

                // $staffChart->medicine_name =  $item->name;

                // if($totalDosage > 0){
                //     $saleOrderService = new SaleOrderService();

                   
                //     $request['staff_id'] = $staff->id; #add_n
                //     $request['client_id'] = $request->get('client_id');
                //     $request['item_name'] = $item->name;
                //     $request['total_order'] = $totalDosage;
                //     $request['order_date'] = 'now';
                //     $request['invoiced'] = true;
                    
                //     $request['total_order'] = $totalDosage;

                //     $saleOrderResponse = $saleOrderService->createSaleOrder($request);

                //     if(!$saleOrderResponse['success']){
                //         return response()->json(
                //             $saleOrderResponse, 400
                //         );
                //     }
                // }
                
                $staffChart->medicine_name = $item->name;
                $saleOrderService = new SaleOrderService();


                // Define time periods to process
                $timePeriods = [
                    'morning' => $dosage_morning_when === "Administered" ? $request->get('dosage_morning') : 0,
                    'afternoon' => $dosage_afternoon_when === "Administered" ? $request->get('dosage_afternoon') : 0,
                    'evening' => $dosage_evening_when === "Administered" ? $request->get('dosage_evening') : 0
                ];

                // Set administration flags
                $staffChart->is_morning_dose_administered = ($timePeriods['morning'] > 0);
                $staffChart->is_afternoon_dose_administered = ($timePeriods['afternoon'] > 0);
                $staffChart->is_evening_dose_administered = ($timePeriods['evening'] > 0);

                $baseRequest = [
                    'staff_id' => $staff->id,
                    'client_id' => $request->get('client_id'),
                    'order_date' => 'now',
                    'invoiced' => true
                ];

                // Process each time period in a single loop
                foreach ($timePeriods as $period => $dosage) {
                    if ($dosage > 0) {
                        // Create sale order with specific period data
                        $periodRequest = array_merge($baseRequest, [
                            'item_name' => $item->name,
                            'total_order' => $dosage
                        ]);

                        $request = new Request($periodRequest);

                        $saleOrderResponse = $saleOrderService->createSaleOrder($request);
                        if (!$saleOrderResponse['success']) {
                            return response()->json($saleOrderResponse, 400);
                        }
                    }
                }
            
            }elseif (!empty($medicine_name) && !empty($category) && $category ===  'Procedures'){
                $item =  Item::where('name', $medicine_name)->first();
            
   

                if(empty($item)){
                    return response()->json([
                        'success' => false,
                        'message' => 'Product Item '.$medicine_name.' not found'
                    ], 400);
                }

                $saleOrderService = new SaleOrderService();

                $request['client_id'] = $request->get('client_id');
                $request['item_name'] = $item->name;
                $request['order_date'] =  'now';
            //   $request['total_order'] = $total_order;
                $request['invoiced'] = true;
                $saleOrderResponse = $saleOrderService->createSaleOrder($request);
                  $total_order = 0;
                if(!$saleOrderResponse['success']){
                    return response()->json(
                        $saleOrderResponse, 400
                    );
                }
                 $total_order = 1; // or any other value based on some calculation or condition
    
                    } else {
                        $total_order = 0;
                    }
            
            $staffChart->medication_type =  $request->get('medication_type');
            $staffChart->medication_unit =  $request->get('medication_unit');
            // $staffChart->total_order = $total_order;
            //For Admin Notification,
            $emailNotifications = NotificationSettings::where('trigger_name', 'MEDICAL_VISIT_ADMIN')
            ->where('send_email', 1)
            ->get();

        $smsNotifications = NotificationSettings::where('trigger_name', 'MEDICAL_VISIT_ADMIN')
            ->where('send_sms', 1)
            ->get();
        if($staffChart->category == "Medical Visits"){
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'MEDICAL_VISIT_ADMIN')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                        $name = $client->user->first_name . " " . $client->user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
            
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new MedicalVisitAdminMail($name, $url);
                                Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::medicalVisitAdminMessage($name, $url);
                                Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
                
    }
    //Friend Notifications,
  
        if($staffChart->category == "Medical Visits"){
                $emailNotifications = NotificationSettings::where('trigger_name', 'MEDICAL_VISIT_FRIEND')
            ->where('send_email', 1)
            ->get();

        $smsNotifications = NotificationSettings::where('trigger_name', 'MEDICAL_VISIT_FRIEND')
            ->where('send_sms', 1)
            ->get();
               
                if ($emailNotifications) {
                    foreach ( $client->friends as $friend) {
                        $email = $friend->email;
                      $familyfriend_name=$friend->first_name . ' ' . $friend->last_name;
                    $mail = new MedicalVisitFriendMail($name, $familyfriend_name);
                        Helper::sendEmail($email, $mail);
                    }
                    
                       
                    
                    
                
                }
                
                if ($smsNotifications) {
                    foreach ( $client->friends as $contact) {
                          $phoneNumber = $contact->phone_num;
                         $familyfriend_name= $contact->first_name . ' ' . $contact->last_name;
                         $message = TwilioSMSController::medicalVisitFriendMessage($name, $familyfriend_name);
                        Helper::sendSms($phoneNumber, $message);
                    }
                
                        
                   
                }
    }

            $staffChart->save();
            return response()->json([
                'success' => true,
                'message' => $staffChart->id
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

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->only( 'id', 'category', 'staff_id', 'client_id'),[
                'id' => 'required|integer',
                'staff_id' => 'required|integer',
                'client_id' => 'required|integer',
                'category' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Staff Chart data.'
                ], 400);
            }
            $client =  Client::where('id', $request->get('client_id'))->first();
            $staff =  Employee::where('id', $request->get('staff_id'))->first();

            if(empty($staff)){
                return response()->json([
                    'success' => false,
                    'message' => 'Staff  not found.'
                ], 400);
            }

            if(empty($client)){
                return response()->json([
                    'success' => false,
                    'message' => 'Client  not found.'
                ], 400);
            }


            // Search for Staff Chart
            $staffChart =  StaffChart::where('id', $request->get('id'))->first();
            if(empty($staffChart)){
                return response()->json([
                    'success' => false,
                    'message' => 'Staff Chart not found.'
                ], 400);
            }

            $staffChart = StaffChart::where('id', $request->get('id'))
                ->where('client_id', $request->get('client_id'))
                ->where('staff_id', $request->get('staff_id'))
                ->where('report_date',  $request->get('report_date'))
                ->where('report_time', $request->get('report_time'))
                ->where('category', $request->get('category'))
                ->where('medicine_name', $request->get('medicine_name'))
                ->where('medication_type', $request->get('medication_type'))
                ->first();

            $staffChart->is_red_flag = $request->get('is_red_flag');
            $staffChart->media1 = $request->get('media1');
            $staffChart->media2 = $request->get('media2');
            $staffChart->media3 = $request->get('media3');
            $staffChart->media4 = $request->get('media4');
            $staffChart->category = $request->get('category');
            $staffChart->health_issue = $request->get('health_issue');
            $staffChart->cream_applied = $request->get('cream_applied');
            $staffChart->consequence_event = $request->get('consequence_event');
            $staffChart->location = $request->get('location');
            $staffChart->reason_for_visit = $request->get('reason_for_visit');
            $staffChart->resultant_action = $request->get('resultant_action');
            $staffChart->is_age6069 = $request->get('is_age6069');
            $staffChart->is_age7079 = $request->get('is_age7079');
            $staffChart->is_age8089 = $request->get('is_age8089');
            $staffChart->is_age90_plus = $request->get('is_age90_plus');
            $staffChart->movement_going_up_stairs = $request->get('movement_going_up_stairs');
            $staffChart->movement_coming_down_stairs = $request->get('movement_coming_down_stairs');
            $staffChart->suggested_equip_resident = $request->get('suggested_equip_resident');
            $staffChart->suggested_equip_staff = $request->get('suggested_equip_staff');
            $staffChart->any_safeguarding_issues = $request->get('any_safeguarding_issues');
            $staffChart->risk_to_children = $request->get('risk_to_children');
            $staffChart->risk_to_offending = $request->get('risk_to_offending');
            $staffChart->any_other_areas_of_risk = $request->get('any_other_areas_of_risk');
            $staffChart->dosage_afternoon_when = $request->get('dosage_afternoon_when');
            $staffChart->dosage_evening = $request->get('dosage_evening');
            $staffChart->dosage_evening_when = $request->get('dosage_evening_when');
            $staffChart->dosage_afternoon = $request->get('dosage_afternoon');
            $staffChart->reason_for_medication = $request->get('reason_for_medication');
            $staffChart->other_intake_guide = $request->get('other_intake_guide');
            $staffChart->is_morning_dose_administered = $request->get('is_morning_dose_administered');
            $staffChart->is_afternoon_dose_administered = $request->get('is_afternoon_dose_administered');
            $staffChart->is_evening_dose_administered = $request->get('is_evening_dose_administered');
            $staffChart->start_date = $request->get('start_date');
            $staffChart->is_positive_covid_19 = $request->get('is_positive_covid_19');
            $staffChart->weight_pounds = $request->get('weight_pounds');
            $staffChart->use_kilogram = $request->get('use_kilogram');
            $staffChart->description = $request->get('description');
            $staffChart->result = $request->get('result');
            $staffChart->score = $request->get('score');
            $staffChart->drink_type = $request->get('drink_type');
            $staffChart->delivery_method = $request->get('delivery_method');
            $staffChart->quantity_ml = $request->get('quantity_ml');
            $staffChart->level_of_need = $request->get('level_of_need');
            $staffChart->present_situation = $request->get('present_situation');
            $staffChart->actions = $request->get('actions');
            $staffChart->family_participant = $request->get('family_participant');
            $staffChart->other_participants = $request->get('other_participants');
            $staffChart->resident_needs = $request->get('resident_needs');
            $staffChart->weight_kg = $request->get('weight_kg');
            $staffChart->review_date = new \DateTime($request->get('review_date'));
            $staffChart->risk_of_falls = $request->get('risk_of_falls');
            $staffChart->risk_of_physical_abuse_to_self = $request->get('risk_of_physical_abuse_to_self');
            $staffChart->risk_of_physical_abuse_to_others = $request->get('risk_of_physical_abuse_to_others');
            $staffChart->risk_of_discrimination = $request->get('risk_of_discrimination');
            $staffChart->risk_of_pressure_sores = $request->get('risk_of_pressure_sores');
            $staffChart->risk_of_manual_handling = $request->get('risk_of_manual_handling');
            $staffChart->risk_of_wandering = $request->get('risk_of_wandering');
            $staffChart->risk_to_property = $request->get('risk_to_property');
            $staffChart->agitation_verbal_physical_aggression = $request->get('agitation_verbal_physical_aggression');
            $staffChart->self_neglect = $request->get('self_neglect');
            $staffChart->vulnerability_from_others = $request->get('vulnerability_from_others');
            $staffChart->indication_of_physical_emotional_abuse = $request->get('indication_of_physical_emotional_abuse');
            $staffChart->is_full_mobile = $request->get('is_full_mobile');
            $staffChart->use_walking_aid = $request->get('use_walking_aid');
            $staffChart->is_wheel_chair_dependant = $request->get('is_wheel_chair_dependant');
            $staffChart->need_assistance_of_one = $request->get('need_assistance_of_one');
            $staffChart->need_assistance_of_two = $request->get('need_assistance_of_two');
            $staffChart->is_fully_dependant = $request->get('is_fully_dependant');
            $staffChart->has_demantia_frail = $request->get('has_demantia_frail');
            $staffChart->has_high_blood_pressure = $request->get('has_high_blood_pressure');
            $staffChart->has_poor_circulation = $request->get('has_poor_circulation');
            $staffChart->has_cva_tia = $request->get('has_cva_tia');
            $staffChart->has_osteo_rheumatoid_arthritis = $request->get('has_osteo_rheumatoid_arthritis');
            $staffChart->has_osteoporosis = $request->get('has_osteoporosis');
            $staffChart->has_poor_vision = $request->get('has_poor_vision');
            $staffChart->has_poor_hearing = $request->get('has_poor_hearing');
            $staffChart->has_diabetes = $request->get('has_diabetes');
            $staffChart->has_amputee = $request->get('has_amputee');
            $staffChart->has_history_of_falls = $request->get('has_history_of_falls');
            $staffChart->admitted_for_investigation = $request->get('admitted_for_investigation');
            $staffChart->has_catheter = $request->get('has_catheter');
            $staffChart->has_inco_urine = $request->get('has_inco_urine');
            $staffChart->has_inco_faeces = $request->get('has_inco_faeces');
            $staffChart->has_incontinent_doubly = $request->get('has_incontinent_doubly');
            $staffChart->requires_assistance = $request->get('requires_assistance');
            $staffChart->uses_diuretics = $request->get('uses_diuretics');
            $staffChart->uses_sedatives = $request->get('uses_sedatives');
            $staffChart->uses_tranquilisers = $request->get('uses_tranquilisers');
            $staffChart->uses_anti_hypertensive = $request->get('uses_anti_hypertensive');
            $staffChart->uses_reg_aperient = $request->get('uses_reg_aperient');
            $staffChart->uses_hypoglycaemic_agents = $request->get('uses_hypoglycaemic_agents');
            $staffChart->drink_alcohol = $request->get('drink_alcohol');
            $staffChart->resident_height = $request->get('resident_height');
            $staffChart->resident_weight = $request->get('resident_weight');
            $staffChart->info_comprehension = $request->get('info_comprehension');
            $staffChart->info_behavior = $request->get('info_behavior');
            $staffChart->info_disability = $request->get('info_disability');
            $staffChart->handling_pain = $request->get('handling_pain');
            $staffChart->handling_skin = $request->get('handling_skin');
            $staffChart->handling_other = $request->get('handling_other');
            $staffChart->movement_walking = $request->get('movement_walking');
            $staffChart->movement_standing = $request->get('movement_standing');
            $staffChart->movement_using_toilet = $request->get('movement_using_toilet');
            $staffChart->movement_going_to_bed = $request->get('movement_going_to_bed');
            $staffChart->movement_getting_from_bed = $request->get('movement_getting_from_bed');
            $staffChart->movement_on_bed = $request->get('movement_on_bed');
            $staffChart->visit_type = $request->get('visit_type');
            $staffChart->visit_location = $request->get('visit_location');
            $staffChart->visitor_name = $request->get('visitor_name');
            $staffChart->bowel_type = $request->get('bowel_type');
            $staffChart->weight_grams = $request->get('weight_grams');
            $staffChart->weight_stone = $request->get('weight_stone');
            $staffChart->food_period = $request->get('food_period');
            $staffChart->food_type = $request->get('food_type');
            $staffChart->review_type = $request->get('review_type');
            $staffChart->service_user_participant = $request->get('service_user_participant');
            $staffChart->pulse = $request->get('pulse');
            $staffChart->temperature = $request->get('temperature');
            $staffChart->blood_pressure_systolic = $request->get('blood_pressure_systolic');
            $staffChart->blood_pressure_diastolic = $request->get('blood_pressure_diastolic');
            $staffChart->blood_sugar = $request->get('blood_sugar');
            $staffChart->oxygen_saturation = $request->get('oxygen_saturation');
            $staffChart->stool_observed = $request->get('stool_observed');
            $staffChart->respiration = $request->get('respiration');
            $staffChart->antecedent_event = $request->get('antecedent_event');
            $medicine_name =  $request->get('medicine_name');
            if(!empty($medicine_name)){
                $medicineName =  MedicineName::where('medicine_name', $medicine_name)->firs();
                if(empty($medicineName)){
                    return response()->json([
                        'success' => false,
                        'message' => 'Medicine name not found.'
                    ], 400);
                }
                $staffChart->medicine_name =  $request->get('medicine_name');
            }
            $staffChart->medication_type =  $request->get('medication_type');
            $staffChart->medication_unit =  $request->get('medication_unit');
            $staffChart->dosage_morning =  $request->get('dosage_morning');
            $staffChart->dosage_morning_when =  $request->get('dosage_morning_when');
            $staffChart->save();

            return response()->json([
                'success' => true,
                'message' => new StaffChartResource($staffChart)
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
        $staffChart = StaffChart::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new StaffChartResource($staffChart)
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

        StaffChart::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Staff Chart successfully deleted"
        ], Response::HTTP_OK);
    }

    public function findAll(Request $request){
        // And Client Id if available
        // And Staff Id if available
        // Both if two available

        $client_id = $request->get('client_id');
        $staff_id = $request->get('staff_id');
        $limit =  $request->get('limit');

        $start =  new \DateTime($request->get('start_date'));
        $end =  new \DateTime($request->get('end_date'));

        $singleDate = ($start === $end && (!empty($request->get('start_date')) && !empty($request->get('start_date'))) ) ? $start : null;

        if (!empty($singleDate)){
            if (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('client_id', $client_id)
                    ->where('report_date', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if(!empty($staff_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('report_date', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if (!empty($client_id)){
                $res = StaffChart::where('client_id', $client_id)
                    ->where('report_date', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                $res = StaffChart::where('report_date', $singleDate)->orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }elseif (!empty($start) && !empty($end)){
            if (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('client_id', $client_id)
                    ->whereBetween('report_date', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if(!empty($staff_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->whereBetween('report_date', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if (!empty($client_id)){
                $res = StaffChart::where('client_id', $client_id)
                    ->whereBetween('report_date', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                $res = StaffChart::whereBetween('report_date', [$start, $end])->orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }else{

            if (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('client_id', $client_id)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if(!empty($staff_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if (!empty($client_id)){
                $res = StaffChart::where('client_id', $client_id)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                $res = StaffChart::orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }

        return \response()->json([
            'success' => true,
            'message' => new StaffChartCollection($res)
        ], Response::HTTP_OK);
    }

    public function findAllByType(Request $request){
        // Retrieve all data By Type
        // And Client Id if available
        // And Staff Id if available
        // Both if two available

        $client_id = $request->get('client_id');
        $staff_id = $request->get('staff_id');
        $category = $request->get('category');
        $limit =  $request->get('limit');

        $start =  new \DateTime($request->get('start_date'));
        $end =  new \DateTime($request->get('end_date'));

        $singleDate = ($start === $end && (!empty($request->get('start_date')) && !empty($request->get('start_date'))) ) ? $start : null;

        if (!empty($singleDate)){
            if (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('client_id', $client_id)
                    ->where('report_date', $singleDate)
                    ->where('category', $category)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if(!empty($staff_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('report_date', $singleDate)
                    ->where('category', $category)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if (!empty($client_id)){
                $res = StaffChart::where('client_id', $client_id)
                    ->where('category', $category)
                    ->where('report_date', $singleDate)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                $res = StaffChart::where('category', $category)->where('report_date', $singleDate)->orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }elseif (!empty($start) && !empty($end)){
            if (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('client_id', $client_id)
                    ->whereBetween('report_date', [$start, $end])
                    ->where('category', $category)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if(!empty($staff_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->whereBetween('report_date', [$start, $end])
                    ->where('category', $category)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if (!empty($client_id)){
                $res = StaffChart::where('client_id', $client_id)
                    ->whereBetween('report_date', [$start, $end])
                    ->where('category', $category)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                $res = StaffChart::where('category', $category)->whereBetween('report_date', [$start, $end])->orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }else{

            if (!empty($staff_id) && !empty($client_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('client_id', $client_id)
                    ->where('category', $category)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if(!empty($staff_id)){
                $res = StaffChart::where('staff_id', $staff_id)
                    ->where('category', $category)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else if (!empty($client_id)){
                $res = StaffChart::where('client_id', $client_id)
                    ->where('category', $category)
                    ->orderBy('updated_at', 'DESC')
                    ->paginate($limit);
            }else{
                $res = StaffChart::where('category', $category)->orderBy('updated_at', 'DESC')->paginate($limit);
            }
        }

        return \response()->json([
            'success' => true,
            'message' => new StaffChartCollection($res)
        ], Response::HTTP_OK);
    }
}
