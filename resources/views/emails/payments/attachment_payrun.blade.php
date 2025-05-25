<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Salary Payslip</title>
    <style>
        .container {
            margin: 0 auto;
            max-width: 600px;
        }

        table {
            width: 100%;
        }

        td {
            padding: 3px;
            line-height: 1;
            vertical-align: top;
        }
 tr td {
            padding-top: 0;
        }
       

        #logo {
            text-align: center;
            margin-top: 15px;
        }

        #logo img {
            width: 80px;
        }
    </style>
</head>

<body>
    <div id="logo">
        <img src="https://rockgardenehr.space/images/image-6.png" alt="logo" />
        <img src="https://rockgardenehr.space/images/RGH.jpg" alt="logo" />
    </div>

    <div class="container">
        <table>
            <tr>
                <th colspan="2">
                    <h2>Salary Payslip</h2>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <hr style="color: black; background-color: black" />
                </th>
            </tr>
            <tr>
                <th colspan="2" align="left">
                    <h3>Employees</h3>
                </th>
            </tr>
            <tr>
                <td align="left">Name</td>
                <td align="right">{{$details['empoyee_fullname']}}</td>
            </tr>
            <tr>
                <td align="left">Department</td>
                <td align="right">{{$staffs->department}}</td>
            </tr>
            <tr>
                <td align="left">Designation</td>
                <td align="right">{{$staffs->designation}}</td>
            </tr>
            <tr>
                <td align="left">Duty Type</td>
                <td align="right">{{$staffs->duty_type}}</td>
            </tr>
            <tr>
                <td align="left">Bank Name</td>
                <td align="right">{{$staffs->bank_name}}</td>
            </tr>
            <tr>
                <td align="left">Bank Account No</td>
                <td align="right">{{$staffs->bank_account_number}}</td>
            </tr>
            <tr>
                <th colspan="2">
                    <hr style="color: #DFDFDF; background-color: #DFDFDF" />
                </th>
            </tr>
             <tr>
    <th colspan="2" align="left">
        <h3>Payrun</h3>
    </th>
</tr>
<tr>
    <td align="left">Title</td>
    <td align="right">{{$payrun->title}}</td>
</tr>
<tr>
    <td align="left">Date range</td>
    <td align="right"><?php echo date('d M Y', strtotime($payrun->from_date)); ?> - <?php echo date('d M Y', strtotime($payrun->to_date)); ?></td>
    
</tr>
<tr>
    <td align="left">No. of Days Present</td>
    <td align="right">{{$payrun->days_present}}</td>
</tr>
<tr>
    <td align="left">Basic Salary</td>
    <td align="right">{{$payrun->currency}} {{ number_format($payrun->basic_salary, 2) }}</td>
</tr>
<tr>
    <th colspan="2">
        <hr style="color: #DFDFDF; background-color: #DFDFDF" />
    </th>
</tr>
<tr>
    <th colspan="2" align="left">
        <h3>Bonuses</h3>
    </th>
