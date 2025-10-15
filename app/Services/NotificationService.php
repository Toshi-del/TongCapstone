<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\AnnualPhysicalExamination;
use App\Models\PreEmploymentExamination;
use App\Models\OpdExamination;

class NotificationService
{
    /**
     * Notify doctor when a new examination is ready for review
     */
    public static function notifyDoctorNewExamination($examination, $examinationType, $patientName)
    {
        $doctors = User::where('role', 'doctor')->get();
        
        $typeMessages = [
            'annual_physical' => 'New Annual Physical Examination',
            'pre_employment' => 'New Pre-Employment Examination',
            'opd' => 'New OPD Examination'
        ];
        
        $title = $typeMessages[$examinationType] ?? 'New Examination';
        $message = "A new {$examinationType} examination for {$patientName} is ready for your review.";
        
        foreach ($doctors as $doctor) {
            Notification::createForUser(
                $doctor,
                'new_examination',
                $title,
                $message,
                [
                    'examination_id' => $examination->id,
                    'examination_type' => $examinationType,
                    'patient_name' => $patientName,
                    'url' => self::getExaminationUrl($examination, $examinationType)
                ],
                'high',
                null,
                $examination
            );
        }
    }

    /**
     * Notify doctor when checklist is submitted
     */
    public static function notifyDoctorChecklistSubmitted($examination, $examinationType, $patientName, $submittedBy)
    {
        // Get all doctors except the one who submitted
        $doctors = User::where('role', 'doctor')
            ->where('id', '!=', $submittedBy->id)
            ->get();
        
        $title = 'Medical Checklist Submitted';
        $message = "Medical checklist for {$patientName} ({$examinationType}) has been submitted by {$submittedBy->fname} {$submittedBy->lname}.";
        
        foreach ($doctors as $doctor) {
            Notification::createForUser(
                $doctor,
                'checklist_submitted',
                $title,
                $message,
                [
                    'examination_id' => $examination->id,
                    'examination_type' => $examinationType,
                    'patient_name' => $patientName,
                    'submitted_by' => $submittedBy->fname . ' ' . $submittedBy->lname,
                    'url' => self::getExaminationUrl($examination, $examinationType)
                ],
                'medium',
                $submittedBy,
                $examination
            );
        }
        
        // Also create a confirmation notification for the submitter
        Notification::createForUser(
            $submittedBy,
            'checklist_submitted_confirmation',
            'Checklist Submitted Successfully',
            "You have successfully submitted the medical checklist for {$patientName} ({$examinationType}).",
            [
                'examination_id' => $examination->id,
                'examination_type' => $examinationType,
                'patient_name' => $patientName,
                'url' => self::getExaminationUrl($examination, $examinationType)
            ],
            'low',
            $submittedBy,
            $examination
        );
    }

    /**
     * Notify doctor when examination results are updated
     */
    public static function notifyDoctorResultsUpdated($examination, $examinationType, $patientName, $updatedBy)
    {
        // Get all doctors except the one who updated
        $doctors = User::where('role', 'doctor')
            ->where('id', '!=', $updatedBy->id)
            ->get();
        
        $title = 'Examination Results Updated';
        $message = "Results for {$patientName} ({$examinationType}) have been updated by {$updatedBy->fname} {$updatedBy->lname}.";
        
        foreach ($doctors as $doctor) {
            Notification::createForUser(
                $doctor,
                'results_updated',
                $title,
                $message,
                [
                    'examination_id' => $examination->id,
                    'examination_type' => $examinationType,
                    'patient_name' => $patientName,
                    'updated_by' => $updatedBy->fname . ' ' . $updatedBy->lname,
                    'url' => self::getExaminationUrl($examination, $examinationType)
                ],
                'medium',
                $updatedBy,
                $examination
            );
        }
        
        // Also create a confirmation notification for the updater
        Notification::createForUser(
            $updatedBy,
            'results_updated_confirmation',
            'Results Updated Successfully',
            "You have successfully updated the examination results for {$patientName} ({$examinationType}).",
            [
                'examination_id' => $examination->id,
                'examination_type' => $examinationType,
                'patient_name' => $patientName,
                'url' => self::getExaminationUrl($examination, $examinationType)
            ],
            'low',
            $updatedBy,
            $examination
        );
    }

    /**
     * Notify doctor when examination is successfully submitted to admin
     */
    public static function notifyDoctorSubmittedToAdmin($examination, $examinationType, $patientName, $doctor)
    {
        $title = 'Examination Submitted to Admin';
        $message = "You have successfully submitted the {$examinationType} examination for {$patientName} to the admin.";
        
        Notification::createForUser(
            $doctor,
            'submitted_to_admin',
            $title,
            $message,
            [
                'examination_id' => $examination->id,
                'examination_type' => $examinationType,
                'patient_name' => $patientName,
                'url' => self::getExaminationUrl($examination, $examinationType)
            ],
            'low',
            $doctor,
            $examination
        );
    }

