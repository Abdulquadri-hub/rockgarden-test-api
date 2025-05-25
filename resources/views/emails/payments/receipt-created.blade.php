@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear {{$payer_name}},</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            Your payment was successful. Please login to the Rockgarden EHR app to view the details. We appreciate your prompt payment and look forward to continuing to work with you. If you have any concerns, please do not hesitate to contact us.
        <div style="text-align: left">
            <span>* Payment Name:{{$invoice_name}}</span>
            <br/>
            <span>* Client Name: {{$client_fullname}}</span>
            <br/>
            <span>* Amount Payable: {{$currency}}{{$amount}}</span>
            <br/>
            <span>* Total Amount Paid: {{$currency}}{{$amount_paid}}</span>
            <br/>
            <span>* Transaction Ref: {{$receipt_id}} | {{$trans_ref}}</span>
            <br/>
            <span>* Transaction Date: {{$trans_date}}</span>
            <br/>
        </div>
        </p>
    </div>
<!--    <div style="text-align: center;">-->
<!--  <br><h3 style="text-align: center;margin-bottom: 5px;">Rate Assigned Staff</h3>-->
<!--<p>You can rate assigned staff when you download <a href="#">our mobile app</a></p>-->
<!--  <div style="display: flex;">-->
<!--    <div style="max-width: 100px;text-align: center;background-image: url(https://www.google.com/url?sa=i&amp;url=https%3A%2F%2Fwww.pngmart.com%2Fimage%2Ftag%2Fstar-rating&amp;psig=AOvVaw0Z0SjsI6AI3S4sPVVwny6c&amp;ust=1687530214751000&amp;source=images&amp;cd=vfe&amp;ved=0CBEQjRxqFwoTCLjGooqK1_8CFQAAAAAdAAAAABAn);margin: 10px;">-->
<!--    <div style="width:50px;height:50px; background:black;border-radius: 50%;margin: 0 auto;margin-bottom: 5px;"></div>-->
<!--      <div><span>Stella Maxwell</span></div>-->
<!--      <div></div>-->
<!--<img jslog="138226; u014N:xr6bB; 53:WzAsMF0." src="https://www.pngmart.com/files/7/Rating-Star-PNG-Background-Image.png" style="width: 100%;"></div><div style="max-width: 100px;text-align: center;background-image: url(https://www.google.com/url?sa=i&amp;url=https%3A%2F%2Fwww.pngmart.com%2Fimage%2Ftag%2Fstar-rating&amp;psig=AOvVaw0Z0SjsI6AI3S4sPVVwny6c&amp;ust=1687530214751000&amp;source=images&amp;cd=vfe&amp;ved=0CBEQjRxqFwoTCLjGooqK1_8CFQAAAAAdAAAAABAn);margin: 10px;">-->
<!--    <div style="width:50px;height:50px; background:black;border-radius: 50%;margin: 0 auto;margin-bottom: 5px;"></div>-->
<!--      <div><span>Dele Juan</span></div>-->
<!--      <div></div>-->
<!--<img jslog="138226; u014N:xr6bB; 53:WzAsMF0." src="https://www.pngmart.com/files/7/Rating-Star-PNG-Background-Image.png" style="width: 100%;"></div><div style="max-width: 100px;text-align: center;background-image: url(https://www.google.com/url?sa=i&amp;url=https%3A%2F%2Fwww.pngmart.com%2Fimage%2Ftag%2Fstar-rating&amp;psig=AOvVaw0Z0SjsI6AI3S4sPVVwny6c&amp;ust=1687530214751000&amp;source=images&amp;cd=vfe&amp;ved=0CBEQjRxqFwoTCLjGooqK1_8CFQAAAAAdAAAAABAn);margin: 10px;">-->
<!--    <div style="width:50px;height:50px; background:black;border-radius: 50%;margin: 0 auto;margin-bottom: 5px;"></div>-->
<!--      <div><span>Makinwa Steven</span></div>-->
<!--      <div></div>-->
<!--<img jslog="138226; u014N:xr6bB; 53:WzAsMF0." src="https://www.pngmart.com/files/7/Rating-Star-PNG-Background-Image.png" style="width: 100%;"></div><div style="max-width: 100px;text-align: center;background-image: url(https://www.google.com/url?sa=i&amp;url=https%3A%2F%2Fwww.pngmart.com%2Fimage%2Ftag%2Fstar-rating&amp;psig=AOvVaw0Z0SjsI6AI3S4sPVVwny6c&amp;ust=1687530214751000&amp;source=images&amp;cd=vfe&amp;ved=0CBEQjRxqFwoTCLjGooqK1_8CFQAAAAAdAAAAABAn);margin: 10px;">-->
<!--    <div style="width:50px;height:50px; background:black;border-radius: 50%;margin: 0 auto;margin-bottom: 5px;"></div>-->
<!--      <div><span>Sarah Mathew</span></div>-->
<!--      <div></div>-->
<!--<img jslog="138226; u014N:xr6bB; 53:WzAsMF0." src="https://www.pngmart.com/files/7/Rating-Star-PNG-Background-Image.png" style="width: 100%;"></div>-->
<!--</div>-->
<!--</div>-->
@endsection
