<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Helpers\MailHelper;
use App\Models\PreEmploymentRecord;

echo "=== Testing Registration Email Functionality ===\n\n";

// Get a test record
$testRecord = PreEmploymentRecord::where('status', 'Approved')
    ->where('registration_link_sent', false)
    ->first();

if (!$testRecord) {
    echo "❌ No approved records found for testing\n";
    exit;
}

echo "📋 Test Record:\n";
echo "   ID: {$testRecord->id}\n";
echo "   Name: {$testRecord->first_name} {$testRecord->last_name}\n";
echo "   Email: {$testRecord->email}\n";
echo "   Company: {$testRecord->company_name}\n\n";

// Generate registration link
$registrationLink = route('register', ['email' => $testRecord->email, 'type' => 'patient', 'record_id' => $testRecord->id]);
echo "🔗 Registration Link: {$registrationLink}\n\n";

// Prepare invitation data
$invitationData = [
    'company' => $testRecord->company_name ?? 'N/A',
    'type' => 'Pre-Employment Medical Examination',
    'registration_link' => $registrationLink
];

echo "📧 Sending test registration email...\n";

try {
    $emailSent = MailHelper::sendRegistrationInvitation(
        $testRecord->email,
        $testRecord->full_name ?? ($testRecord->first_name . ' ' . $testRecord->last_name),
        $invitationData
    );

    if ($emailSent) {
        echo "✅ SUCCESS: Registration email sent successfully!\n";
        echo "📬 Check the email: {$testRecord->email}\n";
        
        // Mark as sent
        $testRecord->update(['registration_link_sent' => true]);
        echo "✅ Record marked as sent in database\n";
    } else {
        echo "❌ FAILED: Email sending failed\n";
        echo "💡 Check the Laravel logs for more details\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "💡 This is likely an email configuration issue\n";
}

echo "\n=== Test Complete ===\n";
