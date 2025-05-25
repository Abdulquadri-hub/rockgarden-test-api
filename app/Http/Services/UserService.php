<?php

namespace App\Http\Services;
use App\Mail\NewAccountByAdminMail;
use App\Dto\AccountDto;
use App\Dto\EventType;
use App\Events\NewAccountEvent;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Mail\NewAccountMail;
use App\Models\FamilyFriendAssignment;
use App\Models\NotificationSettings;
use App\Models\SystemContacts;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserService
{
    public function register(Request $request)
    {
        try {
            //Validate data
            DB::beginTransaction();
            $data = $request->only('first_name', 'last_name', 'phone_num', 'city', 'email', 'password');
            $validator = Validator::make($data, [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'phone_num' => 'required|string',
                'email' => 'required',
                'password' => 'required|string'
            ]);

            $emailValidator = Validator::make($request->only('email'), [
                'email' => 'required|unique:users'
            ]);

            Log::debug(["New User email comming " => $request->only('email')]);
            if ($emailValidator->fails()) {
                DB::commit();
                return [
                    'success' => false,
                    'message' => 'Email already in use.'
                ];
            }
            //Send failed response if request is not valid
            if ($validator->fails()) {
                DB::commit();
                return [
                    'success' => false,
                    'message' => 'Required field cannot be blank.'
                ];
            }

            $otp = rand(1000,9999);
//
//            $mail_details = [
//                'title' => 'Testing Application OTP',
//                'body' => 'Your OTP is : '. $otp
//            ];
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
                'state_of_origin' => $request->state_of_origin,
                'file_img' => $request->file_img,
                'city' => $request->city,
                'state' => $request->state,
                'phone_num' => $request->phone_num,
                'email' => $request->email,
                'avatar' => $request->avatar,
                'password' => Hash::make($request->password),
            ]);

            //Request is valid, create new user
            $user = User::where('email',$request->email)->first();
            $roles = $request->get('role');
            if(!empty($roles)){
                foreach ($roles as $role){
                    $user->assignRole($role);
                }
            }

            User::where('email','=',$request->email)->update(['otp' => $otp]);
            $userId = User::where('email', '=', $request->email)->select('id')->first();
            $user = User::where('email', '=', $request->email)->first();
            //DB::table('role_user')->insert(['role_id'=>3, 'user_id' => $userId['id']]);

            //User created, return success response
            DB::commit();

            // Raise Events
        $account = new AccountDto(null, $user->first_name . ' ' . $user->last_name, $user->otp, null, $user->email, null, EventType::NEW_ACCOUNT);
        event(new NewAccountEvent($account, $user->email, $user->id, $user->phone_num));
        try {
            Mail::to($user->email)->send(new NewAccountByAdminMail($user->first_name . ' ' . $user->last_name, $user->email, $request->password));
        } catch (\Swift_TransportException $e) {
            Log::error($e->getMessage());
        }
            return [
                'success' => true,
                'message' => $userId['id']
            ];
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function update(Request $request)
    {
        try {
            //Validate data
            $data = $request->only('id','first_name', 'last_name', 'phone_num', 'password');
            $validator = Validator::make($data, [
                'id' => 'required|integer',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'phone_num' => 'required|string',
            ]);

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
            $user = User::where('id',$request->id)->first();
            $roles = $request->get('role');
            if(!empty($roles)){
                $oldRoles = $user->roles()->with('permissions')->get();
                foreach ($oldRoles as $oldRole){
                    $user->removeRole($oldRole);
                }
                foreach ($roles as $role){
                    $user->assignRole($role);
                }
            }

            $user =  User::where('id','=',$request->get('id'))->first();

            if(empty($user)){
                return [
                    'success' => false,
                    'message' => 'User not found.'
                ];
            }

            User::where('id','=',$request->get('id'))->update(
                [
                    'state_of_origin' => $request->get('state_of_origin'),
                    'file_img' => $request->get('file_img'),
                    'first_name' => $request->get('first_name'),
                    'last_name' => $request->get('last_name'),
                    'middle_name' => $request->get('middle_name'),
                    'gender' => $request->get('gender'),
                    'date_of_birth' => new \DateTime($request->get('date_of_birth')),
                    'home_address' => $request->get('home_address'),
                    'avatar' => $request->get('avatar'),
                    'email' => empty($request->get('email')) ? $user->email : $request->get('email'),
                    'city' => $request->get('city'),
                    'state' => $request->get('state'),
                    'phone_num' => $request->get('phone_num'),
                    'office_address' => $request->get('office_address'),

                ]
            );

            if(!empty($request->get('password'))){
                $passwordValidator = Validator::make($request->only('password'), [
                    'password' => 'required|string'
                ]);

                if($passwordValidator->fails()) {
                    return [
                        'success' => false,
                        'message' => 'Invalid Password.'
                    ];
                }

                User::where('id','=',$request->get('id'))->update(
                    [
                        'password' => Hash::make($request->get('password')),
                    ]
                );
            }

            $userId = User::where('id', $request->get('id'))->select('id')->first();
            //DB::table('role_user')->insert(['role_id'=>3, 'user_id' => $userId['id']]);

            //User created, return success response
            return [
                'success' => true,
                'message' => $userId['id']
            ];
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param int $id
     * @return string
     */
    public function generateCode(int $id): string
    {
        $strId = "".$id;

        while(strlen($strId) < 6){
            $strId = '0'.$strId;
        }
        return $strId;
    }

    public static function usersAdmin(string $event, $isAdmin){
        $notificationType = NotificationSettings::where('trigger_name', $event)->orderBy('updated_at', 'DESC')->first();
        $contact = SystemContacts::where('is_default', $isAdmin)->first();
        $res = [];

        $res['contact'] = [
            'email' => !empty($contact) ? $contact->email : null,
            'phone' => !empty($contact) ? $contact->phone : null
        ];

        $res['notification'] = [
            'email' => !empty($notificationType) && (bool)$contact->send_email,
            'sms' => !empty($notificationType) && (bool)$contact->send_sms
        ];

        return $res;
    }


    public static function familyFriendUsers($id){
        $res = FamilyFriendAssignment::where('client_id', $id)->pluck('familyfriend_id')->toArray();
        return User::whereIn('id', $res)->get();
    }
}
