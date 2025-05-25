@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
    @if($type === 'staff')
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Staff Birthday Notification</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            This is a reminder that it's {{ $staff->user->first_name }} {{ $staff->user->last_name }}'s birthday today!
        </p>
    @else
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Client Birthday Notification</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            This is a reminder that it's {{ $staff->user->first_name }} {{ $staff->user->last_name }}'s birthday today!
        </p>        
    @endif
        <p style="font-size: 14px; line-height: 160%;">
            Best regards,
        </p>
    </div>
@endsection