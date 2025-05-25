<?php

namespace App\Http\Controllers\Api;

use App\Dto\AccountDto;
use App\Dto\EventType;
use App\Dto\ResetPasswordDto;
use App\Events\NewAccountEvent;
use App\Events\PasswordResetEvent;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserResource;
use App\Mail\NewAccountMail;

use App\Mail\ResetPasswordMail; 
use App\Mail\SuccessRegistrationMail; 
use App\Models\Attendance;
use App\Models\Client;
use App\Models\Employee;
use App\Models\FamilyFriendAssignment;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\PayRun;
use App\Models\Receipt;
use App\Models\Rota;
use App\Models\SaleOrder;
use App\Models\StaffAssignment;
use App\Models\StaffChart;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Gmail;
use App\Helpers\Helper;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Models\NotificationSettings;
use App\Mail\NewAccountByAdminMail;
use App\Http\Resources\StaffAssignmentAllCollection;
use App\Http\Resources\FriendFamilyAssignmentResource;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

class ApiController extends BaseController
{
    protected $maxAttempts = 3; // Default is 5
    protected $decayMinutes = 2; // Default is 1

    use AuthenticatesUsers;

    protected $response;

    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('first_name', 'last_name', 'middle_name', 'gender', 'phone_number', 'state_of_origin', 'home_address', 'state', 'city', 'email', 'password');
        $validator = Validator::make($data, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        // $emailValidator = Validator::make($request->only('email'), [
        //     'email' => 'required|email|unique:users'
        // ]);

        // if ($emailValidator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Email already in use.'
        //     ], 404);
        // }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Required field cannot be blank.'
            ], 404);
        }

        $otp = rand(1000,9999);

        // $mail_details = [
        //     'title' => 'Testing Application OTP',
        //     'body' => 'Your OTP is : '. $otp
        // ];
        //Mail::to($request->email)->send(new Gmail($mail_details));
        
        $user = User::where('email', '=', $request->email)->first();
        
        if($user){
            if($user->is_verified == 1){
                return response()->json([
                    'success' => false,
                    'message' => 'Email already in use.'
                ], 404);
            }
            User::where('email','=',$request->email)->update(['otp' => $otp]);
        }else{
            //Request is valid, create new user
            $user = User::create([
            	'first_name' => $request->first_name,
            	'last_name' => $request->last_name,
            	'middle_name' => $request->middle_name,
            	'gender' => $request->gender,
            	'date_of_birth' => $request->date_of_birth,
            	'home_address' => $request->home_addres,
            	'office_addres' => $request->office_addres,
            	'city' => $request->city,
            	'state' => $request->state,
            	'phone_num' => $request->phone_number,
            	'email' => $request->email,
            	'otp' => $otp,
            	'password' => bcrypt($request->password),
            ]);
            $user->assignRole('Registered');
        }
       // $userId = User::where('email', '=', $request->email)->select('id')->first();
        //DB::table('role_user')->insert(['role_id'=>3, 'user_id' => $userId['id']]);

        // Send Email to new user

