{{-- resources/views/emails/client-summary-report.blade.php --}}
@extends('emails.mail')

@section('content')
    <div class="v-text-align" style="line-height: 160%; text-align: justify; word-wrap: break-word;">
        <p style="font-size: 14px; line-height: 160%;"><span style="font-size: 18px; line-height: 28.8px;"><strong>Dear {{$contact->name}},</strong></span></p>
        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>

        <p style="font-size: 14px; line-height: 160%;">
            We hope this message finds you well. Please find attached the comprehensive monthly summary report for
            <strong>{{$client->user->first_name ?? 'N/A'}} {{$client->user->last_name ?? 'N/A'}}</strong> for the month of <strong>{{$monthYear}}</strong>.
        </p>

        <p style="font-size: 14px; line-height: 160%;">
            This detailed report includes the following information about your loved one's care:
        </p>

        <div style="text-align: left; margin: 20px 0;">
            <span style="font-size: 14px; line-height: 160%;">✓ <strong>Current Medications</strong> - All prescribed medications and dosages</span><br/>
            <span style="font-size: 14px; line-height: 160%;">✓ <strong>Monthly Vital Signs</strong> - Weight assessments and health monitoring</span><br/>
            <span style="font-size: 14px; line-height: 160%;">✓ <strong>Medication Changes</strong> - Any adjustments made to medication regimen</span><br/>
            <span style="font-size: 14px; line-height: 160%;">✓ <strong>Laboratory Tests</strong> - Results from any medical tests conducted</span><br/>
            <span style="font-size: 14px; line-height: 160%;">✓ <strong>Medical Incidents</strong> - Any health-related incidents or concerns</span><br/>
            <span style="font-size: 14px; line-height: 160%;">✓ <strong>Hospital Visits</strong> - Details of any medical appointments or visits</span><br/>
            <span style="font-size: 14px; line-height: 160%;">✓ <strong>Nursing Evaluations</strong> - Professional assessments of care needs</span><br/>
            <span style="font-size: 14px; line-height: 160%;">✓ <strong>Doctor's Observations</strong> - Medical professional reviews and recommendations</span><br/>
        </div>

        <p style="font-size: 14px; line-height: 160%;">
            <strong>Client Information:</strong>
        </p>
        <div style="text-align: left; margin: 15px 0;">
            <span style="font-size: 14px; line-height: 160%;">* Client Number: <strong>{{$client->client_no ?? 'N/A'}}</strong></span><br/>
            <span style="font-size: 14px; line-height: 160%;">* Room Location: <strong>{{$client->room_location ?? 'N/A'}} {{$client->room_number ?? ''}}</strong></span><br/>
            <span style="font-size: 14px; line-height: 160%;">* Report Period: <strong>{{$monthYear}}</strong></span><br/>
            <span style="font-size: 14px; line-height: 160%;">* Primary GP: <strong>{{$client->gp ?? 'N/A'}}</strong></span><br/>
        </div>

        <p style="font-size: 14px; line-height: 160%;">
            This monthly report is part of our commitment to maintaining transparent and informed care.
            We believe that keeping you updated on your loved one's health and wellbeing is essential
            for providing the best possible care experience.
        </p>

        <p style="font-size: 14px; line-height: 160%;">
            If you have any questions or concerns regarding the information in this report, please don't
            hesitate to contact our care team. We are always available to discuss any aspect of your
            loved one's care and wellbeing.
        </p>

        <p style="font-size: 14px; line-height: 160%;">
            For immediate concerns or urgent matters, please contact us directly at our main number.
            For non-urgent inquiries, you may also reach out via email or through the Rockgarden EHR app.
        </p>

        <p style="font-size: 14px; line-height: 160%;">
            Thank you for your continued trust in our care services. We remain committed to providing
            the highest quality care for your loved one.
        </p>

        <p style="font-size: 14px; line-height: 160%;">&nbsp;</p>

        <p style="font-size: 14px; line-height: 160%;"><strong>Warm regards,</strong></p>
        <p style="font-size: 14px; line-height: 160%;"><strong>The Care Team</strong></p>
        <p style="font-size: 14px; line-height: 160%;"><strong>Rockgarden Care Services</strong></p>
    </div>
@endsection
