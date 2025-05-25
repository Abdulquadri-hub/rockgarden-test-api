<?php
namespace App\Helpers;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Log;
use App\Models\ExchangeRate;
use App\Mail\InvoiceEmail;
use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;

use Illuminate\Support\Facades\Mail;

class Helper
{
    public static function sendEmail($to,$mail)
    {
        try {
       
        Mail::to($to)->send($mail);
    } catch (\Exception $e) {
        Log::error($e);
    }
    }
    
    public static function sendSms($toNumber, $message)
    {
    
   
    // Your Twilio Account SID, Auth Token, and Twilio phone number
    $accountSid = 'ACd8f0ae943852733d8bd1a79805f8253c';
    $authToken = 'df2d115808f307ee9cbc65f909199441';
    $twilioNumber = '+13203358047';

    try {
        // Check if 'toNumber' is valid
        if (!empty($toNumber)) {
            // Initialize the Twilio client
            $client = new Client($accountSid, $authToken);

            // Send the SMS message
            $client->messages->create(
                $toNumber,
                [
                    'from' => $twilioNumber,
                    'body' => $message,
                ]
            );

            return true; // SMS sent successfully
        } else {
            return false; // Invalid 'toNumber'
        }
    } catch (RestException $e) {
        // Log the Twilio API error message
        Log::error('Twilio API Error: ' . $e->getMessage());

        return false; // Failed to send SMS
    } catch (Exception $e) {
        // Log any other exceptions
        Log::error('Exception: ' . $e->getMessage());

        return false; // Failed to send SMS
    }
}
    
    // Encode a number to a short string
    public static function encodeId($num)
{
    if (!is_numeric($num)) {
        // Handle the case when the input is not a valid numeric value
        return null; // Or throw an exception, return an error message, etc.
    }

    $ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $chars = [];

    do {
        $remainder = $num % 62;
        $chars[] = $ALPHABET[$remainder];
        $num = (int) ($num / 62);
    } while ($num > 0);

    return implode('', array_reverse($chars));
}

// Decode
public static function decodeId($str)
{
    $ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $num = 0;
    $len = strlen($str);

    for ($i = 0; $i < $len; $i++) {
        $index = strpos($ALPHABET, $str[$i]);
        $num = $num * 62 + $index;
    }

    return $num;
}
public static function ngnToUsd($ngnAmount) {
  $currency = ExchangeRate::where('currency_base', 'NGN')->where('currency_quote', 'USD')->first();
  if($currency) {
    $ngnUsdValue = $currency->value;
    $usdAmount = $ngnAmount * $ngnUsdValue;
    return $usdAmount;
  } else {
    return null;
  }
}
public static function usdToNgn($usdAmount) {
  $currency = ExchangeRate::where('currency_base', 'USD')->where('currency_quote', 'NGN')->first();
  if($currency) {
    $usdNgnValue = $currency->value;
    $ngnAmount = $usdAmount * $usdNgnValue;
    return $ngnAmount;
  } else {
    return null;
  }
}


}





if (! function_exists('changeDateFormate')) {
	function changeDateFormate($date,$date_format){
	    return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($date_format);    
	}
}

if (! function_exists('faqformate')) {
	function faqformate($datas){
	    $faqs =array();
	    if(!empty($datas)){
		    foreach ($datas as $key => $value) {
		    	$faqs[$key]['question'] = $value['title'];
		    	$faqs[$key]['answer'] = $value['details'];
		    } 
		}
	    return  $faqs;
	}
}




if (! function_exists('changeDateTimeFormate')) {
	function changeDateTimeFormate($date,$date_format){		
	    return \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $date)->format($date_format);
	}
}

if (! function_exists('amount_formate')) {
	function amount_formate($number){
	   return  number_format($number,2, '.', ',');
	}
}


if (! function_exists('productImagePath')) {   
	function productImagePath($image_name)
	{
	    return public_path('images/products/'.$image_name);
	}
}


