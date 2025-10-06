<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    private static function createMailer()
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'rsscitihealthservices@gmail.com';
            $mail->Password   = env('GMAIL_APP_PASSWORD'); // Use App Password, not regular password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Set default sender
            $mail->setFrom('rsscitihealthservices@gmail.com', 'RSS Citi Health Services');

            // Enable verbose debug output (disable in production)
            if (env('APP_DEBUG', false)) {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            }

            return $mail;
        } catch (Exception $e) {
            Log::error('PHPMailer configuration error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send appointment confirmation email
     */
    public static function sendAppointmentConfirmation($recipientEmail, $recipientName, $appointmentData)
    {
        try {
            $mail = self::createMailer();

            // Recipients
            $mail->addAddress($recipientEmail, $recipientName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Appointment Confirmation - RSS Citi Health Services';
            
            $mail->Body = self::getAppointmentConfirmationTemplate($recipientName, $appointmentData);
            $mail->AltBody = self::getAppointmentConfirmationTextTemplate($recipientName, $appointmentData);

            $mail->send();
            Log::info("Appointment confirmation email sent to: {$recipientEmail}");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send appointment confirmation email to {$recipientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send medical results notification
     */
    public static function sendMedicalResultsNotification($recipientEmail, $recipientName, $resultsData)
    {
        try {
            $mail = self::createMailer();

            // Recipients
            $mail->addAddress($recipientEmail, $recipientName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Medical Examination Results Available - RSS Citi Health Services';
            
            $mail->Body = self::getMedicalResultsTemplate($recipientName, $resultsData);
            $mail->AltBody = self::getMedicalResultsTextTemplate($recipientName, $resultsData);

            $mail->send();
            Log::info("Medical results notification sent to: {$recipientEmail}");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send medical results notification to {$recipientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send registration invitation email
     */
    public static function sendRegistrationInvitation($recipientEmail, $recipientName, $invitationData)
    {
        try {
            $mail = self::createMailer();

            // Recipients
            $mail->addAddress($recipientEmail, $recipientName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Registration Invitation - RSS Citi Health Services';
            
            $mail->Body = self::getRegistrationInvitationTemplate($recipientName, $invitationData);
            $mail->AltBody = self::getRegistrationInvitationTextTemplate($recipientName, $invitationData);

            $mail->send();
            Log::info("Registration invitation sent to: {$recipientEmail}");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send registration invitation to {$recipientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send general notification email
     */
    public static function sendNotification($recipientEmail, $recipientName, $subject, $message, $isHTML = true)
    {
        try {
            $mail = self::createMailer();

            // Recipients
            $mail->addAddress($recipientEmail, $recipientName);

            // Content
            $mail->isHTML($isHTML);
            $mail->Subject = $subject;
            $mail->Body = $message;

            if ($isHTML) {
                $mail->AltBody = strip_tags($message);
            }

            $mail->send();
            Log::info("Notification email sent to: {$recipientEmail} with subject: {$subject}");
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send notification email to {$recipientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Test email configuration
     */
    public static function testEmailConfiguration($testEmail = null)
    {
        $testEmail = $testEmail ?: 'rsscitihealthservices@gmail.com';
        
        try {
            $mail = self::createMailer();
            $mail->addAddress($testEmail);
            $mail->isHTML(true);
            $mail->Subject = 'PHPMailer Test - RSS Citi Health Services';
            $mail->Body = '<h2>Email Configuration Test</h2><p>If you receive this email, PHPMailer is configured correctly!</p><p>Sent at: ' . date('Y-m-d H:i:s') . '</p>';
            $mail->AltBody = 'Email Configuration Test - If you receive this email, PHPMailer is configured correctly! Sent at: ' . date('Y-m-d H:i:s');

            $mail->send();
            Log::info("Test email sent successfully to: {$testEmail}");
            return ['success' => true, 'message' => 'Test email sent successfully!'];
        } catch (Exception $e) {
            Log::error("Test email failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Test email failed: ' . $e->getMessage()];
        }
    }

    // Email Templates
    private static function getAppointmentConfirmationTemplate($name, $data)
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #6366f1; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { padding: 20px; text-align: center; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>RSS Citi Health Services</h1>
                    <h2>Appointment Confirmation</h2>
                </div>
                <div class='content'>
                    <p>Dear {$name},</p>
                    <p>Your appointment has been confirmed with the following details:</p>
                    <ul>
                        <li><strong>Date:</strong> " . ($data['date'] ?? 'TBD') . "</li>
                        <li><strong>Time:</strong> " . ($data['time'] ?? 'TBD') . "</li>
                        <li><strong>Type:</strong> " . ($data['type'] ?? 'Medical Examination') . "</li>
                        <li><strong>Location:</strong> RSS Citi Health Services</li>
                    </ul>
                    <p>Please arrive 15 minutes before your scheduled appointment time.</p>
                    <p>If you need to reschedule or have any questions, please contact us.</p>
                </div>
                <div class='footer'>
                    <p>RSS Citi Health Services<br>
                    Email: rsscitihealthservices@gmail.com</p>
                </div>
            </div>
        </body>
        </html>";
    }

    private static function getAppointmentConfirmationTextTemplate($name, $data)
    {
        return "RSS Citi Health Services - Appointment Confirmation\n\n" .
               "Dear {$name},\n\n" .
               "Your appointment has been confirmed with the following details:\n" .
               "Date: " . ($data['date'] ?? 'TBD') . "\n" .
               "Time: " . ($data['time'] ?? 'TBD') . "\n" .
               "Type: " . ($data['type'] ?? 'Medical Examination') . "\n" .
               "Location: RSS Citi Health Services\n\n" .
               "Please arrive 15 minutes before your scheduled appointment time.\n\n" .
               "If you need to reschedule or have any questions, please contact us.\n\n" .
               "RSS Citi Health Services\n" .
               "Email: rsscitihealthservices@gmail.com";
    }

    private static function getMedicalResultsTemplate($name, $data)
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #10b981; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { padding: 20px; text-align: center; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>RSS Citi Health Services</h1>
                    <h2>Medical Results Available</h2>
                </div>
                <div class='content'>
                    <p>Dear {$name},</p>
                    <p>Your medical examination results are now available.</p>
                    <p><strong>Examination Type:</strong> " . ($data['type'] ?? 'Medical Examination') . "</p>
                    <p><strong>Date Completed:</strong> " . ($data['date'] ?? date('Y-m-d')) . "</p>
                    <p>Please log in to your patient portal to view your complete results, or contact our office to schedule a consultation to discuss your results.</p>
                    <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
                </div>
                <div class='footer'>
                    <p>RSS Citi Health Services<br>
                    Email: rsscitihealthservices@gmail.com</p>
                </div>
            </div>
        </body>
        </html>";
    }

    private static function getMedicalResultsTextTemplate($name, $data)
    {
        return "RSS Citi Health Services - Medical Results Available\n\n" .
               "Dear {$name},\n\n" .
               "Your medical examination results are now available.\n\n" .
               "Examination Type: " . ($data['type'] ?? 'Medical Examination') . "\n" .
               "Date Completed: " . ($data['date'] ?? date('Y-m-d')) . "\n\n" .
               "Please log in to your patient portal to view your complete results, or contact our office to schedule a consultation to discuss your results.\n\n" .
               "If you have any questions or concerns, please don't hesitate to contact us.\n\n" .
               "RSS Citi Health Services\n" .
               "Email: rsscitihealthservices@gmail.com";
    }

    private static function getRegistrationInvitationTemplate($name, $data)
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #8b5cf6; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { padding: 20px; text-align: center; color: #666; }
                .button { display: inline-block; padding: 12px 24px; background: #8b5cf6; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>RSS Citi Health Services</h1>
                    <h2>Registration Invitation</h2>
                </div>
                <div class='content'>
                    <p>Dear {$name},</p>
                    <p>You have been invited to register for medical services with RSS Citi Health Services.</p>
                    <p><strong>Company:</strong> " . ($data['company'] ?? 'N/A') . "</p>
                    <p><strong>Examination Type:</strong> " . ($data['type'] ?? 'Medical Examination') . "</p>
                    " . (isset($data['registration_link']) ? "<p><a href='{$data['registration_link']}' class='button'>Complete Registration</a></p>" : "") . "
                    <p>Please complete your registration as soon as possible to schedule your medical examination.</p>
                    <p>If you have any questions, please contact us.</p>
                </div>
                <div class='footer'>
                    <p>RSS Citi Health Services<br>
                    Email: rsscitihealthservices@gmail.com</p>
                </div>
            </div>
        </body>
        </html>";
    }

    private static function getRegistrationInvitationTextTemplate($name, $data)
    {
        return "RSS Citi Health Services - Registration Invitation\n\n" .
               "Dear {$name},\n\n" .
               "You have been invited to register for medical services with RSS Citi Health Services.\n\n" .
               "Company: " . ($data['company'] ?? 'N/A') . "\n" .
               "Examination Type: " . ($data['type'] ?? 'Medical Examination') . "\n\n" .
               (isset($data['registration_link']) ? "Registration Link: {$data['registration_link']}\n\n" : "") .
               "Please complete your registration as soon as possible to schedule your medical examination.\n\n" .
               "If you have any questions, please contact us.\n\n" .
               "RSS Citi Health Services\n" .
               "Email: rsscitihealthservices@gmail.com";
    }
}