</tr>
@if(!empty($details['bonuses']))
    @foreach($details['bonuses'] as $key => $bonus)
        <tr>
            <td align="left">
                {{$bonus['name']}}
            </td>
            <td align="right">{{$bonus['currency']}} {{ number_format($bonus['amount'], 2) }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td align="left">None</td>
    </tr>
@endif
<tr>
    <th colspan="2">
        <hr style="color: #DFDFDF; background-color: #DFDFDF" />
    </th>
</tr>
<tr>
    <th colspan="2" align="left">
        <h3>Allowances</h3>
    </th>
</tr>
@if(!empty($details['allowances']))
    @foreach($details['allowances'] as $key => $bonus)
        <tr>
            <td align="left">
                {{$bonus['name']}}
            </td>
            <td align="right">{{$bonus['currency']}} {{ number_format($bonus['amount'], 2) }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td align="left">None</td>
    </tr>
@endif
<tr>
    <th colspan="2">
        <hr style="color: #DFDFDF; background-color: #DFDFDF" />
    </th>
</tr>
<tr>
    <th colspan="2" align="left">
        <h3>Deductions</h3>
    </th>
</tr>
@if(!empty($details['deductions']))
    @foreach($details['deductions'] as $key => $bonus)
        <tr>
            <td align="left">
                {{$bonus['name']}}
            </td>
            <td align="right" style="color:red;">-{{$bonus['currency']}} {{ number_format($bonus['amount'], 2) }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td align="left">None</td>
    </tr>
@endif
<tr>
    <th colspan="2">
        <hr style="color: #DFDFDF; background-color: #DFDFDF" />
    </th>
</tr>
<tr>
    <th colspan="2" align="left">
        <h3>Taxes</h3>
    </th>
</tr>
@if(!empty($details['taxes']))
    @foreach($details['taxes'] as $key => $tax)
        <tr>
            <td align="left">{{$tax['name']}}</td>
            <td align="right" style="color:red;">
                 @if(isset($tax['is_fixed']) && $tax['is_fixed'] == 0)
                    {{$tax['currency']}} {{ number_format($tax['amount'], 2) }}
                @else
                    @if(isset($tax['is_fixed']) && $tax['is_fixed'] == 1)
                        @php
                            $totalAmount = $details['amount'];
                            // Add bonuses
                            if (!empty($details['bonuses'])) {
                                foreach ($details['bonuses'] as $bonus) {
                                    $totalAmount += $bonus['amount'];
                                }
                            }
                            // Add allowances
                            if (!empty($details['allowances'])) {
                                foreach ($details['allowances'] as $allowance) {
                                    $totalAmount += $allowance['amount'];
                                }
                            }
                            // Subtract deductions
                            if (!empty($details['deductions'])) {
                                foreach ($details['deductions'] as $deduction) {
                                    $totalAmount -= $deduction['amount'];
                                }
                            }
                            
                            if(!is_null($tax['percentage'])  && $tax['percentage'] > 0)
                            {
                                $calculatedAmount = ($totalAmount * $tax['percentage']) / 100;
                            }else{
                               $calculatedAmount =  $tax['amount'];
                            }
                            
                        @endphp
                        {{$tax['currency']}} {{ number_format($calculatedAmount, 2) }} ({{$tax['percentage'] ?? 0}}%)
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td align="left">None</td>
    </tr>
@endif
<tr>
    <th colspan="2">
        <hr style="color: #DFDFDF; background-color: #DFDFDF" />
    </th>
</tr>
@php
    $total = $details['amount']; // Initialize the total with the basic salary amount

    // Add bonuses
    if (!empty($details['bonuses'])) {
        foreach ($details['bonuses'] as $bonus) {
            $total += $bonus['amount'];
        }
    }

    // Add allowances
    if (!empty($details['allowances'])) {
        foreach ($details['allowances'] as $allowance) {
            $total += $allowance['amount'];
        }
    }

    // Subtract deductions
    if (!empty($details['deductions'])) {
        foreach ($details['deductions'] as $deduction) {
            $total -= $deduction['amount'];
        }
    }

   // Subtract taxes
if (!empty($details['taxes'])) {
    foreach ($details['taxes'] as $tax) {
        if (isset($tax['is_fixed']) && $tax['is_fixed'] == 0) {
            $total -= $tax['amount'];
        } elseif (isset($tax['is_fixed']) && $tax['is_fixed'] == 1) {
        
            if(!is_null($tax['percentage'])  && $tax['percentage'] > 0)
            {
                $calculatedAmount = ($total * $tax['percentage']) / 100;
                $total -= $calculatedAmount;
            }else{
               $total -= $tax['amount'];
            }
        }
    }
}

    $total_currency = $details['currency'];
@endphp
<tr>
    <td align="left">Total</td>
    <td align="right">{{$total_currency}} <strong>{{ number_format($total, 2) }}</strong></td>
</tr>

        </table>
    </div>
</body>

</html>

