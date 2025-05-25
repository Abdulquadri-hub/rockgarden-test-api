@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Hello Dear,</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            Unpaid-Bill invoice Amount is showing as below  . Please login via app to view more.
        <div style="text-align: left">
            <span>* Payment Name:{{ Carbon\Carbon::parse($details['payment']['created_at'])->format('F j, Y')}}  Unpaid Bill</span>
            <br/>
            <span>* Client Name:{{$details['user_data']['first_name']}} {{$details['user_data']['last_name']}} </span>
            <br/>
         
        <span>* Total Amount (NGN):  {{ number_format($details['total_ngn'], 0, '.', ',') }} </span>
        <br>
       <span>* Total Amount (USD): {{ number_format($details['total_usd'], 2, '.', ',') }}</span>


            <br/>
            <span>* Due Date:{{$details['due_date']}} </span>
            <br/>
            {{-- @if(!empty($pay_link)) --}}
            {{-- <span>* Click here to pay now <a href="{{$details['button']}}"></a> </span> --}}
            <p>
              <a style="text-decoration: none;width: 100%;" href="{{$details['invoice_reference']}}"><button id="pay_button" type="button" style="display: block;width: 100%;height: 40px;background: #1282d2;color: #fff;border-radius: 10px;border: 0px;margin-top: 10px;cursor: pointer;">Click Here to Pay</button></a>
            </p>
            
            {{-- @endif --}}
        </div>
        </p>
    </div>
 


@endsection