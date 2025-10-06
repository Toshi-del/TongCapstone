<?php

// Bootstrap Laravel application
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Helpers\MailHelper;

echo "=== PHPMailer Setup Test for RSS Citi Health Services ===\n\n";

// Test email configuration
echo "Testing email configuration...\n";
$result = MailHelper::testEmailConfiguration();

if ($result['success']) {
    echo "✅ SUCCESS: " . $result['message'] . "\n\n";
    
    // Test appointment confirmation
    echo "Testing appointment confirmation email...\n";
    $appointmentData = [
        'date' => '2025-01-15',
        'time' => '10:00 AM',
        'type' => 'Annual Physical Examination'
    ];
    
    $appointmentResult = MailHelper::sendAppointmentConfirmation(
        'rsscitihealthservices@gmail.com',
        'Test Patient',
        $appointmentData
    );
    
    if ($appointmentResult) {
        echo "✅ Appointment confirmation email sent successfully!\n";
    } else {
        echo "❌ Failed to send appointment confirmation email.\n";
    }
    
    // Test medical results notification
    echo "\nTesting medical results notification...\n";
    $resultsData = [
        'type' => 'Pre-Employment Medical Examination',
        'date' => date('Y-m-d')
    ];
    
    $resultsResult = MailHelper::sendMedicalResultsNotification(
        'rsscitihealthservices@gmail.com',
        'Test Patient',
        $resultsData
    );
    
    if ($resultsResult) {
        echo "✅ Medical results notification sent successfully!\n";
    } else {
        echo "❌ Failed to send medical results notification.\n";
    }
    
} else {
    echo "❌ ERROR: " . $result['message'] . "\n";
    echo "\nPlease check:\n";
    echo "1. Gmail App Password is set in .env file as GMAIL_APP_PASSWORD\n";
    echo "2. Internet connection is working\n";
    echo "3. Gmail account has 2FA enabled and App Password generated\n";
}

echo "\n=== Test Complete ===\n";