if (! function_exists('MakeUserTree')) {
	function MakeUserTree($users,$tree){
		
		if(!empty($users)){
			foreach ($users as $key => $user) {			
				//dd($user->childrenRecursive);
				if(!empty($user->id)){
					$childrens =array();
					if(!empty($user->childrenRecursive)){
						$childrens = $user->childrenRecursive;

					}
					$tree .='<div id="node_'.$user->id.'" class="window hidden"';
					    $tree .=' data-id="'.$user->id.'"';
					    $tree .=' data-parent="'.$user->parent_id.'"';

					    if(!empty($childrens[0]->id)){
					    	$tree .=' data-first-child="'.$childrens[0]->id.'"';
					    }else{
					    	$tree .=' data-first-child=""';
					    }
					    	$tree .=' data-next-sibling="">';
					    
					    $tree .=$user->name;
					$tree .='</div>';

					//dd($user->childrens()->get()[0]->id);
					if(!empty($childrens)){
						foreach ($childrens as $user_key => $user_c) {	
								
							$tree .='<div id="node_'.$user_c->id.'" class="window hidden"';
							    $tree .=' data-id="'.$user_c->id.'"';
							    $tree .=' data-parent="'.$user_c->parent_id.'"';
							    $tree .=' data-first-child=""';
							    $tree .=' data-next-sibling="">';
							    $tree .=$user_c->name;
							$tree .='</div>';
							//echo "<br>--".$user_c->name;
							if(!empty($user_c->childrenRecursive)){
								$childrens_subs = $user_c->childrenRecursive;
								$tree = MakeUserTree($childrens_subs,$tree);
							}
							
						}
					}
				}

			}
		}

		return $tree;
	}
}

if (! function_exists('number_format_locale_old')) {
	function number_format_locale_old($money,$d){
	    $decimal = (string)($money - floor($money));
	    $money = floor($money);
	    $length = strlen($money);
	    $m = '';
	    $money = strrev($money);
	    for($i=0;$i<$length;$i++){
	        if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$length){
	            $m .=',';
	        }
	        $m .=$money[$i];
	    }
	    $result = strrev($m);
	    $decimal = preg_replace("/0\./i", ".", $decimal);
	    $decimal = substr($decimal, 0, 3);
	    if( $decimal != '0'){
	    	$result = $result.$decimal;
	    }else{
	    	$result = $result.".00";
	    }
	    return $result;
	}
}

if (! function_exists('number_format_locale')) {
	function number_format_locale($money,$d){
	    return number_format($money,2);
	}
}


