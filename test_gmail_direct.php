<?php

require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "=== Direct Gmail SMTP Test ===\n\n";

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'rsscitihealthservices@gmail.com';
    $mail->Password = 'dpecjegirgrxizqd'; // New App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output

    // Recipients
    $mail->setFrom('rsscitihealthservices@gmail.com', 'RSS Citi Health Services');
    $mail->addAddress('rsscitihealthservices@gmail.com', 'Test Recipient');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Direct Gmail Test - ' . date('Y-m-d H:i:s');
    $mail->Body = '<h2>Direct Gmail Test</h2><p>This is a direct test of Gmail SMTP without Laravel.</p><p>If you receive this, the Gmail configuration is working!</p>';
    $mail->AltBody = 'Direct Gmail Test - If you receive this, the Gmail configuration is working!';

    $mail->send();
    echo "\n✅ SUCCESS: Email sent successfully!\n";
    echo "📬 Check your Gmail inbox\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: {$mail->ErrorInfo}\n";
    echo "💡 Check your Gmail App Password and 2FA settings\n";
}

echo "\n=== Test Complete ===\n";
