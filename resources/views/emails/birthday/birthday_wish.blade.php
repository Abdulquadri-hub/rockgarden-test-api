@extends('emails.mail')
@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        @if($type === 'staff')
            <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Happy Birthday {{ $staff->user->first_name }} {{ $staff->user->last_name }}!</strong></span></p>
            <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
            <p style="font-size: 14px; line-height: 160%;">
                On behalf of the entire Rockgarden Homes team, we wish you a wonderful birthday filled with joy and celebration.
            </p>
            <p style="font-size: 14px; line-height: 160%;">
                Thank you for being an invaluable member of our team!
            </p>
        @else
            <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Happy Birthday {{ $staff->user->first_name }} {{ $staff->user->last_name }}!</strong></span></p>
            <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
            <p style="font-size: 14px; line-height: 160%;">
                Rockgarden Homes wishes you a wonderful birthday! Thank you for being a valued part of our community.
            </p>
        @endif
        
        <p style="font-size: 14px; line-height: 160%;">
            Best regards,
        </p>
    </div>
@endsection