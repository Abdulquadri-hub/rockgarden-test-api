<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\HealthIssueAdminEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\HealthIssueAdminMail;
use App\Mail\HealthIssueAdminUpdatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HealthIssueAdminEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\HealthIssueAdminEvent  $event
     * @return void
     */
    public function handle(HealthIssueAdminEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), true);

            $contact = $res['contact'];
            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::HEALTH_ISSUE_ADMIN){
                if(!empty($contact)){
                    //Send Email
                    if (filter_var($contact['email'], FILTER_VALIDATE_EMAIL) && $send_email) {
                        Mail::to($contact['email'])->send(new HealthIssueAdminMail($dto->getClientFullname(), $dto->getDashboardLink()));
                    }

                    // Send Sms to user
                    if(!empty($contact['phone']) && $send_sms){
                        TwilioSMSController::sendSMS($contact['phone'], TwilioSMSController::healthIssueAdminMessage($dto->getClientFullname(), $dto->getDashboardLink()));
                    }
                }
            }else if($dto->getType() == EventType::HEALTH_ISSUE_ADMIN_UPDATED){
                if(!empty($contact)){
                    //Send Email
                    if (filter_var($contact['email'], FILTER_VALIDATE_EMAIL) && $send_email) {
                        Mail::to($contact['email'])->send(new HealthIssueAdminUpdatedMail($dto->getClientFullname(), $dto->getHealthIssueName(),$dto->getDashboardLink()));
                    }

                    // Send Sms to user
                    if(!empty($contact['phone']) && $send_sms){
                        TwilioSMSController::sendSMS($contact['phone'], TwilioSMSController::healthIssueAdminUpdatedMessage($dto->getClientFullname(), $dto->getHealthIssueName(),$dto->getDashboardLink()));
                    }
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
