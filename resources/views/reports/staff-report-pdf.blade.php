<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Staff Report - {{ $report->staff_name }}</title>
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
            width: fit-content;
            height: 29.7cm;
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            background: url('http://api.rockgardenehr.space/images/dimension.png');
        }

        h2 {
            color: #5D6975;
            font-size: 1.4em;
            margin: 20px 0 10px 0;
            border-bottom: 1px solid #C1CED9;
            padding-bottom: 5px;
        }

        #project {
            float: left;
        }

        #project span {
            color: #5D6975;
            text-align: right;
            width: 100px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: right;
            text-align: right;
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
            padding: 8px;
        }

        table th {
            padding: 5px 10px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: bold;
            background: #E8E8E8;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table td {
            text-align: center;
            vertical-align: top;
        }

        table td.service,
        table td.desc,
        table td.left {
            text-align: left;
        }

        table td.right {
            text-align: right;
        }

        .summary-box {
            background: #F5F5F5;
            border: 1px solid #C1CED9;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .metric-box {
            display: inline-block;
            background: #E8F4FD;
            border: 1px solid #B8D4E8;
            padding: 10px;
            margin: 5px;
            border-radius: 3px;
            text-align: center;
            width: 120px;
        }

        .metric-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #2C5282;
        }

        .metric-label {
            font-size: 0.8em;
            color: #5D6975;
        }

        .section {
            margin: 30px 0;
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

        .page-break {
            page-break-before: always;
        }

        .no-data {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>

<body>
    <header class="clearfix">
        <div id="logo">
            <img src="https://rockgardenehr.space/images/image-6.png" alt="logo">
        </div>
        <h1>Staff Performance Report</h1>
        <div>
            <div id="company" class="clearfix" style="margin-right: 1rem;">
                <div>Rockgarden Homecare Agency</div>
                <div>191 Okeletu-Ijede Road, Elemu Bus-Stop, Ikorodu,<br /> Lagos State, Nigeria.</div>
                <div>+234 816 717 6778</div>
                <div><a href="mailto:info@rockgardenhomecareagency.com">info@rockgardenhomecareagency.com</a></div>
            </div>
            <div id="project">
                <div><span>STAFF NAME</span> {{ $report->staff_name }}</div>
                <div><span>EMPLOYEE NO</span> {{ $report->employee_no }}</div>
                <div><span>DEPARTMENT</span> {{ $report->department }}</div>
                <div><span>DESIGNATION</span> {{ $report->designation }}</div>
                <div><span>REPORT PERIOD</span> {{ $period }}</div>
                <div><span>GENERATED ON</span> {{ $generated_date }}</div>
            </div>
        </div>
    </header>

    <main>
        <!-- Performance Summary -->
        <div class="summary-box">
            <h2 style="margin-top: 0;">Performance Overview</h2>
            <div style="text-align: center;">
                <div class="metric-box">
                    <div class="metric-value">{{ $report->attendance_percentage }}%</div>
                    <div class="metric-label">Attendance Rate</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">{{ $report->total_attendance_days }}</div>
                    <div class="metric-label">Days Present</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">{{ $report->total_incidents_reported }}</div>
                    <div class="metric-label">Incidents Reported</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">{{ $report->total_staff_charts_created }}</div>
                    <div class="metric-label">Charts Created</div>
                </div>
                @if($report->average_rating)
                <div class="metric-box">
                    <div class="metric-value">{{ $report->average_rating }}/5</div>
                    <div class="metric-label">Average Rating</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Summary Notes -->
        @if($report->summary_notes)
        <div class="section">
            <h2>Executive Summary</h2>
            <p>{{ $report->summary_notes }}</p>
        </div>
        @endif

        <!-- Attendance Details -->
        <div class="section">
            <h2>Attendance Record</h2>
            @if($report->attendance_details && count($report->attendance_details) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->attendance_details as $attendance)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($attendance['date'])->format('M d, Y') }}</td>
                        <td>{{ $attendance['checkin_time'] ? \Carbon\Carbon::parse($attendance['checkin_time'])->format('H:i A') : 'N/A' }}</td>
                        <td>{{ $attendance['checkout_time'] ? \Carbon\Carbon::parse($attendance['checkout_time'])->format('H:i A') : 'N/A' }}</td>
                        <td>{{ $attendance['category'] ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-data">No attendance records found for this period</div>
            @endif
        </div>

        <!-- Incidents Section -->
        @if($report->incident_details && count($report->incident_details) > 0)
        <div class="section">
            <h2>Incident Reports</h2>
            <table>
                <thead>
                    <tr>
                        <th class="service">Title</th>
                        <th class="service">Client</th>
                        <th>Report Date</th>
                        <th class="service">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->incident_details as $incident)
                    <tr>
                        <td class="service">{{ $incident['title'] }}</td>
                        <td class="service">{{ $incident['client_name'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($incident['report_date'])->format('M d, Y') }}</td>
                        <td class="service">{{ Str::limit($incident['description'], 100) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Staff Charts Section -->
        @if($report->staff_chart_details && isset($report->staff_chart_details['by_type']) && count($report->staff_chart_details['by_type']) > 0)
        <div class="section">
            <h2>Staff Chart Activities</h2>

            <!-- Charts by Type -->
            <h3 style="color: #5D6975; font-size: 1.1em;">Charts by Type</h3>
            <table>
                <thead>
                    <tr>
                        <th class="service">Chart Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->staff_chart_details['by_type'] as $typeData)
                    <tr>
                        <td class="service">{{ $typeData['type'] }}</td>
                        <td>{{ $typeData['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Charts by Client -->
            @if(isset($report->staff_chart_details['total_by_client']) && count($report->staff_chart_details['total_by_client']) > 0)
            <h3 style="color: #5D6975; font-size: 1.1em;">Charts by Client</h3>
            <table>
                <thead>
                    <tr>
                        <th class="service">Client Name</th>
                        <th>Total Charts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->staff_chart_details['total_by_client'] as $clientData)
                    <tr>
                        <td class="service">{{ $clientData['client_name'] }}</td>
                        <td>{{ $clientData['total_charts'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
        @endif

        <!-- Payrun Details Section -->
        @if($report->payrun_details && count($report->payrun_details) > 0)
        <div class="section page-break">
            <h2>Payroll Information</h2>
            <table>
                <thead>
                    <tr>
                        <th class="service">Title</th>
                        <th>Period</th>
                        <th>Basic Salary</th>
                        <th>Days Present</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Currency</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->payrun_details as $payrun)
                    <tr>
                        <td class="service">{{ $payrun['title'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($payrun['from_date'])->format('M d') }} - {{ \Carbon\Carbon::parse($payrun['to_date'])->format('M d, Y') }}</td>
                        <td class="right">{{ number_format($payrun['basic_salary'], 2) }}</td>
                        <td>{{ $payrun['days_present'] }}</td>
                        <td class="right">{{ number_format($payrun['allowances'] ?? 0, 2) }}</td>
                        <td class="right">{{ number_format($payrun['deductions'] ?? 0, 2) }}</td>
                        <td>{{ $payrun['currency'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Ratings Section -->
        @if($report->total_ratings_received > 0)
        <div class="section">
            <h2>Performance Ratings</h2>
            <div style="margin-bottom: 15px;">
                <strong>Total Ratings Received:</strong> {{ $report->total_ratings_received }} |
                <strong>Average Rating:</strong> {{ $report->average_rating }}/5.0
            </div>

            @php
                $ratingsData = json_decode(json_encode($report), true);
                $reviews = $ratingsData['ratings_data']['reviews'] ?? [];
            @endphp

            @if(!empty($reviews))
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Rating</th>
                        <th class="service">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $review)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($review['created_at'])->format('M d, Y') }}</td>
                        <td>{{ $review['rating'] }}/5</td>
                        <td class="service">{{ $review['comment'] ?? 'No comment provided' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
        @endif

        <!-- Performance Metrics Summary -->
        <div class="section">
            <h2>Key Performance Indicators</h2>
            <table>
                <thead>
                    <tr>
                        <th class="service">Metric</th>
                        <th>Value</th>
                        <th class="service">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="service">Attendance Rate</td>
                        <td>{{ $report->attendance_percentage }}%</td>
                        <td class="service">
                            @if($report->attendance_percentage >= 95)
                                Excellent
                            @elseif($report->attendance_percentage >= 80)
                                Good
                            @else
                                Needs Improvement
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="service">Total Working Days</td>
                        <td>{{ $report->total_working_days }}</td>
                        <td class="service">{{ $report->total_attendance_days }} days attended</td>
                    </tr>
                    <tr>
                        <td class="service">Incident Reports</td>
                        <td>{{ $report->total_incidents_reported }}</td>
                        <td class="service">
                            @if($report->total_incidents_reported == 0)
                                No incidents
                            @else
                                {{ $report->total_incidents_reported }} reported
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="service">Staff Charts Created</td>
                        <td>{{ $report->total_staff_charts_created }}</td>
                        <td class="service">Documentation activity</td>
                    </tr>
                    @if($report->average_rating)
                    <tr>
                        <td class="service">Performance Rating</td>
                        <td>{{ $report->average_rating }}/5.0</td>
                        <td class="service">
                            @if($report->average_rating >= 4.5)
                                Outstanding
                            @elseif($report->average_rating >= 3.5)
                                Good
                            @else
                                Needs Improvement
                            @endif
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </main>

    <footer>
        Staff Report generated on {{ $generated_date }} - Rockgarden Homecare Agency
    </footer>
</body>

</html>