//        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
//            Mail::to($request->email)->send(new NewAccountMail($user->first_name.' '.$user->last_name, $userId->otp));
//        }
//        // Send Sms to user
//        TwilioSMSController::sendSMS($user->phone_num, TwilioSMSController::newAccountMessage($user->first_name.' '.$user->last_name, $user->otp));

        // Raise Events
        // $account = new AccountDto(null, $user->first_name.' '.$user->last_name, $user->otp, null, $user->email, null, EventType::NEW_ACCOUNT);
        // event(new NewAccountEvent($account, $user->email, $user->id, $user->phone_num));
        
        Mail::to($user->email)->send(new NewAccountMail($user->first_name, $otp));
        $admin=User::where('is_admin','1')->get();
        if($admin){
            // Send email notification to the new user
        // $newUser = $response['data']['user'];
        $emailNotification = NotificationSettings::where('trigger_name', 'ACCOUNT_BY_ADMIN')->where('send_email', 1)->first();
        $fullname=$user->first_name.' '.$user->last_name;
        if ($emailNotification) {
            $mail = new NewAccountByAdminMail($fullname,$user->email, $user->password);
            Mail::to($user->email)->send(new NewAccountMail($user->first_name, $otp));
            Helper::sendEmail($user->email, $mail);
        }

        // Send SMS notification to the new user
        $smsNotification = NotificationSettings::where('trigger_name', 'ACCOUNT_BY_ADMIN')->where('send_sms', 1)->first();
        if ($smsNotification) {
            $message = TwilioSMSController::newAccountByAdminMessage($fullname,$user->email, $user->password);
            Helper::sendSms($user->phone_num, $message);
        }
        //Send welcome message to Staff,
        
        $emailNotification = NotificationSettings::where('trigger_name', 'NEW_STAFF')->where('send_email', 1)->first();
        $fullname=$user->first_name.' '.$user->last_name;
        if ($emailNotification) {
            $mail = new SuccessRegistrationMail($fullname);
            Helper::sendEmail($user->email, $mail);
        }

        
        $smsNotification = NotificationSettings::where('trigger_name', 'NEW_STAFF')->where('send_sms', 1)->first();
        if ($smsNotification) {
            $message = TwilioSMSController::successRegistrationMessage($fullname);
            Helper::sendSms($user->phone_num, $message);
        }

        }

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => $user->id
        ], Response::HTTP_OK);
    }

    public function verifyOtp(Request $request){

        $user = User::where('email', '=', $request->email)->first();
        if($user && $user->otp != $request->otp) {
            return response(['success'=>false, 'message' => 'Invalid OTP']);
        }
        if($user){
            User::where('email','=',$request->email)->update(['otp' => 'null', 'is_verified' => 1]);
            if($user->is_verified == 1){
                return response()->json([
                    'success'=> true,
                    'message'=> $user
                ]);
            }
            $emailNotification = NotificationSettings::where('trigger_name', 'NEW_ACCOUNT')->where('send_email', 1)->first();
        $fullname=$user->first_name.' '.$user->last_name;
        if ($emailNotification) {
            $mail = new SuccessRegistrationMail($fullname);
            Helper::sendEmail($user->email, $mail);
        }

        // Send SMS notification to the new user
        $smsNotification = NotificationSettings::where('trigger_name', 'NEW_ACCOUNT')->where('send_sms', 1)->first();
        if ($smsNotification) {
            $message = TwilioSMSController::successRegistrationMessage($fullname);
            Helper::sendSms($user->phone_num, $message);
        }
        
                // Mail::to($user->email)->send(new SuccessRegistrationMail($user->first_name));
                
            return response(['success'=> true, "message" => $user]);
        }
        else{

            return response(['success'=>false, 'message' => 'Account does not exist']);
        }

    }

    public function authenticate_(Request $request){
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.'
            ], 404);
        }
        $credentials['is_verified'] = 1;

        $myTTL = 60*24*30; //minutes

        JWTAuth::factory()->setTTL($myTTL);
        $token = JWTAuth::attempt($credentials);
        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid login credentials.'
                ], 404);
            }
        } catch (JWTException $e) {
            return $credentials;
                return response()->json([
                        'success' => false,
                        'message' => 'Could not create token.',
                ], 500);
        }

        $user = User::where('email', $request->email)->first();
        $userId = User::where('email', $request->email)->select('id')->first();
        $roleId = DB::table('role_user')->where('user_id', $userId['id'])
                                        ->join('roles', 'roles.id', '=', 'role_user.role_id')
                                        ->select('roles.id')
                                        ->first();

 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'message' => ['user'=> $user,'token'=> $token, 'user_role'=> $roleId]
            // 'token' => $token,
            // 'user_role' => $roleId
        ]);
    }

    public function authenticate(Request $request){
       $credentials = $request->only('email', 'password');

       //valid credential
       $validator = Validator::make($credentials, [
           'email' => 'required|email',
           'password' => 'required|string'
       ]);
       
       

       //Send failed response if request is not valid
       if ($validator->fails()) {
           return response()->json([
               'success' => false,
               'message' => 'Invalid login credentials.'
           ], 404);
       }
       
       //$credentials['is_verified'] = 1;

       if (Auth::guard('web')->attempt($credentials)) {
           
           $token = Auth::guard('web')->user()->createToken('LaravelAuthApp')->accessToken;
           $user = User::find(Auth::guard('web')->user()->id);
           
           //$roles = $user->getRoleNames();
           $roles = $user->roles()->with('permissions')->get();
    
           $isAdmin = false;
           $appRoles = [ 'Registered', 'Client', 'Care Giver',  'Nurse',  'Nurse Assistant', 'Physiotherapist', 'Doctor', 'Trainee (Male)'];

           foreach ($roles as $role){
               if (in_array($role->name, $appRoles)){
                   $isAdmin = false;
                   break;
               }else{
                   $isAdmin =  true;
               }
           }

           if($isAdmin){
               
               $this->incrementLoginAttempts($request);
               return $this->sendError('Invalid login credentials or access denied.',[],0);
           }else{
               $client =  Client::where('user_id',  Auth::guard('web')->user()->id)->first();
               $employee =  Employee::where('user_id',  Auth::guard('web')->user()->id)->first();
               $attendance = null;
               if(!empty($employee)){
                   $attendance = Attendance::where('staff_id', $employee->id)->orderBy('created_at', 'DESC')->first();
               }

               return response()->json([
                   'success' => true,
                   'message' => ['attendance' => $attendance, 'client' => $client, 'staff'=> $employee,'user'=> Auth::guard('web')->user(),'token'=> $token,'roles'=>$roles->toArray()]
               ]);
           }

       } else {

           $this->incrementLoginAttempts($request);
           return $this->sendError('Invalid login credentials.',[],0);
       }

    }

     public function authenticateAdmin(Request $request){
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.'
            ], 404);
        }
        //$credentials['is_verified'] = 1;

        if (Auth::guard('web')->attempt($credentials)) {
            
            $token = Auth::guard('web')->user()->createToken('LaravelAuthApp')->accessToken;
            $user = User::find(Auth::guard('web')->user()->id);
            //$roles = $user->getRoleNames();
            $roles = $user->roles()->with('permissions')->get();
            
            $isAdmin = false;
            

            $appRoles = [ 'Registered', 'Client', 'Care Giver',  'Nurse',  'Nurse Assistant', 'Physiotherapist', 'Doctor'];

            foreach ($roles as $role){
                if (!in_array($role->name, $appRoles)){
                    $isAdmin =  true;
                    break;
                }
            }

            if($isAdmin){
                $client =  Client::where('user_id',  Auth::guard('web')->user()->id)->first();
                $employee =  Employee::where('user_id',  Auth::guard('web')->user()->id)->first();
                return response()->json([
                    'success' => true,
                    'message' => ['client' => $client, 'staff'=> $employee,'user'=> Auth::guard('web')->user(),'token'=> $token,'roles'=>$roles->toArray()]
                ]);
            }else{
                $this->incrementLoginAttempts($request);
                return $this->sendError('Invalid login credentials or access denied.',[],0);
            }
        } else {
            $this->incrementLoginAttempts($request);
            return $this->sendError('Invalid login credentials.',[],0);
        }
    }

    public function getProfile(Request $request){

        $userId = $request->get('user_id');
        $userId =  !empty($userId) ? $userId : Auth::user()->id;

        $client =  Client::where('user_id',  $userId)->first();
        $employee =  Employee::where('user_id',  $userId)->first();
        $user = User::where('id', $userId)->first();
        $roles = $user->roles()->with('permissions')->get();
        return response()->json([
            'success' => true,
            'message' => ['client' => $client, 'staff'=> $employee,'user'=>$user, 'roles' => $roles->toArray()]
        ], 200);

    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return $this->sendResponse($this->response,'User has been logged out',1);

        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token.'
            ], 404);
        }

		//Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request)
    {
        $email = $_GET['email'];

        // $token= $request->bearerToken();
        // $user = JWTAuth::authenticate($token);
        $user = User::where('email', $email)->first();

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'The email dose not exist']);
        } else {

          $user = User::with('roles')->with('permissions')->where('id',$user->id)->first();

        }
        return response()->json(['success' => true, 'message' => $user]);
    }

    public function resend_otp(Request $request)
    {
        $email = $request->email;
        $otp = rand(1000,9999);
        $user = User::where('email', $email)->first();
        User::where('email', $email)->update([
            'otp' => $otp
        ]);
        Mail::to($user->email)->send(new NewAccountMail($user->first_name, $otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
        ], Response::HTTP_OK);
    }

    public function profile_update(Request $request)
    {
        $data = $request->only('first_name', 'last_name ', 'middle_name', 'gender', 'date_of_birth', 'home_address', 'state_of_origin', 'office_address', 'city', 'state');
        $validator = Validator::make($data, [
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'gender' => 'required',
            'date_of_birth' => 'sometimes|string',
            'home_address' => 'sometimes|string',
            'office_address' => 'sometimes',
            'city' => 'sometimes|string',
            'state' => 'sometimes|string'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 404);
        }

        $profile = User::where('id', Auth::guard('api')->user()->id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'home_address' => $request->home_address,
            'office_address' => $request->office_address,
            'city' => $request->city,
            'state' => $request->state,
            'state_of_origin' => $request->state_of_origin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ], Response::HTTP_OK);
    }

    protected function sendResetLinkResponse(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Required field cannot be blank.'
            ], 404);
        }
        $otp = rand(1000,9999);
        $response = User::where('email', $request->email)->first();
        //dd($request->email);

        if($response){
            $emailCheck = DB::table('password_resets')->where('email', '=', $request->email)->exists();

            if(!$emailCheck) {
                DB::table('password_resets')->insert([
                    'email' =>  $request->email,
                    'token' =>  $otp
                ]);
            } else {
                DB::table('password_resets')->where('email', $request->email)
                    ->update([
                        'email' =>  $request->email
                        // 'token' =>  $otp
                    ]);
            }
            $mail_details = [
                'title' => 'Testing Application OTP',
                'body' => 'Your OTP is : '. $otp
            ];
            //$mailSend = Mail::to($request->email)->send(new Gmail($mail_details));

            $user = User::where('email', '=', $request->email)->first();
//
//             Send Email to new user
//            if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
//                Mail::to($request->email)->send(new ResetPasswordMail($user->first_name.' '.$user->last_name, $user->otp));
//            }
//             Send Sms to user
//            TwilioSMSController::sendSMS($user->phone_num, TwilioSMSController::resetPasswordMessage($user->first_name.' '.$user->last_name, $user->otp));

            // $account = new ResetPasswordDto( $user->first_name.' '.$user->last_name, $user->otp,  EventType::NEW_ACCOUNT);
            // event(new PasswordResetEvent($account, $user->email, $user->id, $user->phone_num));

            //DB::table('password_resets')->where('email', '=', $request->email)->update(['otp' => $otp]);
            DB::table('users')->where('email', '=', $request->email)->update(['otp' => $otp]);
            
            // Mail::to($user->email)->send(new ResetPasswordMail($user->first_name, $otp));
            $emailNotification = NotificationSettings::where('trigger_name', 'PASSWORD_RESET')->where('send_email', 1)->first();
                $fullname=$user->first_name.' '.$user->last_name;
                if ($emailNotification) {
                    $mail = new ResetPasswordMail($fullname,$otp);
                    
                    Helper::sendEmail($user->email, $mail);
                }
        
                // Send SMS notification to the new user
                $smsNotification = NotificationSettings::where('trigger_name', 'PASSWORD_RESET')->where('send_sms', 1)->first();
                if ($smsNotification) {
                    $message = TwilioSMSController::resetPasswordMessage($fullname,$otp);
                    Helper::sendSms($user->phone_num, $message);
                }

            $success = true;
            $message = "Mail send successfully";
        }else{
            $success = false;
            $message = "Account does not exist.";
        }

        //$message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : GLOBAL_SOMETHING_WANTS_TO_WRONG;
        $response = ['success'=> $success, 'message' => $message];
        return response($response, 200);
    }

    protected function sendResetLinkResponseCheck(Request $request)
    {
        //$forgotPassword = DB::table('password_resets')->where([['email', '=', $request->email], ['otp', '=', $request->otp]])->first();
        $forgotPassword = DB::table('users')->where([['email', '=', $request->email], ['otp', '=', $request->otp]])->first();

        if($forgotPassword){
            DB::table('users')->where('email', '=', $request->email)->update(['is_verified' => 1]);
            return response(["success" => true, "message" => "Success"]);
        }
        else{
            return response(["success"=>false, 'message' => 'Invalid password']);
        }
    }
    protected function sendResetResponse(Request $request){
        //password.reset
        $input = $request->only('email','otp', 'new_password');
        $validator = Validator::make($input, [
            'otp' => 'required',
            'email' => 'required|email',
            'new_password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Required field cannot be blank.'
            ], 404);
        }

        // if(!$otpValidator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invalid OTP.'
        //     ], 404);
        // }
        $emailValidator = Validator::make($request->only('email'), [
            'email' => 'required|email|unique:users'
        ]);
        if(!$emailValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Account does not exist.'
            ], 404);
        }
        $passwordValidator = Validator::make($request->only('new_password'), [
            'new_password' => 'required|string'
        ]);

        if($passwordValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password length must be upto six (1) character.'
            ], 404);
        }

        $otpValidator = User::where('otp', $request->otp)->first();
        if($otpValidator) {
            $response = User::where('email', $request->email)
                            ->update(['otp' => $request->otp, 'password' => bcrypt($request->new_password)]);

            if($response){
                $message = "Password reset successfully";
            }else{
                $message = "Email could not be sent to this email address";
            }

            $response = ['success'=> true, 'message' => $message];
            return response()->json($response);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Otp.'
            ], 404);
        }
    }

    public function addNew(Request $request)
    {
        //Validate data
        $data = $request->only('first_name', 'last_name', 'middle_name', 'gender', 'phone_num', 'state_of_origin', 'home_address', 'state', 'city', 'email', 'password');
        $validator = Validator::make($data, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_num' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        $emailValidator = Validator::make($request->only('email'), [
            'email' => 'required|email|unique:users'
        ]);
        $passwordValidator = Validator::make($request->only('password'), [
            'password' => 'required|string|min:6|max:50'
        ]);

        if($passwordValidator->fails()) {
            return [
                'success' => false,
                'message' => 'Password length must be upto six (6) characters.'
            ];
        }
        if ($emailValidator->fails()) {
            return [
                'success' => false,
                'message' => 'Email already in use.'
            ];
        }
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Required field cannot be blank.'
            ];
        }

        $otp = rand(1000,9999);

        $mail_details = [
            'title' => 'Testing Application OTP',
            'body' => 'Your OTP is : '. $otp
        ];
        //Mail::to($request->email)->send(new Gmail($mail_details));

        //Request is valid, create new user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'home_address' => $request->home_address,
            'office_address' => $request->office_address,
            'city' => $request->city,
            'state' => $request->state,
            'phone_num' => $request->phone_num,
            'email' => $request->email,
            'avata' => $request->avatar,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole('Registered');
        User::where('email','=',$request->email)->update(['otp' => $otp]);
        $userId = User::where('email', '=', $request->email)->select('id')->first();
        $user = User::where('email', '=', $request->email)->first();
        //DB::table('role_user')->insert(['role_id'=>3, 'user_id' => $userId['id']]);

        // Send Email to new user
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($request->email)->send(new NewAccountMail($user->first_name.' '.$user->last_name, $user->otp));
        }
        // Send Sms to user
        TwilioSMSController::sendSMS($user->phone_num, TwilioSMSController::newAccountMessage($user->first_name.' '.$user->last_name, $user->otp));
        $admin=User::where('is_admin','1')->get();
        if($admin){
            // Send email notification to the new user
        $newUser = $response['data']['user'];
        $emailNotification = NotificationSettings::where('trigger_name', 'ACCOUNT_BY_ADMIN')->where('send_email', 1)->first();
        if ($emailNotification) {
            $mail = new NewAccountByAdminMail($newUser->name,$newUser->email, $newUser->password);
            Helper::sendEmail($newUser->email, $mail);
        }

        // Send SMS notification to the new user
        $smsNotification = NotificationSettings::where('trigger_name', 'ACCOUNT_BY_ADMIN')->where('send_sms', 1)->first();
        if ($smsNotification) {
            $message = TwilioSMSController::newAccountByAdminMessage($newUser->name,$newUser->email, $newUser->password);
            Helper::sendSms($newUser->phone_number, $message);
        }
        }

        // Raise Events
        $account = new AccountDto(null, $user->first_name.' '.$user->last_name, $user->otp, null, $user->email, null, EventType::NEW_ACCOUNT);
        event(new NewAccountEvent($account, $user->email, $user->id, $user->phone_num));

        //User created, return success response
        return [
            'success' => true,
            'message' => $userId['id']
        ];
    }

    public function updateAvatar(Request $request){
        $userId = Auth::user()->id;

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'avatar' => 'required|string',
        ]);

        if($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('id', $userId)->firstOrFail();

        $user->avatar = $request['avatar'];

        $user->save();

        return \response()->json([
                'success'=> true,
                'message' => new UserResource($user)
            ], 200
        );
    }

    public function updatePassword(Request $request){
        $userId = Auth::user()->id;

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'password' => 'required|string|max:255',
        ]);

        if($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('id', $userId)->firstOrFail();

        $user->password = Hash::make($request['password']);

        $user->save();

        return \response()->json([
            'success'=> true,
            'message' => new UserResource($user)
            ], 200
        );
    }

    public function chartDashboard(){
        $countClient = Client::count();
        $countAssignedStaff = StaffAssignment::count();
        $countReport = StaffChart::count();
        $countRedFlag = StaffChart::where('is_red_flag', true)->count();
        return response()->json(
            [
                'success' => true,
                'message' => [
                    'total_num_of_clients' => $countClient,
                    'total_num_of_assigned_staff' => $countAssignedStaff,
                    'total_num_of_submitted_report' => $countReport,
                    'total_num_of_charts_red_flagged' => $countRedFlag,
                ]
            ], Response::HTTP_OK
        );
    }

    public function inventoryDashboard(){

//        -- total_sales_order_usd (decimal) => Sum all total_amount in USD
//        -- total_sales_order_ngn (decimal) => Sum all total_amount in NGN
//        -- total_num_items (int)
//        -- total_num_low_stocks (int)  => (Item.type != 'Service' && (Item.current_stock_level <= Item.reorder_level || Item.current_stock_level == 0))

        $total_sales_order_usd = SaleOrder::where('item_currency', 'USD')->sum('total_amount');
        $total_sales_order_ngn = SaleOrder::where('item_currency', 'NGN')->sum('total_amount');
        $total_num_items = Item::query()->count();
        $total_num_low_stocks =  DB::select("select count(*) as total_num_low_stocks from items where  items.type <> 'Service' and items.current_stock_level <= items.reorder_level or  items.current_stock_level = 0");

        return response()->json(
            [
                'success' => true,
                'message' => [
                    'total_sales_order_usd' => $total_sales_order_usd,
                    'total_sales_order_ngn' => $total_sales_order_ngn,
                    'total_num_items' => $total_num_items,
                    'total_num_low_stocks' => $total_num_low_stocks[0]->total_num_low_stocks,
                ]
            ], Response::HTTP_OK
        );
    }

    public function billingDashboard(){

//        -- total_num_transactions
//        -- total_num_invoices
//        -- total_sum_receipts_usd
//        -- total_sum_receipts_ngn
        $total_sum_receipts_usd = Receipt::where('currency', 'USD')->sum('amount_paid');
        $total_sum_receipts_ngn = Receipt::where('currency', 'NGN')->sum('amount_paid');
        $total_num_transactions = Transaction::query()->count();
        $total_num_invoices = Invoice::query()->count();

        return response()->json(
            [
                'success' => true,
                'message' => [
                    'total_sum_receipts_usd' => $total_sum_receipts_usd,
                    'total_sum_receipts_ngn' => $total_sum_receipts_ngn,
                    'total_num_transactions' => $total_num_transactions,
                    'total_num_invoices' => $total_num_invoices,
                ]
            ], Response::HTTP_OK
        );
    }

    public function appDashboard(Request $request){
        $userId =  Auth::user()->id;
        $staff = Employee::where('user_id', $userId)->first();
        $client =  Client::where('user_id', $userId)->first();

        $clientIds = FamilyFriendAssignment::where('familyfriend_id', $userId)->pluck('client_id');
        $dateFrom = $request->get('date_from');
        $total_num_reports_today_familyfriend = StaffChart::whereIn('client_id', $clientIds)->whereBetween('created_at', [$dateFrom, new \DateTime('now')])->count();

        $total_num_familyfriend_assignment = 0;
        $total_num_staff_assignment = 0;
        $total_num_reports_today_staff = 0;
        $total_num_reports_today_client = 0;
        if(!empty($staff)){
            $res = new StaffAssignmentAllCollection(StaffAssignment::where('staff_id', $staff->id)->get());
            foreach ($res as $value) {
                $active = 1;
                if(isset($value['client']) && isset($value['client']['user'])) {
                    $active = $value['client']["user"]["active"];
                }
                if($active == 1)
                $total_num_staff_assignment += 1;
            }
            
            $total_num_reports_today_staff = StaffChart::where('staff_id', $staff->id)->whereBetween('created_at', [$dateFrom, new \DateTime('now')])->count();
        }elseif (!empty($client)){
            $total_num_staff_assignment = StaffAssignment::where('client_id', $client->id)->count();
            $total_num_reports_today_client = StaffChart::where('client_id', $client->id)->whereBetween('created_at', [$dateFrom, new \DateTime('now')])->count();
        }
        
        
        $res = FamilyFriendAssignment::where('familyfriend_id', $userId)->get();
        foreach (FriendFamilyAssignmentResource::collection($res) as $value) {
            $active = 1;
            if(isset($value['client']) && isset($value['client']['user'])) {
                $active = $value['client']["user"]["active"];
            }
                if($active == 1)
                $total_num_familyfriend_assignment += 1;
        }

        return \response()->json(
                [
                    'success' => true,
                    'message' => [
                        'total_num_staff_assignment' => $total_num_staff_assignment,
                        'total_num_familyfriend_assignment' => $total_num_familyfriend_assignment,
                        'total_num_reports_today_client' => $total_num_reports_today_client,
                        'total_num_reports_today_staff' => $total_num_reports_today_staff,
                        'total_num_reports_today_familyfriend' => $total_num_reports_today_familyfriend
                    ]
                ]
            , 200);

    }

    public function payrollDashboard(Request $request){
            //        total_payrun_salary_ngn: The sum of all salary(basic/per_day) where currency is ngn
            //
            //        total_payrun_salary_usd: The sum of all salary(basic/per_day) where currency is usd
            //
            //        total_absent: total rota where is_present not equal true and rota_date less than current date
            //
            //        total_present: total rota where is_present equal true and rota_date less than current date

            //        api/admin/payrollDashboard
            //
            //        -- total_payrun_salary_ngn (int)
            //
            //        -- total_payrun_salary_usd (int)
            //
            //        -- total_present (int)
            //
            //        -- total_present (int);
        try {
            $staff_id =  $request->get('staff_id');
            if (!empty($staff_id)){
                $total_payrun_salary_ngn = PayRun::where('staff_id', $staff_id)->where('currency', 'NGN')->sum('basic_salary');
                $total_payrun_salary_usd = PayRun::where('staff_id', $staff_id)->where('currency', 'USD')->sum('basic_salary');

                $total_not_present = Rota::where('staff_id', $staff_id)->where('is_present', false)
                    ->where('rota_date', '<', date('Y-m-d'))->count();
                $total_present = Rota::where('staff_id', $staff_id)->where('is_present', true)
                    ->where('rota_date', '<', date('Y-m-d'))->count();
            }else{
                $total_payrun_salary_ngn = PayRun::where('currency', 'NGN')->sum('basic_salary');
                $total_payrun_salary_usd = PayRun::where('currency', 'USD')->sum('basic_salary');

                $total_not_present = Rota::where('is_present', false)
                    ->where('rota_date', '<', date('Y-m-d'))->count();
                $total_present = Rota::where('is_present', true)
                    ->where('rota_date', '<', date('Y-m-d'))->count();
            }
            return \response()->json(
                [
                    'success' => true,
                    'message' => [
                        'total_payrun_salary_ngn' => $total_payrun_salary_ngn,
                        'total_payrun_salary_usd' => $total_payrun_salary_usd,
                        'total_not_present' => $total_not_present,
                        'total_present' => $total_present
                    ]
                ], 200);
        }catch (\Exception $e){
            Log::debug($e);
            return \response()->json(
                [
                    'success' => true,
                    'message' => $e->getMessage()
                ], 200);
        }
    }
}
