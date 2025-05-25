@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear {{$empoyee_fullname}}, </strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            your payroll has been processed. Here is the summary for the month.
            <br/>
            <strong style="text-align: left"> EMPLOYEE</strong>
        <div style="text-align: left">
            <span>* Name:{{$empoyee_fullname}}</span>
            <br/>
            <span>* Department: {{$department_name}}</span>
            <br/>
            <span>* Designation: {{$designation_name}}</span>
            <br/>
            <span>* Duty Type: {{$duty_type}}</span>
            <br/>
            <span>* Bank Name: {{$bank_name}}</span>
            <br/>
            <span>* Bank Account No.: {{$account_no}}</span>
            <br/>
            <span>* Click here to pay now <a href="{{$payment_link}}">{{$payment_link}}</a></span>
        </div>
        <br/>
        <strong style="text-align: left"> PAYRUN </strong>
        <div style="text-align: left">
            <span>* Title:{{$payrun_title}}</span>
            <br/>
            <span>* Date Range:  {{$date_from}} - {{$date_to}}</span>
            <br/>
            <span>* Basic Salary: {{$currency}}{{$amount}}</span>
        </div>
        <br/>
        @if(!empty($bonuses))
            <strong style="text-align: left"> — BONUSES </strong>
            <div style="text-align: left">
                @foreach($bonuses as $key => $bonus)
                    <span>* {{$bonus['name']}} — {{$bonus['currency']}}{{$bonus['amount']}}</span>
                    <br/>
                @endforeach
            </div>
            <br/>
        @endif
        @if(!empty($allowances))
            <strong style="text-align: left"> ALLOWANCES </strong>
            <div style="text-align: left">
                @foreach($allowances as $key => $allowance)
                    <span>* {{$allowance['name']}} — {{$allowance['currency']}}{{$allowance['amount']}}</span>
                    <br/>
                @endforeach
            </div>
            <br/>
        @endif
        @if(!empty($bonuses))
            <strong style="text-align: left"> DEDUCTIONS </strong>
            <div style="text-align: left">
                @foreach($deductions as $key => $deduction)
                    <span>* {{$deduction['name']}} — {{$deduction['currency']}}{{$deduction['amount']}}</span>
                    <br/>
                @endforeach
            </div>
            <br/>
        @endif
        @if(!empty($bonuses))
            <strong style="text-align: left"> DEDUCTIONS </strong>
            <div style="text-align: left">
                @foreach($deductions as $key => $deduction)
                    <span>* {{$deduction['name']}} — {{$deduction['currency']}}{{$deduction['amount']}}</span>
                    <br/>
                @endforeach
            </div>
            <br/>
        @endif
        @if(!empty($bonuses))
            <strong style="text-align: left"> TAXES </strong>
            <div style="text-align: left">
                @foreach($taxes as $key => $taxe)
                    <span>* {{$taxe['name']}} — {{$taxe['currency']}}{{$taxe['amount']}}</span>
                    <br/>
                @endforeach
            </div>
            <br/>
        @endif
        TOTAL   —  {{$total_currency}}{{$total_amount}}
        </p>
    </div>
@endsection


