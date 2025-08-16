<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #000b43;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .header img {
            width: 175px;
        }
        .content {
            padding: 20px;
            color: #000b43;
        }
        .notification {
            background-color: #e0e7ff;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #1026b978;
        }
        .footer {
            background-color: #000b43;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 12px;
        }
        /* New table styles */
        .tank-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            background-color: #fff;
        }
        .tank-table th, .tank-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .tank-table th {
            background-color: #000b43;
            color: #fff;
            font-weight: bold;
        }
        .tank-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .tank-table tr:hover {
            background-color: #f0f4f8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://db.ecoc-site.com/panel/assets/img/brand/logo.png" class="ht-100 mb-0" alt="ECOC Logo">
        </div>
        <div class="content">
            <h3>Dear {{ $recipients['to'][key($recipients['to'])] ?? 'Admin' }},</h3>
            <p>Please find attached the daily tank status report for {{ now()->format('F d, Y') }}. This report includes the latest details of all tanks.</p>
            <p>Key details:</p>
            <table class="tank-table">
                <thead>
                    <tr>
                        <th>Tank ID</th>
                        <th>Status</th>
                        <th>Capacity Utilization</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tanks as $tank)
                        <tr>
                            <td>{{ $tank['id'] }}</td>
                            <td>{{ $tank['status'] }}</td>
                            <td>{{ $tank['capacityUtilization'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Download the attached Excel file for a comprehensive view of all tanks. Please review and take any necessary actions.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>