<?php

require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "=== Simple Gmail SMTP Test ===\n\n";

// Load environment variables manually
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
        }
    }
}

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'rsscitihealthservices@gmail.com';
    
    // Try different password formats
    $passwords = [
        $_ENV['GMAIL_APP_PASSWORD'] ?? '',
        $_ENV['MAIL_PASSWORD'] ?? '',
        'nnoukyoacklpswre',  // Without spaces
        'nnou koya cklp swre' // With spaces
    ];
    
    echo "Testing different password formats...\n";
    
    foreach ($passwords as $index => $password) {
        if (empty($password)) continue;
        
        echo "Testing password format " . ($index + 1) . ": " . substr($password, 0, 4) . "****" . substr($password, -4) . "\n";
        
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
        
        // Set sender and recipient
        $mail->setFrom('rsscitihealthservices@gmail.com', 'RSS Citi Health Services');
        $mail->addAddress('rsscitihealthservices@gmail.com');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email - ' . date('Y-m-d H:i:s');
        $mail->Body = '<h2>Test Email</h2><p>This is a test email to verify SMTP configuration.</p>';
        
        try {
            $mail->send();
            echo "✅ SUCCESS with password format " . ($index + 1) . "!\n";
            echo "Email sent successfully!\n\n";
            
            echo "Use this password in your .env file:\n";
            echo "MAIL_PASSWORD={$password}\n";
            echo "GMAIL_APP_PASSWORD={$password}\n";
            break;
            
        } catch (Exception $e) {
            echo "❌ Failed with password format " . ($index + 1) . ": " . $e->getMessage() . "\n\n";
            $mail->clearAddresses();
            continue;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Configuration error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nIf all formats failed, please:\n";
echo "1. Verify 2FA is enabled on rsscitihealthservices@gmail.com\n";
echo "2. Generate a new App Password at: https://myaccount.google.com/apppasswords\n";
echo "3. Use the new 16-character App Password (no spaces)\n";
