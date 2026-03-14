<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #8B4513;
        }
        .header h1 {
            color: #8B4513;
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px 20px;
            text-align: center;
        }
        .code {
            background: linear-gradient(135deg, #8B4513, #D2691E);
            color: white;
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 8px;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-family: monospace;
            display: inline-block;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PageTurner</h1>
        </div>
        
        <div class="content">
            <h2>Two-Factor Authentication Code</h2>
            
        
            
            <p>Please use the following verification code to complete your login:</p>
            
            <div class="code">
                {{ $otp }}
            </div>
            
            <p>This code will expire in 10 minutes.</p>
            
            <div class="warning">
                <strong>Security Notice:</strong> If you didn't request this code, please secure your account immediately.
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} PageTurner. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>