if (! function_exists('getUserDetails')) {
	function getUserDetails($user,$available_earning=50000){

		$data = array();
		$birthdate ="";
		if(!empty($user->birthdate)){
			$birthdate = Carbon::createFromFormat('Y-m-d', $user->birthdate)->format('d-m-Y');
		}
		$mature_slot_earning = 0;
		$mature_slot_ =$user->pincode->where('is_mature',1)->where('is_withdraw',0);
		if(!empty($mature_slot_) ){			
			foreach ($mature_slot_ as $slot){
				$mature_slot_earning = $mature_slot_earning + $slot->pin_amount;
			}
			$Withdrawal = App\Models\Withdrawal::where('user_id',$user->id)->where('withdraw_from','=','slot_mature')
				->where(function ($query) {
				    $query->where('status','=','pending')
				          ->orWhere('status','=','processed');
				})->sum('amount');
			$mature_slot_earning = $mature_slot_earning - $Withdrawal ;
		}		

		$data['user_id']			=	$user->id;
		$data['name']				=	$user->name;
		$data['email']				=	$user->email;
		$data['province_id']		=	(int)$user->province_id;
		$data['town_id']			=	(int)$user->town_id;
		$data['status']				=	$user->status;
		$data['birthdate']			=	$birthdate;
		$data['refcode']			=	$user->refcode;
		$data['qr_code']			=	"https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=".$user->refcode;
		$data['phone']				= 	$user->phone;
		$data['investment_limit'] 	= 	$user->investment_limit;
		$data['total_earning'] 		= 	$user->over_all_earning;
		$data['available_earning'] 	= 	$user->wallet;
		$data['mature_slot_earning']= 	$mature_slot_earning;
		//money_format('$%i',$amount);
		$data['investment_limit_display'] 	= 	number_format_locale($user->investment_limit,2);		
		$data['total_earning_display'] 		= 	number_format_locale($user->over_all_earning,2);
		$data['available_earning_display'] 	= 	number_format_locale($user->wallet,2);
		$data['mature_slot_earning_display']= 	number_format_locale($mature_slot_earning,2);

		$data['is_email_verify']	= 	$user->is_email_verify;
		$data['is_phone_verify']	= 	$user->is_phone_verify;
		$data['profile_verify']		= 	$user->isverify;
		$data['member_since']		= 	$user->created_at->format(config('site_setting.DATE_FORMATE','m/d/Y'));
		$data['province_name']		= 	!empty($user->province->name) ? $user->province->name:'' ;
		$data['town_name']			= 	!empty($user->town->name) ? $user->town->name:'';
		$data['referral_by']		= 	!empty($user->parent->name)?$user->parent->name:'';
		
		$data['avatar']				= 	!empty($user->avatar)?url('uploads/userAvatar/'.$user->avatar):url('img/default-user.png');

		$data['document']			= 	!empty($user->document)?url('uploads/userdocument/'.$user->document):null;
		if(!empty($user->Bankdetail)){
			$data['bank_details'] = $user->Bankdetail;
		}
		if(!empty($user->Beneficiary)){
			$data['beneficiary']['fullname'] = $user->Beneficiary->fullname;
			$data['beneficiary']['phone'] = $user->Beneficiary->phone;
			$data['beneficiary']['address'] = $user->Beneficiary->address;
		}	
		$withdrawl_status = withdrawEligible($user);	
		$data['is_withdraw_enable'] = $withdrawl_status;
		
		/*$present_month = Carbon::now()->month;
		$present_year = Carbon::now()->year;		
		$Withdrawal =App\Models\Withdrawal::where('user_id',$user->id)
		            ->where('status','!=','reject')
		            ->whereMonth('request_date', $present_month)
		            ->whereYear('request_date', $present_year)
		            ->orderBy('id','DESC')
		            ->first();
		$data['withdraw_enable_color'] = "red";        
		if($withdrawl_status ==1 ){
			$data['withdraw_enable_color'] = "green";
		}
		if(!empty($Withdrawal)){
			$data['withdraw_enable_color'] = "blue";
		}*/

		
		return $data;
	}
}



if (! function_exists('generateUniqueCode')) {
	function generateUniqueCode($digit){

	    $characters = '0123456789';
	    $charactersNumber = strlen($characters);
	    $codeLength = 6;
	    $code = '';
	    while (strlen($code) < $digit) {
	        $position = rand(0, $charactersNumber - 1);
	        $character = $characters[$position];
	        $code = $code.$character;
	    }
	    
	    return $code;
	}
}


if(! function_exists('getUserPincodes')){
	function getUserPincodes($pincodes){
		$data =array();
		if(!empty($pincodes)){
			foreach ($pincodes as $key => $pincode) {
				$data[$key]['id'] 			= $pincode->id;
				$data[$key]['pincode'] 		= $pincode->pincode;
				$data[$key]['action'] 		= $pincode->type;
				$data[$key]['status'] 		= 'Available';

				if(!empty($pincode->available_till)){
					$data[$key]['action']="Posted";
					//$data[$key]['status']= 'Used';
				}


				$data[$key]['assigned_on'] 	= $pincode->assigned_on;
				$data[$key]['received_on'] 	= $pincode->received_on;
				$data[$key]['is_slot'] 		= $pincode->is_slot;
				$data[$key]['slot_converted_Date'] 		= $pincode->slot_converted_Date;
				
				if($pincode->type =="received"){
					$data[$key]['received_from_name'] 		= $pincode->purchaseFrom->name;
					$data[$key]['received_from_id'] 		= $pincode->purchaseFrom->id;
					$data[$key]['received_from_member_id'] 	= $pincode->purchaseFrom->refcode;
				}else{
					
					$data[$key]['purchase_from_name'] 		= !empty($pincode->purchaseFrom->name)?$pincode->purchaseFrom->name:"";
					$data[$key]['purchase_from_id'] 		= !empty($pincode->purchaseFrom->id)?$pincode->purchaseFrom->id:'';
					$data[$key]['purchase_from_member_id'] 	= !empty($pincode->purchaseFrom->refcode)?$pincode->purchaseFrom->refcode:'';
				}
			}
		}

		return $data;
	}

}




