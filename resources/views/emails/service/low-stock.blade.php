@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear admin,</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            {{$item_name}} is low in stock. Only {{$total_item_in_stock}} unit(s) is left.Please login to the Rockgarden EHR’s dashboard to view the details.<a href="{{$dashboard_link}}">{{$dashboard_link}}</a>
        </p>
    </div>
@endsection