    /**
     * Notify company when appointment is submitted successfully
     */
    public static function notifyCompanyAppointmentSubmitted($appointment, $company)
    {
        $title = 'Appointment Submitted Successfully';
        $message = "Your appointment for {$appointment->patient_name} has been submitted successfully. Appointment Date: " . \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y');
        
        Notification::createForUser(
            $company,
            'appointment_submitted',
            $title,
            $message,
            [
                'appointment_id' => $appointment->id,
                'patient_name' => $appointment->patient_name,
                'appointment_date' => $appointment->appointment_date,
                'url' => route('company.appointments.index')
            ],
            'low',
            $company,
            $appointment
        );
    }

    /**
     * Notify company when pre-employment examination is submitted successfully
     */
    public static function notifyCompanyPreEmploymentSubmitted($preEmploymentRecord, $company)
    {
        $title = 'Pre-Employment Examination Submitted';
        $message = "Pre-employment examination for {$preEmploymentRecord->patient_name} has been submitted successfully and is now being processed.";
        
        Notification::createForUser(
            $company,
            'pre_employment_submitted',
            $title,
            $message,
            [
                'record_id' => $preEmploymentRecord->id,
                'patient_name' => $preEmploymentRecord->patient_name,
                'url' => route('company.pre-employment.index')
            ],
            'low',
            $company,
            $preEmploymentRecord
        );
    }

    /**
     * Notify company when OPD examination is submitted successfully
     */
    public static function notifyCompanyOpdSubmitted($opdExamination, $company)
    {
        $patientName = $opdExamination->patient->fname . ' ' . $opdExamination->patient->lname;
        $title = 'OPD Examination Submitted';
        $message = "OPD examination for {$patientName} has been submitted successfully and is now being processed.";
        
        Notification::createForUser(
            $company,
            'opd_submitted',
            $title,
            $message,
            [
                'examination_id' => $opdExamination->id,
                'patient_name' => $patientName,
                'url' => route('company.dashboard')
            ],
            'low',
            $company,
            $opdExamination
        );
    }

    /**
     * Notify company when medical results are ready/approved
     */
    public static function notifyCompanyResultsReady($examination, $examinationType, $patientName, $company)
    {
        $typeMessages = [
            'annual_physical' => 'Annual Physical Examination',
            'pre_employment' => 'Pre-Employment Examination',
            'opd' => 'OPD Examination'
        ];
        
        $examType = $typeMessages[$examinationType] ?? 'Medical Examination';
        $title = 'Medical Results Ready';
        $message = "Medical results for {$patientName} ({$examType}) are now ready and approved. You can view the results in your dashboard.";
        
        Notification::createForUser(
            $company,
            'results_ready',
            $title,
            $message,
            [
                'examination_id' => $examination->id,
                'examination_type' => $examinationType,
                'patient_name' => $patientName,
                'url' => self::getCompanyExaminationUrl($examinationType)
            ],
            'high',
            null,
            $examination
        );
    }

    /**
     * Notify company when examination status is updated to sent_to_company
     */
    public static function notifyCompanyExaminationSent($examination, $examinationType, $patientName, $company)
    {
        $typeMessages = [
            'annual_physical' => 'Annual Physical Examination',
            'pre_employment' => 'Pre-Employment Examination',
            'opd' => 'OPD Examination'
        ];
        
        $examType = $typeMessages[$examinationType] ?? 'Medical Examination';
        $title = 'Examination Results Sent';
        $message = "The {$examType} results for {$patientName} have been sent to your company and are now available for review.";
        
        Notification::createForUser(
            $company,
            'examination_sent',
            $title,
            $message,
            [
                'examination_id' => $examination->id,
                'examination_type' => $examinationType,
                'patient_name' => $patientName,
                'url' => self::getCompanyExaminationUrl($examinationType)
            ],
            'high',
            null,
            $examination
        );
    }

    /**
     * Get examination URL based on type (for doctors)
     */
    private static function getExaminationUrl($examination, $examinationType)
    {
        switch ($examinationType) {
            case 'annual_physical':
                return route('doctor.annual-physical.edit', $examination->id);
            case 'pre_employment':
                return route('doctor.pre-employment.edit', $examination->id);
            case 'opd':
                return route('doctor.opd.examination.show', $examination->id);
            default:
                return route('doctor.dashboard');
        }
    }

    /**
     * Get examination URL based on type (for company)
     */
    private static function getCompanyExaminationUrl($examinationType)
    {
        switch ($examinationType) {
            case 'annual_physical':
            case 'pre_employment':
                return route('company.medical-results');
            case 'opd':
                return route('company.dashboard');
            default:
                return route('company.dashboard');
        }
    }
}
