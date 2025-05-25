<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use Validator;
//use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as VPassword;
use Illuminate\Support\Facades\Config;
use DB;

class AuthController extends BaseController
{
	/**
	* Registration
	*/
	protected $response;
	//http://localhost:8001/api/admin/registration
	public function register(Request $request){

	}


	public function login(Request $request){

	    $data = [
	        'email' => $request->email,
	        'password' => $request->password
	    ];

	    if (method_exists($this, 'hasTooManyLoginAttempts') &&
	        $this->hasTooManyLoginAttempts($request)) {
	        $this->fireLockoutEvent($request);
	        $seconds = $this->limiter()->availableIn(
	            $this->throttleKey($request)
	        );
	       return $this->sendError(__('Too many login attempts. Please try again in '.$seconds.' seconds.'),[],0);
	    }

	    if (auth()->attempt($data)) {

	        $this->response = getUserDetails(auth()->user());
	        $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
	        return $this->sendResponse($this->response,__('api.login_success'),1,'',$token);
	    } else {
	        $this->incrementLoginAttempts($request);
	        return $this->sendError(__('api.login_error'),[],0);
	    }
	}



	public function logout(Request $request){
	    $request->user()->token()->revoke();
	    return $this->sendResponse($this->response,__('api.loged_out_message'),1);
	}

}
