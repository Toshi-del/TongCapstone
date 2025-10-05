<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Invitation - RSS Citi Health Services</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .header .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .greeting {
            font-size: 18px;
            color: #2c5aa0;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
            color: #555555;
        }
        
        .success-badge {
            background-color: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .cta-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }
        
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #2c5aa0;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .info-box h3 {
            color: #2c5aa0;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .info-box p {
            font-size: 14px;
            color: #666666;
            margin: 0;
        }
        
        .footer {
            background-color: #f8fafc;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer .signature {
            font-size: 16px;
            color: #374151;
            margin-bottom: 15px;
        }
        
        .footer .company-info {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
        }
        
        .footer .company-name {
            font-weight: 600;
            color: #2c5aa0;
        }
        
        /* Mobile responsiveness */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                box-shadow: none;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .cta-button {
                padding: 14px 28px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Welcome to RSS Citi Health Services!</h1>
            <div class="subtitle">Your Health, Our Priority</div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">Dear {{ $name }},</div>
            
            <div class="success-badge">✓ Medical Examination Passed</div>
            
            <div class="message">
                Congratulations! We're pleased to inform you that you have successfully passed your pre-employment medical examination. This is an important milestone in your journey with us.
            </div>
            
            <div class="info-box">
                <h3>Next Step: Complete Your Registration</h3>
                <p>To finalize your onboarding process, please complete your account registration by clicking the button below. This will give you access to your patient portal and health records.</p>
            </div>
            
            <div class="cta-container">
                <a href="{{ route('register', ['email' => $email, 'type' => 'patient', 'record_id' => $record_id]) }}" class="cta-button">
                    Complete Registration →
                </a>
            </div>
            
            <div class="message">
                If you have any questions or need assistance with the registration process, please don't hesitate to contact our support team.
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="signature">
                Best regards,<br>
                <span class="company-name">{{ config('app.name') }} Team</span>
            </div>
            <div class="company-info">
                RSS Citi Health Services<br>
                Providing Quality Healthcare Solutions<br>
                <em>This is an automated message. Please do not reply to this email.</em>
            </div>
        </div>
    </div>
</body>
</html>