<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Environment Variables Check ===\n\n";

$envVars = [
    'MAIL_MAILER',
    'MAIL_HOST', 
    'MAIL_PORT',
    'MAIL_USERNAME',
    'MAIL_PASSWORD',
    'MAIL_FROM_ADDRESS',
    'MAIL_FROM_NAME',
    'GMAIL_APP_PASSWORD'
];

foreach ($envVars as $var) {
    $value = env($var);
    if ($var === 'MAIL_PASSWORD' || $var === 'GMAIL_APP_PASSWORD') {
        // Mask password for security
        $maskedValue = $value ? substr($value, 0, 4) . '****' . substr($value, -4) : 'NOT SET';
        echo "{$var}: {$maskedValue}\n";
    } else {
        echo "{$var}: " . ($value ?: 'NOT SET') . "\n";
    }
}

echo "\n=== Check Complete ===\n";
