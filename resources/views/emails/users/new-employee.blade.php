@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear {{$name}},</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            An employee account has been created for you to access Rockgarden EHR app and perform assigned operations.
            <br/>
        <strong style="text-align: left"> — Account Details —</strong>
        <div style="text-align: left">
            <span><strong>Username</strong>: {{$email}}</span>
            <br/>
            <span><strong>Password</strong>: {{$password}}</span>
            <br/>
            <span><strong>Roles</strong>: {{$roles}}</span>
        </div>
        <br/>
        Please ensure to update your account password on first login.
        </p>
    </div>
@endsection

