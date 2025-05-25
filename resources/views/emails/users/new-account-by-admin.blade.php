@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear {{$fullname}},</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            A user account has been created for you on the Rockgarden EHR platform to give you secured access to your loved one’s health and care record. 
            <br/>
        <strong style="text-align: left"> — Account Details —</strong>
        <div style="text-align: left">
            <span><strong>Username</strong>: {{$email}}</span>
            <br/>
            <span><strong>Password</strong>: {{$password}}</span>
        </div>
        <br/>
        Please ensure you update your account password on your first login.
        </p>
    </div>
@endsection

