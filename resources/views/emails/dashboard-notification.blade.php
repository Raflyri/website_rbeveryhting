<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .header {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 20px;
        }

        pre {
            white-space: pre-wrap;
            word-break: break-word;
            background: #f9f9f9;
            padding: 16px;
            border-radius: 6px;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">📬 {{ config('app.name') }}</div>
        <pre>{{ $notificationBody }}</pre>
        <div class="footer">
            Sent via the RBeverything Dashboard &middot; {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>

</html>