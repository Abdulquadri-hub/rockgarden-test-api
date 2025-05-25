<?php

namespace App\Http\Controllers\Api;

use App\Dto\ApplicationDto;
use App\Dto\EventType;
use App\Events\ApplicationApprovedEvent;
use App\Events\ApplicationEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceApplicationCollection;
use App\Http\Resources\ServiceApplicationResource;
use App\Http\Services\UserService;
use App\Models\Client;
use App\Models\SystemContacts;
use App\Helpers\Helper;
use App\Models\ServiceApplication;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\User;
use App\Models\NotificationSettings;
use App\Mail\ApplicationRejectedMail;
use App\Mail\ApplicationApprovedMail;
use App\Mail\NewApplicationMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ServiceApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::all())
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
        $applicant_id = $request->get('applicant_id');
        if(!empty($applicant_id)){
            return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::where('applicant_id', $applicant_id)->orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
        }
        return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexByStatusPaged(Request $request)
    {
        $applicant_id = $request->get('applicant_id');
        if(!empty($applicant_id)){
            return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::where('applicant_id', $applicant_id)->where('status', $request->get('status'))->orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
        }
        return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::where('status', $request->get('status'))->orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexByPlanAndStatusPaged(Request $request)
    {
        $applicant_id = $request->get('applicant_id');
        if(!empty($applicant_id)){
            return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::where('applicant_id', $applicant_id)->where('plan_name', $request->get('plan_name'))->where('status', $request->get('status'))->orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
        }
        return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::where('plan_name', $request->get('plan_name'))->where('status', $request->get('status'))->orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexByPlanPaged(Request $request)
    {
        $applicant_id = $request->get('applicant_id');
        if(!empty($applicant_id)){
            return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::where('applicant_id', $applicant_id)->where('plan_name', $request->get('plan_name'))->orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
        }
        return \response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationCollection(ServiceApplication::where('plan_name', $request->get('plan_name'))->orderBy('created_at', 'DESC')->paginate((int) $request->get('limit')))
            ], Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only('applicant_id'),[
                'applicant_id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Invalid Service Application data.'
                ], 400);
            }

            // Check Client Existence
            $user =  User::where('id', $request->get('applicant_id'))->first();
            if(empty($user)){
                return response()->json([
                    'success' => false,
                    'message' => 'Applicant not found.'
                ], 400);
            }

            // Create new Service Application
            $serviceApplication = new ServiceApplication();
            $serviceApplication->plan_name = $request->plan_name;
            $serviceApplication->client_last_name = $request->client_last_name;
            $serviceApplication->client_first_name = $request->client_first_name;
            $serviceApplication->client_middle_name = $request->client_middle_name;
            $serviceApplication->client_gender = $request->client_gender;
            $serviceApplication->client_date_of_birth = $request->client_date_of_birth;
            $serviceApplication->client_home_address = $request->client_home_address;
            $serviceApplication->client_office_address = $request->client_office_address;
            $serviceApplication->client_phone_number = $request->client_phone_number;
            $serviceApplication->client_email = $request->client_email;
            $serviceApplication->applicant_id = $request->applicant_id;
            $serviceApplication->phone_number_payee = $request->phone_number_payee;
            $serviceApplication->phone_number_next_of_kin = $request->phone_number_next_of_kin;
            $serviceApplication->primary_language_spoken = $request->primary_language_spoken;
            $serviceApplication->receiving_service_elsewhere = $request->receiving_service_elsewhere;
            $serviceApplication->home_settings_description = $request->home_settings_description;
            $serviceApplication->require_general_healthcare = $request->require_general_healthcare;
            $serviceApplication->require_mobility_assistance = $request->require_mobility_assistance;
            $serviceApplication->require_personal_supervision = $request->require_personal_supervision;
            $serviceApplication->require_emotional_support = $request->require_emotional_support;
            $serviceApplication->require_demantia_care = $request->require_demantia_care;
            $serviceApplication->require_grocery_shopping_assistance = $request->require_grocery_shopping_assistance;
            $serviceApplication->require_feeding_assistance = $request->require_feeding_assistance;
            $serviceApplication->require_haircare_nailcare_assistance = $request->require_haircare_nailcare_assistance;
            $serviceApplication->require_bathing_grooming_assistance = $request->require_bathing_grooming_assistance;
            $serviceApplication->require_dishes_laundry_assistance = $request->require_dishes_laundry_assistance;
            $serviceApplication->require_meal_prep_assistance = $request->require_meal_prep_assistance;
            $serviceApplication->require_toileting_assistance = $request->require_toileting_assistance;
            $serviceApplication->require_health_monitoring = $request->require_health_monitoring;
            $serviceApplication->require_vital_signs_monitoring = $request->require_vital_signs_monitoring;
            $serviceApplication->require_oral_skin_medication = $request->require_oral_skin_medication;
            $serviceApplication->require_injections = $request->require_injections;
            $serviceApplication->require_dressing_of_wounds = $request->require_dressing_of_wounds;
            $serviceApplication->require_oxygen_therapy = $request->require_oxygen_therapy;
            $serviceApplication->require_exercise_oral_feeding = $request->require_exercise_oral_feeding;
            $serviceApplication->require_ng_tube_feeding = $request->require_ng_tube_feeding;
            $serviceApplication->require_post_surgical_management = $request->require_post_surgical_management;
            $serviceApplication->require_companionship = $request->require_companionship;
            $serviceApplication->require_appointment_reminder = $request->require_appointment_reminder;
            $serviceApplication->require_patient_recovery_monitoring = $request->require_patient_recovery_monitoring;
            $serviceApplication->require_improvement_suggestions = $request->require_improvement_suggestions;
            $serviceApplication->require_improvement_advice = $request->require_improvement_advice;
            $serviceApplication->require_steady_availability_for_questions = $request->require_steady_availability_for_questions;
            $serviceApplication->require_highly_skilled_nursing = $request->require_highly_skilled_nursing;
            $serviceApplication->require_other_skilled_nursing = $request->require_other_skilled_nursing;
            $serviceApplication->require_other_assistance = $request->require_other_assistance;
            $serviceApplication->other_assistance_description = $request->other_assistance_description;
            $serviceApplication->main_source_of_finance = $request->main_source_of_finance;
            $serviceApplication->has_history_of_urinary_incontinence = $request->has_history_of_urinary_incontinence;
            $serviceApplication->has_history_of_feacal_incontinence = $request->has_history_of_feacal_incontinence;
            $serviceApplication->number_of_falls_past_12months = $request->number_of_falls_past_12months;
            $serviceApplication->has_diabetes = $request->has_diabetes;
            $serviceApplication->has_hypertension = $request->has_hypertension;
            $serviceApplication->has_hearing_impairment = $request->has_hearing_impairment;
            $serviceApplication->has_dental_problem = $request->has_dental_problem;
            $serviceApplication->has_stroke_tia = $request->has_stroke_tia;
            $serviceApplication->has_sleep_problem = $request->has_sleep_problem;
            $serviceApplication->has_arthritis = $request->has_arthritis;
            $serviceApplication->has_difficulty_moving_around = $request->has_difficulty_moving_around;
            $serviceApplication->has_blindness_or_partial = $request->has_blindness_or_partial;
            $serviceApplication->has_congestive_heart_failure = $request->has_congestive_heart_failure;
            $serviceApplication->has_history_of_demantia = $request->has_history_of_demantia;
            $serviceApplication->has_history_of_mental_illness= $request->has_history_of_mental_illness;
            $serviceApplication->has_cancer_or_terminal_illness = $request->has_cancer_or_terminal_illness;
            $serviceApplication->other_health_problems = $request->other_health_problems;
            $serviceApplication->admissions_in_last_1year = $request->admissions_in_last_1year;
            $serviceApplication->past_medical_surgical_history = $request->past_medical_surgical_history;
            $serviceApplication->all_current_medications = $request->all_current_medications;
            $serviceApplication->known_allergies = $request->known_allergies;
            $serviceApplication->weight_kg = $request->weight_kg;
            $serviceApplication->height_ft = $request->height_ft;
            $serviceApplication->build_slim_or_plum = $request->build_slim_or_plum;
            $serviceApplication->latest_blood_pressure = $request->latest_blood_pressure;
            $serviceApplication->latest_fasting_blood_sugar = $request->latest_fasting_blood_sugar;
            $serviceApplication->hiv_status = $request->hiv_status;
            $serviceApplication->hbsag_hcv_status = $request->hbsag_hcv_status;
            $serviceApplication->all_relevant_diagnosis = $request->all_relevant_diagnosis;
            $serviceApplication->signature = $request->signature;
            $serviceApplication->send_all_correspondence_to_applicant = $request->send_all_correspondence_to_applicant;
            $serviceApplication->fullname_next_of_kin = $request->get('fullname_next_of_kin');
            $serviceApplication->fullname_signatory = $request->get('fullname_signatory');
            $serviceApplication->require_basic_food_preparation = $request->get('require_basic_food_preparation');
            $serviceApplication->client_state = $request->get('client_state');
            $serviceApplication->client_city = $request->get('client_city');
            $serviceApplication->disapproval_reason = $request->get('disapproval_reason');
            $serviceApplication->applying_for_self = $request->get('applying_for_self');
            $serviceApplication->relation_next_of_kin = $request->get('relation_next_of_kin');
            $serviceApplication->status = 'PENDING';
            $serviceApplication->save();
             
           
                   $emailNotifications = NotificationSettings::where('trigger_name', 'APPLICATION_NEW')
                ->where('send_email', 1)
                ->get();

              $smsNotifications = NotificationSettings::where('trigger_name', 'APPLICATION_NEW')
                ->where('send_sms', 1)
                ->get();
           
                $systemContacts = SystemContacts::all();

                foreach ($systemContacts as $systemContact) {
                    $systemContactEmails = explode(',', $systemContact->email);
                    $systemContactPhoneNumbers = explode(',', $systemContact->phone_number);
            
                    $notificationSettings = NotificationSettings::where('system_contact_id', $systemContact->id)
                        ->where('trigger_name', 'APPLICATION_NEW')
                        ->get();
            
                    foreach ($notificationSettings as $notificationSetting) {
                         $name = $user->first_name . " " . $user->last_name;
                        $url = 'https://admin.rockgardenehr.com';
                        
                        if ($notificationSetting->send_email) {
                            foreach ($systemContactEmails as $email) {
                                $mail = new NewApplicationMail($name, $url);
                                Helper::sendEmail($email, $mail);
                            }
                        }
            
                        if ($notificationSetting->send_sms) {
                            
                            foreach ($systemContactPhoneNumbers as $phoneNumber) {
                                $message = TwilioSMSController::newApplicationMessage($name, $url);
                                Helper::sendSms($phoneNumber, $message);
                            }
            // \Log::info('Right now im here');
                        }
                    }
                }
            // Raise Events
            // $applicationDto = new ApplicationDto($user->first_name.' '.$user->last_name, null, null,EventType::APPLICATION_NEW);
            // event(new ApplicationEvent($applicationDto, $user->email, $user->id, $user->phone_num));

            return response()->json([
                'success'=> true,
                'message'=> $serviceApplication->id
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
            $validator = Validator::make($request->only('id', 'status'),[
                'id' => 'required|integer',
                'status' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Invalid Service Application data.'
                ], 400);
            }

            // Check Application Existence
            $serviceApplication =  ServiceApplication::where('id', $request->get('id'))->first();
            if(empty($serviceApplication)){
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 400);
            }

            // Update Service Application
            $serviceApplication->plan_name = $request->plan_name;
            $serviceApplication->client_last_name = $request->client_last_name;
            $serviceApplication->client_first_name = $request->client_first_name;
            $serviceApplication->client_middle_name = $request->client_middle_name;
            $serviceApplication->client_gender = $request->client_gender;
            $serviceApplication->client_date_of_birth = $request->client_date_of_birth;
            $serviceApplication->client_home_address = $request->client_home_address;
            $serviceApplication->client_office_address = $request->client_office_address;
            $serviceApplication->client_phone_number = $request->client_phone_number;
            $serviceApplication->client_email = $request->client_email;
            $serviceApplication->applicant_id = $request->applicant_id;
            $serviceApplication->phone_number_payee = $request->phone_number_payee;
            $serviceApplication->phone_number_next_of_kin = $request->phone_number_next_of_kin;
            $serviceApplication->primary_language_spoken = $request->primary_language_spoken;
            $serviceApplication->receiving_service_elsewhere = $request->receiving_service_elsewhere;
            $serviceApplication->home_settings_description = $request->home_settings_description;
            $serviceApplication->require_general_healthcare = $request->require_general_healthcare;
            $serviceApplication->require_mobility_assistance = $request->require_mobility_assistance;
            $serviceApplication->require_personal_supervision = $request->require_personal_supervision;
            $serviceApplication->require_emotional_support = $request->require_emotional_support;
            $serviceApplication->require_demantia_care = $request->require_demantia_care;
            $serviceApplication->require_grocery_shopping_assistance = $request->require_grocery_shopping_assistance;
            $serviceApplication->require_feeding_assistance = $request->require_feeding_assistance;
            $serviceApplication->require_haircare_nailcare_assistance = $request->require_haircare_nailcare_assistance;
            $serviceApplication->require_bathing_grooming_assistance = $request->require_bathing_grooming_assistance;
            $serviceApplication->require_dishes_laundry_assistance = $request->require_dishes_laundry_assistance;
            $serviceApplication->require_meal_prep_assistance = $request->require_meal_prep_assistance;
            $serviceApplication->require_toileting_assistance = $request->require_toileting_assistance;
            $serviceApplication->require_health_monitoring = $request->require_health_monitoring;
            $serviceApplication->require_vital_signs_monitoring = $request->require_vital_signs_monitoring;
            $serviceApplication->require_oral_skin_medication = $request->require_oral_skin_medication;
            $serviceApplication->require_injections = $request->require_injections;
            $serviceApplication->require_dressing_of_wounds = $request->require_dressing_of_wounds;
            $serviceApplication->require_oxygen_therapy = $request->require_oxygen_therapy;
            $serviceApplication->require_exercise_oral_feeding = $request->require_exercise_oral_feeding;
            $serviceApplication->require_ng_tube_feeding = $request->require_ng_tube_feeding;
            $serviceApplication->require_post_surgical_management = $request->require_post_surgical_management;
            $serviceApplication->require_companionship = $request->require_companionship;
            $serviceApplication->require_appointment_reminder = $request->require_appointment_reminder;
            $serviceApplication->require_patient_recovery_monitoring = $request->require_patient_recovery_monitoring;
            $serviceApplication->require_improvement_suggestions = $request->require_improvement_suggestions;
            $serviceApplication->require_improvement_advice = $request->require_improvement_advice;
            $serviceApplication->require_steady_availability_for_questions = $request->require_steady_availability_for_questions;
            $serviceApplication->require_highly_skilled_nursing = $request->require_highly_skilled_nursing;
            $serviceApplication->require_other_skilled_nursing = $request->require_other_skilled_nursing;
            $serviceApplication->require_other_assistance = $request->require_other_assistance;
            $serviceApplication->other_assistance_description = $request->other_assistance_description;
            $serviceApplication->main_source_of_finance = $request->main_source_of_finance;
            $serviceApplication->has_history_of_urinary_incontinence = $request->has_history_of_urinary_incontinence;
            $serviceApplication->has_history_of_feacal_incontinence = $request->has_history_of_feacal_incontinence;
            $serviceApplication->number_of_falls_past_12months = $request->number_of_falls_past_12months;
            $serviceApplication->has_diabetes = $request->has_diabetes;
            $serviceApplication->has_hypertension = $request->has_hypertension;
            $serviceApplication->has_hearing_impairment = $request->has_hearing_impairment;
            $serviceApplication->has_dental_problem = $request->has_dental_problem;
            $serviceApplication->has_stroke_tia = $request->has_stroke_tia;
            $serviceApplication->has_sleep_problem = $request->has_sleep_problem;
            $serviceApplication->has_arthritis = $request->has_arthritis;
            $serviceApplication->has_difficulty_moving_around = $request->has_difficulty_moving_around;
            $serviceApplication->has_blindness_or_partial = $request->has_blindness_or_partial;
            $serviceApplication->has_congestive_heart_failure = $request->has_congestive_heart_failure;
            $serviceApplication->has_history_of_demantia = $request->has_history_of_demantia;
            $serviceApplication->has_history_of_mental_illness= $request->has_history_of_mental_illness;
            $serviceApplication->has_cancer_or_terminal_illness = $request->has_cancer_or_terminal_illness;
            $serviceApplication->other_health_problems = $request->other_health_problems;
            $serviceApplication->admissions_in_last_1year = $request->admissions_in_last_1year;
            $serviceApplication->past_medical_surgical_history = $request->past_medical_surgical_history;
            $serviceApplication->all_current_medications = $request->all_current_medications;
            $serviceApplication->known_allergies = $request->known_allergies;
            $serviceApplication->weight_kg = $request->weight_kg;
            $serviceApplication->height_ft = $request->height_ft;
            $serviceApplication->build_slim_or_plum = $request->build_slim_or_plum;
            $serviceApplication->latest_blood_pressure = $request->latest_blood_pressure;
            $serviceApplication->latest_fasting_blood_sugar = $request->latest_fasting_blood_sugar;
            $serviceApplication->hiv_status = $request->hiv_status;
            $serviceApplication->hbsag_hcv_status = $request->hbsag_hcv_status;
            $serviceApplication->all_relevant_diagnosis = $request->all_relevant_diagnosis;
            $serviceApplication->signature = $request->signature;
            $serviceApplication->send_all_correspondence_to_applicant = $request->send_all_correspondence_to_applicant;
            $serviceApplication->fullname_next_of_kin = $request->get('fullname_next_of_kin');
            $serviceApplication->fullname_signatory = $request->get('fullname_signatory');
            $serviceApplication->require_basic_food_preparation = $request->get('require_basic_food_preparation');
            $serviceApplication->client_state = $request->get('client_state');
            $serviceApplication->client_city = $request->get('client_city');
            $serviceApplication->disapproval_reason = $request->get('disapproval_reason');
            $serviceApplication->applying_for_self = $request->get('applying_for_self');
            $serviceApplication->relation_next_of_kin = $request->get('relation_next_of_kin');
            $serviceApplication->save();
            return response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationResource($serviceApplication)
            ], 200);
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

    public function approve(Request $request){
        try {
            $validator = Validator::make($request->only('id'),[
                'id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Invalid Service Application data.'
                ], 400);
            }

            // Check Application Existence
            $serviceApplication =  ServiceApplication::where('id', $request->get('id'))->first();
            
                
            if(empty($serviceApplication)){
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 400);
            }

            if($serviceApplication->status !== 'PENDING') {
                return response()->json([
                    'success' => false,
                    'message' => 'Application status invalid.'
                ], 400);
            }

            $user =  null;
            $client =  null;

            $userService = new UserService();
            // Get existing user for this application
            // and also retrieve client if exist or create new one

            if($serviceApplication->applying_for_self){
                $user = User::where('id', $serviceApplication->applicant_id)->first();
                if(empty($user)){
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found in case of self application.'
                    ], 400);
                }
                $client =  Client::where('user_id', $user->id)->first();
                if (empty($client)){
                    $client = new Client();
                    $latestClt = Client::orderBy('created_at','DESC')->first();
                    $client->client_no = 'CLT'.$userService->generateCode($latestClt === null ? 1 : ($latestClt->id + 1));
                    $client->user_id = $user->id;
                    $client->save();
                }
            }else{

                $request['first_name'] = $serviceApplication->client_first_name;
                $request['last_name'] = $serviceApplication->client_last_name;
                $request['middle_name'] = $serviceApplication->client_middle_name;
                $request['gender'] = $serviceApplication->client_gender;
                $request['date_of_birth'] = $serviceApplication->client_date_of_birth;
                $request['home_address'] = $serviceApplication->client_home_address;
                $request['office_address'] = $serviceApplication->client_office_address;
                $request['phone_num'] = $serviceApplication->client_phone_number;
                $request['email'] = $serviceApplication->client_email;
                $request['state'] = $serviceApplication->client_state;
                $request['city'] = $serviceApplication->client_city;
                $request['role'] = ['Client'];

                $res =  $userService->register($request);

                if(!$res['success']){
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not create new user for the application.'
                    ], 400);
                }
                $client = new Client();
                $latestClt = Client::orderBy('created_at','DESC')->first();
                $client->client_no = 'CLT'.$userService->generateCode($latestClt === null ? 1 : ($latestClt->id + 1));
                $client->user_id = (int) $res['message'];
                $client->save();

               
                }

            

            // Update Service Application
            $serviceApplication->status = 'APPROVED';
            $serviceApplication->client->id =  $client->id;
            $serviceApplication->disapproval_reason = null;
            $serviceApplication->date_approved = new \DateTime('now');
            $serviceApplication->save();
            
            if(!empty($client)){
                 // Raise Events
                // Raise Events
            $emailNotification = NotificationSettings::where('trigger_name', 'APPLICATION_APPROVED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'APPLICATION_APPROVED')
                    ->where('send_sms', 1)
                    ->first();
               $name = $serviceApplication->client_first_name . " " . $serviceApplication->client_last_name;
                $email = $serviceApplication->client_email;
               $phone = $serviceApplication->client_phone_number;
                if ($emailNotification) {
                    $mail = new ApplicationApprovedMail($name);
                    
                    Helper::sendEmail($email, $mail);
                    
                
                }
                
                if ($smsNotification) {
                    $message = TwilioSMSController::applicationApprovedMessage($name);
                
                    Helper::sendSms($phone, $message);
                }
                   
                
            }
            return response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationResource($serviceApplication)
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
    public function reject(Request $request){
        try {
            $validator = Validator::make($request->only('id', 'disapproval_reason'),[
                'id' => 'required|integer',
                'disapproval_reason' => 'required|string'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Invalid Service Application data.'
                ], 400);
            }

            // Check Application Existence
            $serviceApplication =  ServiceApplication::where('id', $request->get('id'))->first();
            if(empty($serviceApplication)){
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 400);
            }

            if($serviceApplication->status !== 'PENDING') {
                return response()->json([
                    'success' => false,
                    'message' => 'Application status invalid.'
                ], 400);
            }
            // Update Service Application
            $serviceApplication->status = 'DISAPPROVED';
            $serviceApplication->is_approved = true;
            $serviceApplication->disapproval_reason = $request->get('disapproval_reason');
            $serviceApplication->save();

            // Raise Events
            // $user = User::where('id', $serviceApplication->applicant_id)->first();
             
            $emailNotification = NotificationSettings::where('trigger_name', 'APPLICATION_REJECTED')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'APPLICATION_REJECTED')
                    ->where('send_sms', 1)
                    ->first();
               $name = $serviceApplication->client_first_name . " " . $serviceApplication->client_last_name;
                $email = $serviceApplication->client_email;
               $phone = $serviceApplication->client_phone_number;
               $reason=$serviceApplication->disapproval_reason;
                if ($emailNotification) {
                    $mail = new ApplicationRejectedMail($name,$reason);
                    
                    Helper::sendEmail($email, $mail);
                    
                
                }
                
                if ($smsNotification) {
                    $message = TwilioSMSController::applicationRejectedMessage($name,$reason);
                
                    Helper::sendSms($phone, $message);
                }
                // $applicationDto = new ApplicationDto($name, null, $request->get('disapproval_reason'),EventType::APPLICATION_REJECTED);
                // event(new ApplicationApprovedEvent($applicationDto, $email, $user->id, $user->phone_num));
             

            

            return response()->json([
                'success'=> true,
                'message'=> new ServiceApplicationResource($serviceApplication)
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
        $serviceApplication = ServiceApplication::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new ServiceApplicationResource($serviceApplication)
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

        ServiceApplication::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "Service Application successfully deleted"
        ], Response::HTTP_OK);
    }
}
