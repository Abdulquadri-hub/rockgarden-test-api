<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IndividualPayRunMail extends Mailable
{
    use Queueable, SerializesModels;

    private $department_name, $taxes, $deductions, $payrun_title, $allowances, $bonuses, $date_to, $date_from, $total_amount, $total_currency, $account_no, $duty_type, $bank_name, $designation_name, $empoyee_fullname, $amount, $currency, $payment_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($department_name, $taxes, $deductions, $payrun_title, $allowances, $bonuses, $date_to, $date_from, $total_amount, $total_currency, $account_no, $duty_type, $bank_name, $designation_name, $empoyee_fullname, $amount, $currency, $payment_link)
    {
        $this->empoyee_fullname = $empoyee_fullname;
        $this->department_name = $department_name;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->total_amount = $total_amount;
        $this->total_currency = $total_currency;
        $this->designation_name = $designation_name;
        $this->duty_type = $duty_type;
        $this->bank_name = $bank_name;
        $this->account_no = $account_no;
        $this->payrun_title = $payrun_title;
        $this->payment_link = $payment_link;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->bonuses = $bonuses;
        $this->allowances = $allowances;
        $this->deductions = $deductions;
        $this->taxes = $taxes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $empoyee_fullname = $this->empoyee_fullname;
        $department_name = $this->department_name;
        $amount = $this->amount;
        $currency = $this->currency;
        $total_amount = $this->total_amount;
        $total_currency = $this->total_currency;
        $designation_name = $this->designation_name;
        $duty_type = $this->duty_type;
        $bank_name = $this->bank_name;
        $account_no = $this->account_no;
        $payrun_title = $this->payrun_title;
        $payment_link = $this->payment_link;
        $date_from = $this->date_from;
        $date_to = $this->date_to;
        $bonuses = $this->bonuses;
        $allowances = $this->allowances;
        $deductions = $this->deductions;
        $taxes = $this->taxes;

        return $this->subject('Payroll')->view('emails.service.individual-payrun', compact( 'empoyee_fullname', 'total_amount', 'total_currency', 'currency', 'amount', 'date_from', 'date_to', 'allowances', 'bonuses', 'deductions', 'taxes', 'payrun_title', 'department_name', 'designation_name', 'account_no', 'bank_name', 'duty_type', 'payment_link'));
    }
}
