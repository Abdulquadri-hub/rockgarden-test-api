@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear admin,</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            a new service application has been submitted by {{$applicant_fullname}}.
            <br>
            Please login to dashboard to view more. <a href="{{$dashboard_link}}">{{$dashboard_link}}</a>
        </p>
    </div>
@endsection

