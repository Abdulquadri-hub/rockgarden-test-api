@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Hello Dear,</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
           Thank you for your succesfull invoice payment. Should you have any questions or require further assistance, please do not hesitate to contact us.
        <div style="text-align: left">
            <span>* Payment Name:{{$payer_name}}</span>
            <br/>
            <span>* Client Name: {{$client_fullname}}</span>
            <br/>
            <span>* Amount Paid: {{$currency}}{{$amount}}</span>
           
            <br/>
            <span>* Transaction Ref: {{$trans_ref}}</span>
            <br/>
            <span>* Transaction Date: {{$trans_date}}</span>
            <br/>
        </div>
        </p>
    </div>
  


<div style="text-align: center;">
  <br>
   
  <!--<h3 style="text-align: center;margin-bottom: 5px;">Rate Assigned Staff</h3>-->
<p>
              <a style="text-decoration: none;width: 100%;" href="#"><button id="pay_button" type="button" style="display: block;width: 100%;height: 40px;background: #1282d2;color: #fff;border-radius: 10px;border: 0px;margin-top: 10px;cursor: pointer; margin-bottom: 5px;">Rate Our Staff</button></a>
    </p>
  <div style="display: inline-flex;text-align: center;">
      @foreach ($staffDetails as $staffDetail)
    <div style="max-width: 100px;text-align: center;margin: 10px;">
    <div style="width:50px;height:50px; background:{{ isset($staffDetail['file_path']) ? 'none' : 'black' }};border-radius: 50%;margin: 0 auto;margin-bottom: 5px;">
           @if(isset($staffDetail['file_path']))
                    <img src="{{ $staffDetail['file_path'] }}" alt="Staff Image" style="max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 50%;">
                @endif
    </div>
      <div><span>{{ $staffDetail['first_name'] }} {{ $staffDetail['last_name'] }}</span></div>
      <div></div>
<img jslog="138226; u014N:xr6bB; 53:WzAsMF0." src="https://www.pngmart.com/files/7/Rating-Star-PNG-Background-Image.png" style="width: 100%;">
</div>
 @endforeach
</div>
</div>
@endsection