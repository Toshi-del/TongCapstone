<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MedicalResultsNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $examination;
    public $examinationType;
    public $patientEmail;
    public $patientName;

    /**
     * Create a new message instance.
     */
    public function __construct($examination, $examinationType, $patientEmail, $patientName)
    {
        $this->examination = $examination;
        $this->examinationType = $examinationType; // 'pre_employment' or 'annual_physical'
        $this->patientEmail = $patientEmail;
        $this->patientName = $patientName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->examinationType === 'pre_employment' 
            ? 'Your Pre-Employment Medical Results - RSS Citi Health Services'
            : 'Your Annual Physical Medical Results - RSS Citi Health Services';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.medical-results-notification',
            with: [
                'examination' => $this->examination,
                'examinationType' => $this->examinationType,
                'patientEmail' => $this->patientEmail,
                'patientName' => $this->patientName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
