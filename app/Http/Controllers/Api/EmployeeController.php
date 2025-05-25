<?php

namespace App\Http\Controllers\Api;

use App\Dto\AccountDto;
use App\Dto\EventType;
use App\Events\NewAccountEvent;
use App\Events\EmployeeEvent;
use App\Models\NotificationSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeCollection;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Resources\EmployeeResource;
use App\Helpers\Helper;
use App\Http\Services\UserService;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Mail\NewEmployeeMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EmployeeController extends Controller
{
    public function staffChart(){
        return \response()->json([
                'success' => true,
                'message' => cache()->get('staff_chart')
            ]
            , ResponseAlias::HTTP_OK);
    }

    public function staffActive(Request $request){

        $staffIdByActive = DB::select( "select DISTINCT emp.id  as status FROM employees AS emp INNER JOIN users AS usr ON emp.user_id = usr.id WHERE usr.active = 1");

        return \response()->json([
                'success' => true,
                'message' => new EmployeeCollection(Employee::whereIn('id', $staffIdByActive)->orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }

    public function staffNotActive(Request $request){

        $staffIdByActive = DB::select( "select DISTINCT emp.id  as status FROM employees AS emp INNER JOIN users AS usr ON emp.user_id = usr.id WHERE usr.active = 1");

        return \response()->json([
                'success' => true,
                'message' => new EmployeeCollection(Employee::whereNotIn('id', $staffIdByActive)->orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }

    public function staffAssigned(Request $request){
        $staffAssigned = DB::select( "select DISTINCT emp.id FROM employees AS emp INNER JOIN staff_assignments AS st ON emp.id = st.staff_id");


        return \response()->json([
                'success' => true,
                'message' => new EmployeeCollection(Employee::whereIn('id', $staffAssigned)->orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }

    public function staffNotAssigned(Request $request){
        $staffAssigned = DB::select( "select DISTINCT emp.id FROM employees AS emp INNER JOIN staff_assignments AS st ON emp.id = st.staff_id");

        return \response()->json([
                'success' => true,
                'message' => new EmployeeCollection(Employee::whereNotIn('id', $staffAssigned)->orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // $user = User::where('active',0)->pluck('id');
        // return response()->json( $user = User::where('active',0)->pluck('id'));
        if(!empty($request->get('limit'))){
            return \response()->json([
                    'success' => true,
                    'message' => new EmployeeCollection(Employee::orderBy('updated_at', 'DESC')->paginate((int) $request->get('limit')))
                    // 'message' => new EmployeeCollection(Employee::orderBy('updated_at', 'DESC')->whereNotIn('user_id',$user)->paginate((int) $request->get('limit')))
                ]
                , ResponseAlias::HTTP_OK);
        }

        return \response()->json([
                'success' => true,
                'message' => new EmployeeCollection(Employee::orderBy('updated_at', 'DESC')->get())
                // 'message' => new EmployeeCollection(Employee::orderBy('updated_at', 'DESC')->whereNotIn('user_id',$user)->get())
            ]
            , ResponseAlias::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexPaged(Request $request)
    {
        $user = User::where('active',0)->pluck('id');
        return \response()->json([
                'success' => true,
                'message' => new EmployeeCollection(Employee::whereNotIn('user_id',$user)->paginate((int) $request->get('limit')))
            ]
            , ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            array_merge($request['role'], ['Registered']);
            // Create new Users
            $userService = new UserService();
            $userResponse = $userService->register($request);

            if(!$userResponse['success']){
                DB::commit();
                return response()->json($userResponse, 400);
            }

            // Create Employee

            $employee = new Employee();
            $latestEmp = Employee::orderBy('created_at','DESC')->first();

            $employee_info = $request->get('employee_info');

            $employee->employee_no = $employee_info['employee_no'];
            $employee->nationality = $employee_info['nationality'];
            $employee->duty_type = $employee_info['duty_type'];
            $employee->national_identification_number = $employee_info['national_identification_number'];
            $employee->department = $employee_info['department'];
            $employee->designation = $employee_info['designation'];
            $employee->bank_account_number =  $employee_info['bank_account_number'];
            $employee->bank_name = $employee_info['bank_name'];
            $employee->date_employed = new \DateTime($employee_info['date_employed']);
            $employee->user_id = (int)$userResponse['message'];
            $employee->save();
            DB::commit();

            $employee = Employee::where('id', $employee->id)->first();
            // Raise Events
            if(!empty($staff)){
                $roles = $request->get('role');
                $roleNotif = '';
                if(!empty($roles)){
                    foreach ($roles as $role){
                        $roleNotif = $roleNotif.''.$role.' - ';
                    }
                }
                $emailNotification = NotificationSettings::where('trigger_name', 'NEW_STAFF')
                    ->where('send_email', 1)
                    ->first();
                
                $smsNotification = NotificationSettings::where('trigger_name', 'NEW_STAFF')
                    ->where('send_sms', 1)
                    ->first();
               
                if ($emailNotification) {
                    
                    $mail = new NewEmployeeMail($employee->name, $employee->email, $userResponse['password'], $request['role']);
                        Helper::sendEmail($familyFriend->email, $mail);
                    
                    
                
                }
                
                if ($smsNotification) {
                    
                
                         $phoneNumber = $employee->user->phone_num;
                         $message = TwilioSMSController::newEmployeeMessage($employee->name, $employee->email, $userResponse['password'], $request['role']);
                        Helper::sendSms($phoneNumber, $message);
                        // \Log::info('SMS is sent');
                   
                }
              
                $account = new AccountDto($employee->user->first_name.' '.$employee->user->last_name, $employee->user->first_name.' '.$employee->user->last_name, null, $employee->user->first_name.' '.$employee->user->last_name, $employee->user->email, $request->get('password'), EventType::NEW_STAFF);
                event(new EmployeeEvent($account, $employee->user->email, $employee->user->id, $employee->user->phone_num, $roleNotif));
            }

            return response()->json([
                'success' => true,
                'message' => $employee->id
            ], ResponseAlias::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseAlias::HTTP_CONFLICT
            );
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $designation = Employee::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => new EmployeeResource($designation)
            ]
            ,
            ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // Retrieve Employee
            $employee_info = $request->get('employee_info');

            $employee = Employee::where('id', $employee_info['id'])->first();
            if(empty($employee)){
                DB::commit();
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found.'
                ], 404);
            }
            // Update user
            $userService = new UserService();
            $userResponse = $userService->update($request);

            if(!$userResponse['success']){
                DB::commit();
                return response()->json($userResponse, 400);
            }

            // Update Employee
            $employee->employee_no = $employee_info['employee_no'];
            $employee->nationality = $employee_info['nationality'];
            $employee->national_identification_number = $employee_info['national_identification_number'];
            $employee->department = $employee_info['department'];
            $employee->designation = $employee_info['designation'];
            $employee->duty_type = $employee_info['duty_type'];
            $employee->bank_account_number =  $employee_info['bank_account_number'];
            $employee->bank_name = $employee_info['bank_name'];
            $employee->date_employed = new \DateTime($employee_info['date_employed']);
            $employee->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $employee
            ], ResponseAlias::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseAlias::HTTP_CONFLICT
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'id' => 'required|integer'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'id not found'
                ], ResponseAlias::HTTP_BAD_REQUEST);
            }
            DB::beginTransaction();
            $employee  =  Employee::where('id', $request->get('id'))->first();
            User::where('id', $employee->user_id)->delete();

            Employee::where('id', $request->get('id'))->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Employee successfully deleted"
            ], ResponseAlias::HTTP_OK);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], ResponseAlias::HTTP_OK);
        }
    }
    
     public function reactivate(Request $request)
    {
        try {
            //Validate data
            $validator = Validator::make($request->only('id'), [
                'id' => 'required|integer',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return \response()->json(
                    [
                        'success' => false,
                        'message' => 'Employee id not found.'
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );
            }

            $client = Employee::where('id','=',$request->id)->first();
            if(empty($client)){
                return \response()->json(
                    [
                        'success' => false,
                        'message' => 'Employee not found.'
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );
            }
            User::where('id','=', $client->user_id)->update(
                [
                    'active' => 1
                ]
            );

            $user = User::where('id', $client->user_id)->first();

            // Raise Events
            $account = new AccountDto($user->first_name.' '.$user->last_name, null, null, $user->first_name.' '.$user->last_name, $user->email, null, EventType::ACCOUNT_ACTIVATED);
            event(new NewAccountEvent($account, $user->email, $user->id, $user->phone_num));

            return \response()->json(
                [
                    'success' => true,
                    'message' => "Account activated successfully."
                ], ResponseAlias::HTTP_OK
            );
        }catch (Exception $e){
            Log::error($e->getMessage());
            return \response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseAlias::HTTP_OK
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deactivate(Request $request)
    {
        try {
            //Validate data
            $validator = Validator::make($request->only('id'), [
                'id' => 'required|integer',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return \response()->json(
                    [
                        'success' => false,
                        'message' => 'Employee id not found.'
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );
            }

            $client = Employee::where('id','=',$request->id)->first();
            if(empty($client)){
                return \response()->json(
                    [
                        'success' => false,
                        'message' => 'Employee not found.'
                    ], ResponseAlias::HTTP_BAD_REQUEST
                );
            }
            User::where('id','=', $client->user_id)->update(
                [
                    'active' => 0
                ]
            );

            $user = User::where('id', $client->user_id)->first();
DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->delete();
            // Raise Events
            $account = new AccountDto($user->first_name.' '.$user->last_name, null, null, $user->first_name.' '.$user->last_name, $user->email, null, EventType::ACCOUNT_DEACTIVATED);
            event(new NewAccountEvent($account, $user->email, $user->id, $user->phone_num));

            return \response()->json(
                [
                    'success' => true,
                    'message' => "Account deactivated successfully."
                ], ResponseAlias::HTTP_OK
            );
        }catch (Exception $e){
            Log::error($e->getMessage());
            return \response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], ResponseAlias::HTTP_OK
            );
        }
    }

}