if(! function_exists('getListMarketPlace')){
	function getListMarketPlace($Lists){
		$return =array();
		if(!empty($Lists)){
			foreach ($Lists as $key => $List) {

				$to = Carbon::parse($List->available_till);
				$from = Carbon::now()->format('Y-m-d H:i:s');


				$return[$key]['id'] 			= $List->id;
				$return[$key]['pincode'] 		= $List->pincode->pincode;
				$return[$key]['pincode_blur'] 	= BlurPincode($List->pincode->pincode);
				$return[$key]['available_till'] = $List->available_till;
				$return[$key]['remain_time'] 	= $to->diff($from)->format('%H:%I:%S');;
				$return[$key]['now'] 			= $from;
				$return[$key]['posted_by'] 		= $List->user->refcode;
				$return[$key]['email'] 			= $List->user->email;
				$return[$key]['phone'] 			= $List->user->phone;
				$return[$key]['name'] 			= $List->user->name;				
			}
		}
		return $return;
	}
}

if(! function_exists('BlurPincode')){
	function BlurPincode($pincode){
		$pincode_new = substr($pincode,0,4);
		for($i=0;$i < strlen($pincode) - 4;$i++ ){
			$pincode_new =$pincode_new."*";
		}

		return $pincode_new;
	}

}

if(! function_exists('todayDate')){
	function todayDate(){
		return Carbon::now()->format('Y-m-d');
	}
}

if(! function_exists('todayDateTime')){
	function todayDateTime(){
		return Carbon::now()->format('Y-m-d H:i:s');
	}
}

if(! function_exists('SlotEarning')){
	function SlotEarning($data){
		$return =array();
		$return['date_added'] = Carbon::parse($data->slot_converted_Date)->format(config('site_setting.DATE_FORMATE','m/d/Y'));
		$return['pin_code'] = $data->pincode;
		$return['origin'] = $data->slot_origin;
		$return['slot_income'] = $data->slot_earning? $data->slot_earning:0;
		$return['slot_income_display'] = $data->slot_earning? number_format($data->slot_earning,2) : 0.00;

		return $return;
	}
}

if(! function_exists('responcePayoutHistory')){
	function responcePayoutHistory($datas){
		$return =array();
		foreach ($datas as $key => $data) {
			$return[$key]['request_date'] = Carbon::parse($data->request_date)->format(config('site_setting.DATE_FORMATE','m/d/Y'));
			$return[$key]['amount'] 	= number_format_locale($data->amount,2);
			$return[$key]['admin_fee'] 	= number_format_locale($data->admin_fee, 2);
			$return[$key]['paid_amount'] 	= number_format_locale($data->paid_amount, 2);
			
			$pay_via = "Bank";
			if($data->pay_via =="gcash"){
				$pay_via = "GCash";
			}

			$return[$key]['pay_via'] 	=$pay_via;

			$status = $data->status;		
			if($status == "reject"){
				//$status = "Rejected";
			}

			$return[$key]['status'] 	= $status;

			$return[$key]['request_time'] = Carbon::parse($data->request_date)->format('H:i:s');
			$return[$key]['request_no'] = $data->req_no;
			$return[$key]['rejected_reason'] = $data->rejected_reason;
			$return[$key]['id'] = $data->id;
		}
		return $return;

	}
}
if(! function_exists('responcePayoutHistoryDetails')){
	function responcePayoutHistoryDetails($data){
		$return =array();
		//dd($data);
		$return['request_date'] = Carbon::parse($data->request_date)->format(config('site_setting.DATE_FORMATE','m/d/Y'));
		$return['amount'] 	= number_format_locale($data->amount,2);
		$return['admin_fee'] 	= number_format_locale($data->admin_fee, 2);
		$return['paid_amount'] 	= number_format_locale($data->paid_amount, 2);
		
		$pay_via = "Bank";
		if($data->pay_via =="gcash"){
			$pay_via = "GCash";
		}

		$return['pay_via']=$pay_via;
		$status = $data->status;		
		if($status == "reject"){
			$status = "Rejected";
		}
		$return['status'] 	= $status;
		$return['request_time'] = Carbon::parse($data->request_date)->format('H:i:s');
		$return['request_no'] = $data->req_no;
		$return['rejected_reason'] = $data->rejected_reason;
		$return['id'] = $data->id;		
		return $return;

	}
}

