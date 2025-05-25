@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear {{$family_friend_name}},</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            A new invoice has been created for {{$client_fullname}} . Please login to the Rockgarden EHR app to view the details. 
        <div style="text-align: left">
            <span>* Payment Name:{{$invoice_name}}</span>
            <br/>
            <span>* Client Name: {{$client_fullname}}</span>
            <br/>
            <span>* Total Amount: {{$currency}}{{$amount}}</span>
            <br/>
            <span>* Due Date: {{$due_date}}</span>
            <br/>
            @if(!empty($payment_link))
                <span>* Click here to pay now <a href="{{$payment_link}}">{{$payment_link}}</a> </span>
            @endif
        </div>
        </p>
    </div>
@endsection

