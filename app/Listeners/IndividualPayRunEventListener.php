<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\IndividualPayRunEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\IndividualPayRunMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IndividualPayRunEventListener
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
     * @param  \App\Events\IndividualPayRunEvent  $event
     * @return void
     */
    public function handle(IndividualPayRunEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            //$send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::INDIVIDUAL_PAY_RUN){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new IndividualPayRunMail($dto->getDepartmentName(), $dto->getTaxes(), $dto->getDeductions(), $dto->getPayrunTitle(), $dto->getAllowances(), $dto->getBonuses(), $dto->getDateTo(), $dto->getDateFrom(), $dto->getTotalAmount(), $dto->getTotalCurrency(), $dto->getAccountNo(), $dto->getDutyType(), $dto->getBankName(), $dto->getDesignationName(), $dto->getEmpoyeeFullname(), $dto->getAmount(), $dto->getCurrency(), $dto->getPaymentLink()));
                }

//            // Send Sms to user
//            if(!empty($event->phone_num)){
//                TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::p($dto->getApplicantFullname()));
//            }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
