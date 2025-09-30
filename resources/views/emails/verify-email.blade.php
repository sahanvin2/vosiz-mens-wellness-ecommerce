<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Vosiz</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #0f0f0f 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #ffffff;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1a1a1a 0%, #262626 50%, #1a1a1a 100%);
            border-radius: 20px;
            border: 1px solid rgba(245, 158, 11, 0.2);
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }
        
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 40px 20px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" patternUnits="userSpaceOnUse" width="100" height="100"><circle cx="50" cy="50" r="1" fill="rgba(0,0,0,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }
        
        .logo {
            position: relative;
            z-index: 1;
        }
        
        .logo h1 {
            color: #000000;
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .logo p {
            color: #000000;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 3px;
            opacity: 0.8;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 700;
            color: #f59e0b;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message {
            font-size: 16px;
            color: #d1d5db;
            margin-bottom: 30px;
            text-align: center;
            line-height: 1.8;
        }
        
        .verification-btn {
            display: block;
            width: fit-content;
            margin: 30px auto;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #000000 !important;
            text-decoration: none;
            padding: 18px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .verification-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(245, 158, 11, 0.4);
            border-color: rgba(245, 158, 11, 0.5);
        }
        
        .security-note {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        
        .security-note h3 {
            color: #f59e0b;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .security-note p {
            color: #9ca3af;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .footer {
            background: rgba(0, 0, 0, 0.5);
            padding: 30px;
            text-align: center;
            border-top: 1px solid rgba(245, 158, 11, 0.1);
        }
        
        .footer p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #f59e0b;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .social-links a:hover {
            color: #ffffff;
        }
        
        .link-fallback {
            background: rgba(38, 38, 38, 0.8);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
        
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 16px;
            }
            
            .header {
                padding: 30px 15px;
            }
            
            .logo h1 {
                font-size: 28px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .greeting {
                font-size: 20px;
            }
            
            .message {
                font-size: 15px;
            }
            
            .verification-btn {
                padding: 16px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <h1>VOSIZ</h1>
                <p>Men's Wellness & Health</p>
            </div>
        </div>
        
        <div class="content">
            <div class="greeting">Welcome to Vosiz!</div>
            
            <div class="message">
                <p>Thank you for joining our premium men's wellness community. To complete your registration and access your account, please verify your email address by clicking the button below.</p>
            </div>
            
            <a href="{{ $verificationUrl }}" class="verification-btn">
                🔐 Verify Email Address
            </a>
            
            <div class="security-note">
                <h3>🛡️ Security First</h3>
                <p>This verification link will expire in 60 minutes for your security. If you didn't create an account with Vosiz, please ignore this email.</p>
            </div>
            
            <div class="message">
                <p>Once verified, you'll have access to:</p>
                <ul style="text-align: left; margin: 20px 0; padding-left: 20px; color: #d1d5db;">
                    <li style="margin: 8px 0;">Premium skincare products</li>
                    <li style="margin: 8px 0;">Exclusive wellness content</li>
                    <li style="margin: 8px 0;">Personalized recommendations</li>
                    <li style="margin: 8px 0;">Early access to new products</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <div class="link-fallback">{{ $verificationUrl }}</div>
            
            <div class="social-links">
                <a href="#">Privacy Policy</a> |
                <a href="#">Terms of Service</a> |
                <a href="#">Contact Support</a>
            </div>
            
            <p>&copy; {{ date('Y') }} Vosiz. All rights reserved.</p>
            <p style="color: #4b5563; font-size: 12px; margin-top: 10px;">
                This email was sent to {{ $user->email }} because you registered for a Vosiz account.
            </p>
        </div>
    </div>
</body>
</html>