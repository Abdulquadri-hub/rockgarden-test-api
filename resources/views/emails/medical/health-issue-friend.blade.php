@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear {{$name}},</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            A new health issue has been recorded for {{$client_fullname}}. Please login to the Rockgarden EHR app to view the details..
        </p>
    </div>
@endsection

