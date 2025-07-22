<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Legal Organization Legal Department</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 50%, #2563eb 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1e3a2e;
            margin-bottom: 20px;
        }
        
        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 30px;
        }
        
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #3ca44c, #2563eb);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.3s ease;
        }
        
        .reset-button:hover {
            transform: translateY(-2px);
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .security-notice {
            background-color: #f8f9fa;
            border-left: 4px solid #3ca44c;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .security-notice h3 {
            margin: 0 0 10px;
            color: #1e3a2e;
            font-size: 16px;
        }
        
        .security-notice p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        
        .footer .company-info {
            font-weight: 600;
            color: #1e3a2e;
        }
        
        .link-text {
            word-break: break-all;
            font-size: 14px;
            color: #666;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 25px 15px;
            }
            
            .reset-button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/company-logo.png') }}" alt="Legal Organization Logo">
            </div>
            <h1>Password Reset Request</h1>
            <p>Legal Organization Legal Department</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">Hello {{ $user->name }},</div>
            
            <div class="message">
                <p>We received a request to reset your password for your Legal Case Management System account. If you made this request, please click the button below to reset your password:</p>
            </div>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">Reset Your Password</a>
            </div>
            
            <div class="message">
                <p>If the button above doesn't work, you can copy and paste the following link into your browser:</p>
                <div class="link-text">{{ $resetUrl }}</div>
            </div>
            
            <div class="security-notice">
                <h3>🔒 Security Notice</h3>
                <p><strong>This link will expire in 60 minutes</strong> for your security. If you didn't request a password reset, please ignore this email or contact your system administrator if you have concerns.</p>
            </div>
            
            <div class="message">
                <p>For security reasons, this password reset link can only be used once. If you need to reset your password again, please submit a new request.</p>
                
                <p>If you have any questions or need assistance, please contact the Legal Department IT support.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="company-info">Legal Organization Legal Department</p>
            <p>Legal Case Management System</p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                © {{ date('Y') }} Legal Organization. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>






