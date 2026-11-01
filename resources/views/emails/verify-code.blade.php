<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NACOMES Email Verification Code</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 48px 16px;
        }
        .container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }
        .header {
            background-color: #0b132b;
            padding: 36px 32px 32px;
            text-align: center;
        }
        .badge {
            display: inline-block;
            background-color: #1e293b;
            color: #facc15;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 6px 14px;
            border-radius: 9999px;
            border: 1px solid rgba(250, 204, 21, 0.3);
            margin-bottom: 12px;
        }
        .header-title {
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .header-sub {
            color: #94a3b8;
            font-size: 13px;
            margin-top: 6px;
            margin-bottom: 0;
        }
        .body-content {
            padding: 40px 36px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0b132b;
            margin-top: 0;
            margin-bottom: 14px;
        }
        .paragraph {
            font-size: 14px;
            line-height: 1.65;
            color: #475569;
            margin: 0 0 24px;
        }
        .code-box-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .code-box {
            display: inline-block;
            background-color: #0b132b;
            color: #facc15;
            font-family: 'Courier New', Courier, monospace, monospace;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 10px;
            padding: 20px 36px 20px 46px;
            border-radius: 12px;
            border: 2px solid #facc15;
            box-shadow: 0 2px 8px rgba(11, 19, 43, 0.15);
        }
        .timer-notice {
            font-size: 12px;
            color: #64748b;
            margin-top: 14px;
            margin-bottom: 0;
            text-align: center;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin: 28px 0;
        }
        .info-card p {
            margin: 0;
            font-size: 12px;
            line-height: 1.6;
            color: #64748b;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px 32px;
            text-align: center;
        }
        .footer-text {
            font-size: 11px;
            line-height: 1.6;
            color: #94a3b8;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <span class="badge">Electoral Commission</span>
                <h1 class="header-title">NACOMES E-Voting Portal</h1>
                <p class="header-sub">Department of Computer Engineering</p>
            </div>

            <!-- Body -->
            <div class="body-content">
                <p class="greeting">Hello, {{ $userName }}</p>
                
                <p class="paragraph">
                    Thank you for registering on the NACOMES Electoral Portal. To complete your voter accreditation and verify your institutional student email, enter the 6-digit verification code below:
                </p>

                <!-- 6-Digit Code Container -->
                <div class="code-box-wrapper">
                    <div class="code-box">{{ $code }}</div>
                    <p class="timer-notice">
                        Expires in <strong>30 minutes</strong> &bull; One-time use only
                    </p>
                </div>

                <div class="info-card">
                    <p>
                        <strong>Security Reminder:</strong> Never share this code with anyone. Official NACOMES electoral representatives will never ask you for your verification code or ballot choices.
                    </p>
                </div>

                <p class="paragraph" style="margin-bottom: 0; font-size: 13px; color: #94a3b8;">
                    If you did not initiate this request or register on the portal, no further action is required and you may safely disregard this message.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p class="footer-text">
                    &copy; {{ date('Y') }} NACOMES Electoral Commission &bull; All Rights Reserved.<br>
                    Autonomous Student Governance Portal &bull; Department of Computer Engineering
                </p>
            </div>
        </div>
    </div>
</body>
</html>
