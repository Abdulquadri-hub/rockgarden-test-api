@extends('emails.mail')
@section('content')

    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;">
            <span style="font-size: 18px; line-height: 28.8px;">
                <strong>Message from {{ config('app.name') }}</strong>
            </span>
        </p>
        
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        
        <p style="font-size: 14px; line-height: 160%;">
            <strong>Subject:</strong> {{ $messageData->subject }}
        </p>

        <p style="font-size: 14px; line-height: 160%;">
            {!! nl2br(e($messageData->body)) !!}
        </p>

        @if($messageData->attachments->count() > 0)
            <p style="font-size: 14px; line-height: 160%;">
                <strong>Attachments:</strong>
            </p>
            <ul>
                @foreach($messageData->attachments as $attachment)
                    <li>{{ $attachment->file_name }}</li>
                @endforeach
            </ul>
         @else
           <h1>No attachments found</h1>
        @endif
       
        

        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        
        <p style="font-size: 14px; line-height: 160%;">
            Best regards,<br>
            {{ $messageData->sender->first_name }} {{ $messageData->sender->last_name }}<br>
            {{ config('app.name') }}
        </p>

        @if(isset($messageData->parent_id))
            <hr style="border-top: 1px solid #dee2e6; margin: 20px 0;">
            <p style="font-size: 12px; line-height: 160%; color: #6c757d;">
                This message is in reply to an earlier conversation.
            </p>
        @endif
    </div>
@endsection
