@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;">
            <span style="font-size: 18px; line-height: 28.8px;">
                <strong>New Announcement: {{ $announcement->title }}</strong>
            </span>
        </p>
        
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        
        <p style="font-size: 14px; line-height: 160%;">
            A new announcement has been posted.
        </p>
        
        <p style="font-size: 14px; line-height: 160%;">
            Title: {{ $announcement->title }}
        </p>
        
        <p style="font-size: 14px; line-height: 160%;">
            Priority: {{ ucfirst($announcement->priority) }}
        </p>
        
        <p style="font-size: 14px; line-height: 160%;">
            Thank you!
        </p>
        
        <p style="font-size: 14px; line-height: 160%;">
            Best regards,
        </p>
    </div>
@endsection