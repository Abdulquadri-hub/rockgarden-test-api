@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word; color:black; background-color:#FFFF;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear {{$details['empoyee_fullname']}} </strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>
        <p style="font-size: 14px; line-height: 160%;">
            Your payroll has been processed. Here is the summary for the month:
            <br/><br/>
            <strong style="text-align: left"> EMPLOYEE</strong>
        <div style="text-align: left">
            <span>* Name: {{$details['empoyee_fullname']}}</span>
            <br/>
            <span>* Department: {{$details['department_name']}}</span>
            <br/>
            <span>* Designation: {{$details['designation_name']}}</span>
            <br/>
            <span>* Duty Type: {{$details['duty_type']}}</span>
            <br/>
            <span>* Bank Name: {{$details['bank_name']}}</span>
            <br/>
            <span>* Bank Account No.: {{$details['account_no']}}</span>
            <br/>
            
        </div>
        <br/>
<strong style="text-align: left"> PAYRUN </strong>
<div style="text-align: left">
    <span>* Title:{{$details['payrun_title']}}</span>
    <br/>
    <span>* Date Range: <?php echo date('d M Y', strtotime($details['date_from'])); ?> - <?php echo date('d M Y', strtotime($details['date_to'])); ?></span>

    <br/>
    <span>* Basic Salary: {{$details['currency']}} {{ number_format($details['amount'], 2) }}</span>
</div>
<br/>
<strong style="text-align: left"> BONUSES </strong>
@if(!empty($details['bonuses']))
    <div style="text-align: left">
        @foreach($details['bonuses'] as $key => $bonus)
            <span>* {{$bonus['name']}} — {{$bonus['currency']}} {{ number_format($bonus['amount'], 2) }}</span>
            <br/>
        @endforeach
    </div>
    <br/>
@else
    <br/>
    <strong style="text-align: left"> None </strong> <br/><br/><br/>
@endif
<strong style="text-align: left"> ALLOWANCES </strong>
@if(!empty($details['allowances']))
    <div style="text-align: left">
        @foreach($details['allowances'] as $key => $allowance)
            <span>* {{$allowance['name']}} — {{$allowance['currency']}} {{ number_format($allowance['amount'], 2) }}</span>
            <br/>
        @endforeach
    </div>
    <br/>
@else
    <br/>
    <strong style="text-align: left"> None </strong> <br/><br/><br/>
@endif
<strong style="text-align: left"> DEDUCTIONS </strong>
@if(!empty($details['deductions']))
    <div style="text-align: left">
        @foreach($details['deductions'] as $key => $deduction)
            <span>* {{$deduction['name']}} — {{$deduction['currency']}} {{ number_format($deduction['amount'], 2) }}</span>
            <br/>
        @endforeach
    </div>
    <br/>
@else
    <br/>
    <strong style="text-align: left"> None </strong> <br/><br/><br/>
@endif

        
<strong style="text-align: left"> TAXES </strong>
@if(!empty($details['taxes']))
    <div style="text-align: left">
        @foreach($details['taxes'] as $key => $tax)
            @if(isset($tax['is_fixed']) && $tax['is_fixed'] == 0)
                @php
                    $formattedAmount = number_format($tax['amount'], 2);
                @endphp
                <span>* {{$tax['name']}} — {{$tax['currency']}} {{$formattedAmount}}</span>
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
                        $formattedCalculatedAmount = number_format($calculatedAmount, 2);
                        
                    @endphp
                    <span>* {{$tax['name']}} — {{$tax['currency']}} {{$formattedCalculatedAmount}} ({{$tax['percentage'] ?? 0}}%)</span>
                @endif
            @endif
            <br/>
        @endforeach
    </div>
    <br/>
@else
    <br/>
    <strong style="text-align: left"> None </strong> <br/><br/><br/>
@endif


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
 <strong style="text-align: left">  TOTAL   —  {{$details['currency']}} {{number_format($total,2)}}</strong>
       
        </p>
    </div>
@endsection


