<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Unpaid Invoice</title>
    <link rel="stylesheet" href="style.css" media="all" />
    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #5D6975;
            text-decoration: underline;
        }

        body {
            position: relative;
            width: fit-content height: 29.7cm;
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-family: Arial;
        }

        header,
        main {
            padding: 10px 0;
            margin-bottom: 30px;
        }

        #logo {
            text-align: center;
            margin-bottom: 10px;
        }

        #logo img {
            width: 90px;
        }

        h1 {
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            color: #5D6975;
            font-size: 2.4em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 20px 0;
            background: url('https://api.rockgardenehr.space/images/dimension.png');
        }

        #project {
            float: left;
            width: 50%;
        }

        #project span {
            color: #5D6975;
            text-align: right;
            width: 52px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: right;
            text-align: right;
            width: 50%;
        }

        #project div,
        #company div {
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table tr:nth-child(2n-1) td {
            background: #F5F5F5;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 5px 20px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;

        }

        table td {
            padding: 20px;
            text-align: right;
        }

        table td.service,
        table td.desc {
            vertical-align: top;

        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
            ;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 1.2em;
        }

        footer {
            color: #5D6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #C1CED9;
            padding: 8px 0;
            text-align: center;
        }

        #project .detail-text {
            font-size: 12px;
            width: 250px;
            word-wrap: break-word;
            white-space: pre-wrap;
            text-align: left;
            color: #000;
            align-self: start;
        }

        #project div {
            display: flex;
        }
        .address {
        display: inline-block;
        max-width: 200px; 
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    </style>
</head>

<body>
    <header class="clearfix">
        <div id="logo">
          <img src="https://api.rockgardenehr.space/images/RGH.jpg" alt="logo">
        </div>
        <h1>Unpaid Bill</h1>
      <div>
        <div id="company" class="clearfix" style="margin-right: 1rem;">
          <div>Rockgarden Homes</div>
          <div>191/193 Okeletu-Ijede Road, Elemu Bus-Stop, Ikorodu,<br /> Lagos State, Nigeria.</div>
          <div>+234 816 717 6778</div>
          <div><a href="mailto:info@rockgardenhomecareagency.com?subject=&amp;body=">info@rockgardenhomecareagency.com</a></div>
        </div>
        <div id="project" >
          <div><span>CLIENT</span> {{$inv_user->first_name}} {{$inv_user->last_name}}</div>
           <table style="margin-bottom: 0;">
    <tbody>
        <tr>
            <td style="background: initial; padding: 0; text-align: left; width: 65px;">
                <span>ADDRESS</span>
            </td>
            <td align="right" style="padding: 0; text-align: right; background: initial;">
                {{ $inv_user->home_address }},<br>
                {{ $inv_user->city }}, {{ $inv_user->state }}
            </td>
        </tr>
    </tbody>
</table>


          <div><span>EMAIL</span> <a href="mailto:{{$inv_user->email}}">{{$inv_user->email}}</a></div>
          <div><span>DATE</span> {{$date = Carbon\Carbon::now()->toDateTimeString()}}</div>
        </div>
      </div>
      </header>
    <main>
        <div>
            
                <table>
                    <thead>
                        <tr>
                            <th class="service">Product/Service</th>
                            <th>Item Count</th>
                            <th>Unpaid Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedItems = $inv->groupBy('payment_name');
                        @endphp
                        @foreach ($groupedItems as $paymentName => $items)
                        <tr>
                            <td class="service">{{ $paymentName }}</td>
                            <td>{{ count($items) }}</td>
                            @php
                                $unpaidAmount = 0;
                                foreach ($items as $item) {
                                    $unpaidAmount += $item->payment_amount - $item->total_amount_paid;
                                }
                                if ($items[0]->currency === 'USD') {
                                    $conversionRate = App\Helpers\Helper::usdToNgn($unpaidAmount);
                                    $unpaidAmount = $conversionRate;
                                }
                            @endphp
                            <td>NGN {{ number_format($unpaidAmount, 0, '.', ',') }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="grand total"></td>
                            <td class="grand total">
                                Grand Total (NGN): {{ number_format($total_ngn, 0, ',', ',') }}
                                <br><br />
                                Grand Total (USD): ${{ number_format($total_usd, 2, '.', ',') }}
                            </td>
                        </tr>
                    </tbody>
                </table>>
            </table>
        </div>
        <div id="notices">

        </div>
    </main>
    <footer>
        Invoice was created on a computer and is valid without the signature and seal.
    </footer>
</body>

</html>
