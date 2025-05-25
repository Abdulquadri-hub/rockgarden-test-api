<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewAccountMail;
use App\Mail\TestRockMail;
use Exception;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class TwilioSMSController extends Controller
{
    /**
     * @return void
     */
    public function index()
    {

        $receiverNumber = "+2348097175974";
        $message = "This is testing from Rockgarden https://www.google.com";

        try {
            $account_sid = env("TWILIO_SID");
            $auth_token = env("TWILIO_TOKEN");
            $twilio_number = env("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);

            $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message]);

            dd('SMS Sent Successfully.');

        } catch (Exception $e) {

            dd("Error: ". $e->getMessage());

        }
    }

    public static function sendSMS($receiverNumber, $message)
    {
        if(empty($receiverNumber)){
            return false;
        }

        try {
            $account_sid = env("TWILIO_SID");
            $auth_token = env("TWILIO_TOKEN");
            $twilio_number = env("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);

            $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message]);
            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
   public static function sms(Request $request)
{
   
    $toNumber = '+923415202903';
    $message = 'Hello Dev its testing' ;

    if (Helper::sendSms($toNumber, $message)) {
        // SMS sent successfully
        return response()->json([
            'success' => true,
            'message' => 'SMS sent successfully.',
        ]);
    } else {
        // Failed to send SMS
        return response()->json([
            'success' => false,
            'message' => 'Failed to send SMS.',
        ]);
    }
}

    public static function accountActivatedMessageMessage($user_fullname){
        return "Dear $user_fullname, your account has been fully activated.";
    }

    public static function accountDeActivatedMessage($user_fullname){
        return "Dear $user_fullname, your account has been restricted. You no longer have access to the Rockgarden EHR platform.";
    }

    public static function applicationApprovedMessage($name){
        return "Dear $name, Your application for service has been approved. An agent will contact you soon.";
    }

    public static function applicationRejectedMessage($name, $reason_for_rejection){
        return "Dear $name, Your application for service has was rejected due to $reason_for_rejection.";
    }

    public static function deathRecordAdminMessage($client_fullname, $dashboard_link){
        return "Dear admin, A new death record has been recorded for $client_fullname. Please login to dashboard to view more. $dashboard_link";
    }

    public static function documentClientCreatedMessage($familyfriend_name, $client_fullname){
        return "Dear $familyfriend_name, We have added a new document record to the profile of your loved one $client_fullname. Login to the Rockgarden EHR app with your credentials to view the details.";
    }

    public static function documentClientUpdatedMessage($familyfriend_name, $client_fullname){
        return "Dear $familyfriend_name, We have updated the document records of your loved one $client_fullname. Login to the Rockgarden EHR app with your credentials to view the details.";
    }

    public static function documentStaffCreatedMessage($fullname){
        return "Dear $fullname, A new document record has been added to your profile. Login to the Rockgarden EHR app with your credentials to view the details.";
    }

    public static function documentStaffUpdatedMessage($fullname){
        return "Dear $fullname, Your document records has been updated. Login to the Rockgarden EHR app with your credentials to view the details.";
    }

    public static function familyFriendCreatedMessage($familyfriend_name, $client_fullname){
        return "Dear $familyfriend_name, You can now view the confidential records of your loved one  $client_fullname. Login to the app to get started.";
    }

    public static function familyFriendUpdatedMessage($familyfriend_name, $client_fullname){
        return "Dear $familyfriend_name, You can now view the secured records of your loved one  $client_fullname. Login to the app to get started.";
    }

    public static function healthIssueAdminMessage($client_fullname, $dashboard_link){
        return "Dear admin, A new health issue has been recorded for $client_fullname. Please login to dashboard to view more. $dashboard_link";
    }

    public static function healthIssueAdminUpdatedMessage($client_fullname, $health_issue_name, $dashboard_link){
        return "Dear admin, health issue $health_issue_name details has been updated for $client_fullname. Please login to dashboard to view more. $dashboard_link";
    }

    public static function healthIssueFriendMessage($client_fullname, $family_friend_name){
        return "Dear $family_friend_name, A new health issue has been recorded for $client_fullname. Please login via app to view more.";
    }

    public static function healthIssueFriendUpdatedMessage($client_fullname, $health_issue_name, $family_friend_name){
        return "Dear $family_friend_name, health issue $health_issue_name details has been updated for $client_fullname. Please login via app to view more.";
    }

//    public static function individualPayRunMessage($department_name, $taxes, $deductions, $payrun_title, $allowances, $bonuses, $date_to, $date_from, $total_amount, $total_currency, $account_no, $duty_type, $bank_name, $designation_name, $empoyee_fullname, $amount, $currency, $payment_link){
//        return "
//
//        ";
//    }

    public static function invoiceCreatedMessage($family_friend_name, $client_fullname, $invoice_name, $amount, $currency, $due_date, $payment_link){
        return "Dear {{$family_friend_name}}, A new invoice has been created for {{$client_fullname}} . Please login via app to view more.\n* Payment Name: $invoice_name
                * Client Name: $client_fullname\n
                * Total Amount: $currency $amount\n
                * Due Date: $due_date\n
                * Click here to pay now $payment_link\n";
    }

    public static function lowStockMessage($item_name, $total_item_in_stock, $dashboard_link){
        return "Dear admin, $item_name is low in stock. Only $total_item_in_stock unit(s) is left. Please login to dashboard to view more. $dashboard_link";
    }

    public static function medicalVisitAdminMessage($client_fullname, $dashboard_link){
        return "Dear admin, A new medical visit has been recorded for $client_fullname. Please login to dashboard to view more. $dashboard_link";
    }

    public static function medicalVisitFriendMessage($client_fullname, $family_friend_name){
        return "Dear $family_friend_name, A new medical visit has been recorded for $client_fullname. ";
    }

    public static function medicationMissedAdminMessage($client_fullname, $dashboard_link, $date){
        return "Dear admin, $client_fullname has missed one or more medication intake for $date. Please login to dashboard to view more. $dashboard_link";
    }
     public static function medicationAdminMessage($client_fullname, $dashboard_link, $date){
        return "Dear admin, $client_fullname has a medication intake scheduled for $date. Please login to dashboard to view more. $dashboard_link";
    }

    public static function medicationMissedFriendMessage($client_fullname, $family_friend_name, $date){
        return "Dear $family_friend_name, $client_fullname has a medication intake scheduled for $date. Please login via app to view more.";
    }
    public static function medicationFriendMessage($client_fullname, $family_friend_name, $date){
        return "Dear $family_friend_name, $client_fullname has missed one or more medication intake for $date. Please login via app to view more.";
    }

    public static function newAccountByAdminMessage($fullname, $email, $password){
        return "Dear $fullname, A user account has been created for you on the Rockgarden EHR platform to give you secured access to your loved one’s health and care record.\n
            — Account Details —\n
            Username: $email\n
            Password: $password\n
            Please ensure you update your account password on your first login.";
    }

    public static function newAccountMessage($name, $digit_code){
        return "Hello ".$name.", ".$digit_code." is your verification code for Rockgarden EHR.";
    }

    public static function newApplicationMessage($applicant_fullname, $dashboard_link){
        return "Dear admin, a new service application has been submitted by $applicant_fullname. Please login to dashboard to view more. $dashboard_link";
    }

    public static function newEmployeeMessage($name, $email, $password, $roles){
        $user_role = "";
        if(!empty($roles)){
            foreach ($roles as $role){
                $user_role.$role.", ";
            }
        }
        return "Dear {{$name}}, An employee account has been created for you to access Rockgarden EHR app and perform assigned operations.\n
        — Account Details —\n
         Username: $email\n
         Password: $password\n
         Roles: $user_role\n";
    }

    public static function newIncidentAdminMessage($client_fullname, $dashboard_link){
        return "Dear admin, a new incident has been reported for resident ($client_fullname). Please login to dashboard to view more. $dashboard_link";
    }

    public static function prescriptionAdminCreatedMessage($client_fullname, $dashboard_link, $medicine_name){
        return "Dear admin, A new medical prescription ($medicine_name) has been charted for $client_fullname. Please login to dashboard to view more. $dashboard_link";
    }

    public static function prescriptionAdminUpdatedMessage($client_fullname, $dashboard_link, $medicine_name){
        return "Dear admin, medical prescription ($medicine_name) was updated for $client_fullname. Please login to dashboard to view more. $dashboard_link";
    }

    public static function prescriptionFriendCreatedMessage($client_fullname, $family_friend_name, $medicine_name){
        return "Dear $family_friend_name, A new medical prescription ($medicine_name) has been charted for $client_fullname. Please login via app to view more.";
    }

    public static function prescriptionFriendUpdatedMessage($client_fullname, $family_friend_name, $medicine_name){
        return "Dear $family_friend_name, medical prescription ($medicine_name) was updated for $client_fullname. Please login via app to view more.";
    }

    public static function procedureAdminMessage($item_name, $client_fullname, $dashboard_link){
        return "Dear admin, procedure ($item_name) was carried out on $client_fullname. Please login to dashboard to view more. $dashboard_link";
    }

    public static function procedureFriendMessage($family_friend_name, $item_name, $client_fullname){
        return "Dear $family_friend_name, procedure ($item_name) was carried out on $client_fullname. Please login via app to view more.";
    }

    public static function receiptCreatedMessage($payer_name, $client_fullname, $trans_ref, $receipt_id, $trans_date, $invoice_name, $amount, $amount_paid, $currency){
        return "Dear $payer_name, Your payment was successful. We appreciate your prompt payment and look forward to continuing to work with you.\n
                * Payment Name: $invoice_name\n
                * Client Name: $client_fullname\n
                * Amount Payable: $currency $amount\n
                * Total Amount Paid: $currency $amount_paid\n
                * Transaction Ref: $receipt_id | $trans_ref\n
                * Transaction Date: $trans_date";
    }

    public static function resetPasswordMessage($name, $digit_code){
        return "Hello $name, your verification code for password reset is $digit_code.";
    }

    public static function staffAssignmentCreatedMessage($fullname, $client_fullname){
        return "Dear $fullname, You have been assigned a new resident ($client_fullname). Login to app with your credentials to get started.";
    }

    public static function staffAssignmentUpdatedMessage($fullname, $client_fullname){
        return "Dear $fullname, You have been assigned a new resident ($client_fullname). Login to app with your credentials to get started.";
    }

    public static function successRegistrationMessage($name){
        return "Dear $name, Welcome to Rockgarden EHR. Please feel free to contact us via our social and support channels if you have any questions or further enquires for assistance.";
    }

    public function mail(){
        // Mail::to('labayifa@gmail.com')->send(new TestRockMail('SAGBO Carmel Prosper'));
        Mail::to('ogbonnagideon5@gmail.com')->send(new NewAccountMail('Gideon', '012345'));
        return view('emails.mail');
    }
}