if(! function_exists('MonthLastSEvenDate')){
	function MonthLastSEvenDate(){
		$lastday = config('site_setting.WITHDRAWAL_REQUEST_DAY',6);
		$return =array();
		$lastDayofPreviousMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
		$month_start = Carbon::now()->endOfMonth()->subDays($lastday-1)->format('Y-m-d');
		$return['month_end']=$lastDayofPreviousMonth;
		$return['month_start']=$month_start;		
		return $return;
	}
}

if(! function_exists('generateReqNo')){
	function generateReqNo($id){
		if($id <10){
			$id="000".$id;
		}

		if($id >= 10 && $id <100){
			$id="00".$id;
		}

		if($id >= 100 && $id <1000){
			$id="0".$id;
		}

		return "RQ".$id;
	}
}


if(! function_exists('sort_partners')){
	function sort_partners(&$array, $subfield,$sort_type){
		$sortarray = array();
	    foreach ($array as $key => $row){
	        $sortarray[$key] = $row[$subfield];
	    }

	    if($sort_type =='desc'){
	    	array_multisort($sortarray, SORT_DESC, $array);
	    }

	    if($sort_type =='asc'){
	    	array_multisort($sortarray, SORT_ASC, $array);
	    }
	}
}

if(! function_exists('PincodeHistoryArray')){
	function PincodeHistoryArray($PincodeHistorys){
		$return = array();
		foreach ($PincodeHistorys as $key => $PincodeHistory) {
			$return[$key]['id']= $PincodeHistory->id;
			$return[$key]['date']= $PincodeHistory->created_at->format(config('site_setting.DATE_FORMATE','m/d/Y'));
			$return[$key]['time']= $PincodeHistory->created_at->format("H:i:s");
			$return[$key]['pincode']= $PincodeHistory->pincode->pincode;			
			$return[$key]['action']= $PincodeHistory->action;
			
			$return[$key]['status']= $PincodeHistory->status;
			
			if($PincodeHistory->pincode->is_slot == 1){
				$return[$key]['status']= 'Used';
			}else{
				$return[$key]['status']= 'Available';
			}
			
			if($PincodeHistory->action == 'slot_reward'){
				$return[$key]['action']= "reward";
			}
		}

		return $return;
	}
}

if(! function_exists('paymentReceipt')){
	function paymentReceipt($details){
		$settings = \App\models\SiteSetting::all()->pluck('value','key')->toArray();
		$config = array(
		    'transport' => $settings['MAIL_MAILER'],
		    'host' => $settings['MAIL_HOST'],
		    'port' => $settings['MAIL_PORT'],
		    'encryption' => $settings['MAIL_ENCRYPTION'],
		    'username' => $settings['MAIL_USERNAME'],
		    'password' => $settings['MAIL_PASSWORD'],
		    'timeout' => null,
		    'auth_mode' => null,
		);
		Config::set('mail.mailers.smtp', $config);
		Config::set('mail.from.address', $settings['MAIL_FROM_ADDRESS']);       
		Config::set('mail.from.name', $settings['MAIL_FROM_NAME']); 
		try {	
			
		   \Mail::to($details['user_email'])->send(new \App\Mail\PaymentReceipt($details));	    
		} catch (\Exception $e) {
		   return $e->getMessage();		   
		}
	}
}

