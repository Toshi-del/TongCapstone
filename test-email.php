<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test email configuration
    echo "Testing email configuration...\n";
    echo "MAIL_HOST: " . env('MAIL_HOST') . "\n";
    echo "MAIL_PORT: " . env('MAIL_PORT') . "\n";
    echo "MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";
    echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n\n";
    
    // Send test email
    Mail::raw('This is a test email to verify SMTP connection.', function (Message $message) {
        $message->to(env('MAIL_USERNAME')) // Send to same email for testing
                ->subject('SMTP Connection Test - ' . date('Y-m-d H:i:s'));
    });
    
    echo "✅ Email sent successfully! Check your inbox.\n";
    
} catch (Exception $e) {
    echo "❌ Email failed to send.\n";
    echo "Error: " . $e->getMessage() . "\n";
    
    // Additional debugging info
    if (strpos($e->getMessage(), 'Connection') !== false) {
        echo "\n🔍 Connection issue detected. Please check:\n";
        echo "- Internet connection\n";
        echo "- Gmail SMTP settings\n";
        echo "- App password validity\n";
    }
    
    if (strpos($e->getMessage(), 'Authentication') !== false) {
        echo "\n🔍 Authentication issue detected. Please check:\n";
        echo "- Email address is correct\n";
        echo "- App password is correct (not regular password)\n";
        echo "- 2-factor authentication is enabled on Gmail\n";
    }
}
