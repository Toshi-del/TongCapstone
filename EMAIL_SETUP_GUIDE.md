# PHPMailer Setup Guide for RSS Citi Health Services

## Gmail Account Configuration

### Step 1: Enable 2-Factor Authentication
1. Go to your Google Account settings: https://myaccount.google.com/
2. Navigate to "Security" → "2-Step Verification"
3. Follow the steps to enable 2FA if not already enabled

### Step 2: Generate App Password
1. In Google Account settings, go to "Security" → "App passwords"
2. Select "Mail" as the app and "Other" as the device
3. Enter "RSS Citi Health Services" as the device name
4. Copy the generated 16-character app password

### Step 3: Update Environment Configuration
Add the following to your `.env` file:

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=rsscitihealthservices@gmail.com
MAIL_PASSWORD=your_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=rsscitihealthservices@gmail.com
MAIL_FROM_NAME="RSS Citi Health Services"

# Gmail App Password (16 characters, no spaces)
GMAIL_APP_PASSWORD=your_16_character_app_password_here
```

## Testing the Configuration

### Method 1: Using Test File
Run the test file from the project root:
```bash
php test_email_setup.php
```

### Method 2: Using Laravel Tinker
```bash
php artisan tinker
```

Then run:
```php
use App\Helpers\MailHelper;

// Test basic configuration
$result = MailHelper::testEmailConfiguration();
print_r($result);

// Test appointment confirmation
$appointmentData = [
    'date' => '2025-01-15',
    'time' => '10:00 AM',
    'type' => 'Annual Physical Examination'
];

MailHelper::sendAppointmentConfirmation(
    'test@example.com',
    'Test Patient',
    $appointmentData
);
```

## Usage Examples

### 1. Send Appointment Confirmation
```php
use App\Helpers\MailHelper;

$appointmentData = [
    'date' => '2025-01-15',
    'time' => '10:00 AM',
    'type' => 'Pre-Employment Medical Examination'
];

MailHelper::sendAppointmentConfirmation(
    'patient@email.com',
    'John Doe',
    $appointmentData
);
```

### 2. Send Medical Results Notification
```php
use App\Helpers\MailHelper;

$resultsData = [
    'type' => 'Annual Physical Examination',
    'date' => '2025-01-10'
];

MailHelper::sendMedicalResultsNotification(
    'patient@email.com',
    'Jane Smith',
    $resultsData
);
```

### 3. Send Registration Invitation
```php
use App\Helpers\MailHelper;

$invitationData = [
    'company' => 'ABC Corporation',
    'type' => 'Pre-Employment Medical Examination',
    'registration_link' => 'https://your-app.com/register/token123'
];

MailHelper::sendRegistrationInvitation(
    'employee@company.com',
    'New Employee',
    $invitationData
);
```

### 4. Send General Notification
```php
use App\Helpers\MailHelper;

MailHelper::sendNotification(
    'recipient@email.com',
    'Recipient Name',
    'Subject Line',
    '<h1>HTML Message</h1><p>Your message content here.</p>',
    true // isHTML
);
```

## Integration with Existing Controllers

### In PleboController (Blood Collection Notifications)
```php
use App\Helpers\MailHelper;

// After blood collection is completed
if (!empty($data['blood_extraction_done_by'])) {
    // Notify admin
    MailHelper::sendNotification(
        'admin@rsscitihealthservices.com',
        'Administrator',
        'Blood Collection Completed',
        "Blood collection completed for patient: {$patientName} by {$phlebotomistName}"
    );
}
```

### In PathologistController (Results Ready)
```php
use App\Helpers\MailHelper;

// After pathologist completes lab results
$resultsData = [
    'type' => 'Annual Physical Examination',
    'date' => date('Y-m-d')
];

MailHelper::sendMedicalResultsNotification(
    $patient->email,
    $patient->full_name,
    $resultsData
);
```

## Troubleshooting

### Common Issues:

1. **Authentication Failed**
   - Ensure 2FA is enabled on Gmail account
   - Use App Password, not regular password
   - Check that GMAIL_APP_PASSWORD is set correctly in .env

2. **Connection Timeout**
   - Check internet connection
   - Verify firewall settings allow SMTP on port 587
   - Try using port 465 with SSL instead of TLS

3. **Email Not Received**
   - Check spam/junk folder
   - Verify recipient email address
   - Check Gmail account sending limits

### Alternative Configuration (Port 465/SSL):
If port 587 doesn't work, try:
```env
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

And update MailHelper.php:
```php
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = 465;
```

## Security Notes

- Never commit the `.env` file to version control
- Use App Passwords instead of regular Gmail passwords
- Regularly rotate App Passwords
- Monitor email sending logs for suspicious activity
- Consider implementing rate limiting for email sending

## Email Templates

The MailHelper includes professional HTML email templates with:
- Responsive design
- RSS Citi Health Services branding
- Fallback text versions
- Proper styling and formatting

Templates are automatically used for:
- Appointment confirmations
- Medical results notifications
- Registration invitations
- General notifications
