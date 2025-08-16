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
        .footer {
            background-color: #000b43;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 12px;
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
            <p>This is the monthly tank transaction report for {{ $previousMonth }}. The report includes detailed transactions for each tank with activity last month.</p>
            <p>Key Summary:</p>
            <table class="tank-table">
                <thead>
                    <tr>
                        <th>Tank ID</th>
                        <th>Total Load (mt)</th>
                        <th>Total Discharge (mt)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tanks as $tank)
                        <?php
                            $totalLoad = $tank['transactions']->filter(function ($t) {
                                return in_array($t['type'], ['loading', 'load (transfer)']) && \Carbon\Carbon::parse($t['date'])->between(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth());
                            })->sum(function ($t) {
                                return (float)str_replace(' mt', '', $t['quantity']);
                            });
                            $totalDischarge = $tank['transactions']->filter(function ($t) {
                                return in_array($t['type'], ['discharging', 'discharge (transfer)']) && \Carbon\Carbon::parse($t['date'])->between(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth());
                            })->sum(function ($t) {
                                return (float)str_replace(' mt', '', $t['quantity']);
                            });
                        ?>
                        <tr>
                            <td>{{ $tank['id'] }}</td>
                            <td>{{ number_format($totalLoad, 2) }}</td>
                            <td>{{ number_format($totalDischarge, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Download the attached Excel file for detailed transaction breakdowns per tank. Please review and take any necessary actions.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>