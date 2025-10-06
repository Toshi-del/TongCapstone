<?php

// Read the current .env file
$envFile = __DIR__ . '/.env';
$envContent = file_get_contents($envFile);

// Check if GMAIL_APP_PASSWORD already exists
if (strpos($envContent, 'GMAIL_APP_PASSWORD') === false) {
    // Add the GMAIL_APP_PASSWORD line after the mail configuration
    $envContent .= "\nGMAIL_APP_PASSWORD=dpecjegirgrxizqd\n";
    
    // Write back to .env file
    file_put_contents($envFile, $envContent);
    echo "✅ Added GMAIL_APP_PASSWORD to .env file\n";
} else {
    echo "ℹ️  GMAIL_APP_PASSWORD already exists in .env file\n";
}

echo "Done!\n";
