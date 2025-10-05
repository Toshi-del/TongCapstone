<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medical Results Available</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px;
            background-color: #ffffff;
        }
        .header { 
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white; 
            padding: 30px 20px; 
            text-align: center; 
            border-radius: 8px 8px 0 0; 
        }
        .content { 
            background: #f9fafb; 
            padding: 30px 20px; 
            border-radius: 0 0 8px 8px; 
        }
        .button { 
            display: inline-block; 
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white; 
            padding: 15px 30px; 
            text-decoration: none; 
            border-radius: 8px; 
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            background: linear-gradient(135deg, #3730a3, #6b21a8);
        }
        .info-box {
            background: #e0f2fe;
            border-left: 4px solid #0288d1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            color: #6b7280; 
            font-size: 14px; 
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .results-summary {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-fit {
            background: #dcfce7;
            color: #166534;
        }
        .status-unfit {
            background: #fef2f2;
            color: #991b1b;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏥 RSS Citi Health Services</div>
            <h1>Your Medical Results Are Ready</h1>
            <p>{{ $examinationType === 'pre_employment' ? 'Pre-Employment Medical Examination' : 'Annual Physical Examination' }}</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $patientName }}</strong>,</p>
            
            <p>We are pleased to inform you that your medical examination results are now available for review.</p>
            
            <div class="results-summary">
                <h3>📋 Examination Summary</h3>
                <p><strong>Examination Type:</strong> {{ $examinationType === 'pre_employment' ? 'Pre-Employment Medical Examination' : 'Annual Physical Examination' }}</p>
                <p><strong>Examination ID:</strong> #{{ $examination->id }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($examination->created_at)->format('F d, Y') }}</p>
                
                @if($examinationType === 'pre_employment' && isset($examination->fitness_assessment))
                    <p><strong>Fitness Assessment:</strong> 
                        @if($examination->fitness_assessment === 'Fit to Work')
                            <span class="status-badge status-fit">✅ Fit to Work</span>
                        @elseif($examination->fitness_assessment === 'Not Fit to Work')
                            <span class="status-badge status-unfit">❌ Not Fit to Work</span>
                        @else
                            <span class="status-badge status-pending">⏳ {{ $examination->fitness_assessment }}</span>
                        @endif
                    </p>
                @endif
                
                @if($examinationType === 'pre_employment' && isset($examination->company_name))
                    <p><strong>Company:</strong> {{ $examination->company_name }}</p>
                @endif
            </div>
            
            <div class="info-box">
                <p><strong>📱 Access Your Results:</strong></p>
                <p>To view your complete medical results and create your patient account (if you haven't already), please click the button below:</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('register', ['email' => $patientEmail, 'type' => 'patient', 'examination_id' => $examination->id, 'examination_type' => $examinationType]) }}" class="button">
                    🔍 View My Medical Results
                </a>
            </div>
            
            <div class="info-box">
                <p><strong>🔒 Important Notes:</strong></p>
                <ul>
                    <li>This link will help you create a secure patient account if you don't have one</li>
                    <li>Your medical information is confidential and secure</li>
                    <li>You can access your results anytime after registration</li>
                    <li>If you have questions, please contact our support team</li>
                </ul>
            </div>
            
            <p>If you already have a patient account, you can log in directly at <a href="{{ route('login') }}">{{ route('login') }}</a> to view your results.</p>
            
            <p>Thank you for choosing RSS Citi Health Services for your medical needs.</p>
            
            <p>Best regards,<br>
            <strong>RSS Citi Health Services Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated message regarding your medical examination results.</p>
            <p>Please do not reply to this email. For support, contact us through our official channels.</p>
            <p>&copy; {{ date('Y') }} RSS Citi Health Services. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