if(!function_exists('get_phone_with_country_code')){
	function get_phone_with_country_code($phone='', $country_id='', $country_code='63'){
		$phone = trim_phone($phone);
		if((!empty($phone)) && (!empty($country_code))){
			if(!check_starts_with($phone, $country_code)){
                $phone = $country_code.$phone;
            }
		}elseif((!empty($phone)) && (!empty($country_id))){
			$country_code = '63';
			if(!check_starts_with($phone, $country_code)){
                $phone = $country_code.$phone;
            }
		}
		return '+'.$phone;
	}
}

if(!function_exists('trim_phone')){
	function trim_phone($phone=''){
		if(!empty($phone)){ $phone = ltrim(get_intval($phone), '0'); }
		return $phone;
	}
}

if(!function_exists('check_starts_with')){
	// Function to check string starting with given substring
	function check_starts_with ($string, $startString){
	    $len = strlen($startString);
	    return (substr($string, 0, $len) === $startString);
	}
	// Examples:
	// if(check_starts_with("abcd", "ab")){ echo "True"; }else{ echo "False"; }    // True
	// if(check_starts_with("abcd", "cd")){ echo "True"; }else{ echo "False"; }    // False
}

if(!function_exists('get_intval')){
	function get_intval($value='', $return_zero_on_blank=FALSE){
		$intval = preg_replace('/[^0-9]/', '', $value);
		if(empty($intval) && $return_zero_on_blank){
			return 0;
		}else{ return $intval; }
	}
}

if(!function_exists('mobileNotification')){
	function mobileNotification($user,$title,$message){
		$SERVER_API_KEY = config('site_setting.FCM_KEY');

		if(empty($SERVER_API_KEY)){
			return false;
		}
		//return false;
		/*$title="Ump Community";
		$message="avvv tu";
		$test =  mobileNotification($user,$title,$message);*/

		$device  = $user->Devices()->latest()->first();
		if(empty($device)){
			return false;
		}
		$firebaseToken[]= $device->device_token;
		$device_type ="";
		$insert_data = array(
			'user_id' => $user->id,
			'title' => $title,
			'message' => $message,
			'created_at' => now(),
			'updated_at' => now()
		);
		$subscribe_id = DB::table('notifications')->insert($insert_data);
		$url = 'https://fcm.googleapis.com/fcm/send';
		$data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "ticker" => $title,
                "body" => $message,  
                "vibrate" => 1,  
                "sound" => 1,  
            ]
	    ];
	    $dataString = json_encode($data);

	    
	    
	    $msg = array(
	    	'message' 	=> $message,
	    	'title'		=> $title,
	    	'vibrate'	=> 1,
	    	'sound'		=> 1,
	    	"ticker" => $title,
	    );
	    $fields = array(
	    	'registration_ids' 	=> $firebaseToken,
	    	'data'			=> $msg
	    );
	    //$dataString = json_encode($fields);

		
		$headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$dataString);
		$result = curl_exec($ch);
		//print_r($result);die;
		if($result === false)
		{
			$result_noti = 0;
		} else {
			$result_noti = 1;
		}
		curl_close($ch);
		return $result_noti;

	}
}

