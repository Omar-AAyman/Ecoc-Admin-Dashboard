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
            background-color: #f8d7da;
            /* Light danger background */
            padding: 15px;
            margin: 10px 0;
            border-left: 6px solid #dc3545;
            /* Danger border */
            border-radius: 5px;
            color: #721c24;
            /* Darker danger text */
            position: relative;
            font-size: 14px;
        }

        .notification.expired {
            background-color: #dc3545;
            /* Stronger red for expired */
            color: #fff;
            border-left-color: #fff;
        }

        .notification:before {
            content: "\26A0";
            /* Unicode warning symbol */
            font-size: 18px;
            color: #dc3545;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .notification.expired:before {
            color: #fff;
            /* White icon for expired */
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
            <h3>Dear Admin,</h3>
            <p>The following contracts are approaching expiration ({{ $alert_type }}). Immediate action is required:
            </p>
            @foreach ($notifications as $notification)
                <div class="notification {{ $notification['is_expired'] ? 'expired' : '' }}">
                    <p>Company: {{ $notification['company_name'] }}<br>
                        Tank Number: #{{ $notification['tank_id'] }} expires on {{ $notification['end_date'] }}<br>
                        @if ($notification['is_expired'])
                            Expired since {{ $notification['days'] }}
                            {{ $notification['days'] === 1 ? 'day' : 'days' }}.
                        @else
                            {{ $notification['days'] }} {{ $notification['days'] === 1 ? 'day' : 'days' }} remaining.
                        @endif
                    </p>
                </div>
            @endforeach
            <p>Please contact the respective company to renew or discuss options. Thank you!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