if(!function_exists('withdrawEligible')){
	function withdrawEligible($user){

		$request_date_validation = MonthLastSEvenDate();
		$start = $request_date_validation['month_start'];
		$end = $request_date_validation['month_end'];
		$present = todayDate();
		if( ($start > $present) ){ 
			return 0;
		}
		return 1;


		$week = $user->week_withdraw;		
		$date = date('d');
		switch ($week) {
		  case 'week1':
		  		if($date >=1 && $date <=7){
		  			return 1;
		  		}else{
		  			return 0;
		  		}
		    break;
		  case 'week2':
		  		if($date >=8 && $date <=14){
		  			return 1;
		  		}else{
		  			return 0;
		  		}
		    break;
		  case 'week3':
		  		if($date >=15 && $date <=21){
		  			return 1;
		  		}else{
		  			return 0;
		  		}
		    break;
		  case 'week4':
		  		if($date >=22 && $date <=28){
		  			return 1;
		  		}else{
		  			return 0;
		  		}
		    break;

		  default:
		  	return 0;
		}
	}
}


if(! function_exists('TicketsHistory')){
	function TicketsHistory($Tickets){
		$return = array();
		foreach ($Tickets as $key => $Ticket) {
			$return[$key]['id'] =$Ticket->id;
			$return[$key]['title'] =$Ticket->title;
			$return[$key]['description'] =$Ticket->description;
			$return[$key]['ticket_id'] =$Ticket->ticket_id;
			$return[$key]['status'] =$Ticket->status;
			$return[$key]['attachment_one'] = !empty($Ticket->attachment_one)?url('uploads/Ticket/'.$Ticket->attachment_one):null;
			$return[$key]['attachment_two'] = !empty($Ticket->attachment_two)?url('uploads/Ticket/'.$Ticket->attachment_two):null;
			$return[$key]['date']= $Ticket->created_at->format(config('site_setting.DATE_FORMATE','m/d/Y'));
			$return[$key]['time']= $Ticket->created_at->format("H:i:s");
		}

		return $return;
	}
}

if(! function_exists('AnnouncementsAll')){
	function AnnouncementsAll($Announcements){
		$return = array();
		foreach ($Announcements as $key => $Announcement) {
			$return[$key]['id'] =$Announcement->id;
			$return[$key]['title'] =$Announcement->title;
			$return[$key]['message'] =$Announcement->message;
			$image = "";

			if(!empty($Announcement->image))
			    $image = asset('uploads/Announcement/'.$Announcement->image);

			 if(!empty($Announcement->image_link))
			    $image =$Announcement->image_link;
			
			$return[$key]['image'] = $image;
			$return[$key]['date']= $Announcement->created_at->format(config('site_setting.DATE_FORMATE','m/d/Y'));
			$return[$key]['time']= $Announcement->created_at->format("H:i:s");
		}

		return $return;
	}
}

if(! function_exists('NotificatonAll')){
	function NotificatonAll($Notificatons){
		$return = array();
		foreach ($Notificatons as $key => $Notificaton) {
			$return[$key]['id'] =$Notificaton->id;
			$return[$key]['title'] =$Notificaton->title;
			$return[$key]['message'] =$Notificaton->message;
			$image = "";			
			$return[$key]['date']= $Notificaton->created_at->format(config('site_setting.DATE_FORMATE','m/d/Y'));
			$return[$key]['time']= $Notificaton->created_at->format("H:i:s");
		}

		return $return;
	}
}

if(! function_exists('MakeDummyAccount')){
	function MakeDummyAccount($user){
		$user->name="DUMMY";
        $user->email="DUMMY".$user->id."@dummy.com";
        $user->is_dummy=1;
        $user->birthdate=NULL;
        $user->phone=NULL;
        $user->province_id=NULL;
        $user->town_id=NULL;
        $user->status=0;
        $user->is_block=1;
        $user->is_phone_verify=0;
        $user->is_email_verify=0;
        $user->investment_limit=0;
        $user->wallet=0;
        $user->slot_earning=0;
        $user->over_all_earning=0;
        $user->reward_earning=0;
        $user->purchase_pin_count=0;
        $user->total_slots=0;
        $user->document=NULL;
        $user->isverify=0;
        $user->p_to_p_limit=0;
        $user->p_to_p_commission=100;
        $user->deleted_at=now();
        $user->save();
	}

}